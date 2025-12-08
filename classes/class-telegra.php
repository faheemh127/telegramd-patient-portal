<?php

/**
 * HLD_Telegra: external API integration (create patient at TelegraMD).
 * create_patient() checks local DB first via HLD_Patient, returns existing Telegra ID if present,
 * otherwise creates via remote API, saves it locally, and returns the new Telegra ID.
 */
class HLD_Telegra
{
    public function get_medicine_code($medicine)
    {
        // Mapping of medicines to codes
        $codes = [
            "Semaglutide" => "pvt::fbfa6d41-773e-4724-b184-742c43d302d0",
            "Tirzepatide"   => "pvt::b04cabe5-2acc-4b8c-aacd-eea3a48b65bb",
        ];

        // Return code if exists, otherwise null
        return $codes[$medicine] ?? null;
    }




    public function get_order($order_id, $info = "")
    {
        if (empty($order_id)) {
            error_log('OrderID should not be empty to fetch order from telegra');
            return new WP_Error('order_id_invalid', 'Invalid order id.');
        }


        if (strpos($order_id, 'order::') !== 0) {
            error_log("Invalid order_id: {$order_id}. Unable to fetch error from Telegra.");
            return null;
        }



        $bearer_token = 'Bearer ' . TELEGRAMD_BEARER_TOKEN;
        $endpoint     = TELEGRA_BASE_URL . '/orders/' . urlencode($order_id);

        $response = wp_remote_get($endpoint, [
            'headers' => [
                'Authorization' => $bearer_token,
                'Accept'        => 'application/json',
            ],
            'timeout' => 15,
        ]);

        if (is_wp_error($response)) {
            error_log('TelegraMD Order Fetch Error: ' . $response->get_error_message());
            return new WP_Error('order_fetch_failed', 'Failed to retrieve order data.');
        }

        $status_code   = wp_remote_retrieve_response_code($response);
        $response_body = wp_remote_retrieve_body($response);
        $data          = json_decode($response_body, true);

        if ($status_code !== 200 || !$data || !isset($data['id'])) {
            error_log('TelegraMD Order Invalid Response: ' . $response_body);
            return new WP_Error('order_invalid', 'Invalid order data.');
        }

        if ($info == "") {
            return $data;
        }
        return $data[$info];
    }

    public static function get_patient_name()
    {
        // Ensure user is logged in
        if (!is_user_logged_in()) {
            error_log('get_patient_name() called but user not logged in');
            return 'Guest User';
        }

        $user = wp_get_current_user();

        // Priority fallback chain for best name
        $name = $user->first_name;

        if (empty($name)) {
            $name = $user->display_name;
        }

        if (empty($name)) {
            $name = $user->user_nicename;
        }

        if (empty($name)) {
            $name = $user->user_login;
        }

        // Final safety check
        $name = trim($name) ?: 'Unnamed User';

        return $name;
    }




    public static function create_patient()
    {
        global $wpdb;
        if (!is_user_logged_in()) {
            return false;
        }
        $user = wp_get_current_user();
        // Only subscriber is a patient not any other user
        if (!in_array('subscriber', (array) $user->roles)) {
            return false;
        }

        $email = $user->user_email;
        if (empty($email) || !is_email($email)) {
            error_log('HLD_Telegra: Invalid user email for create_patient');
            return false;
        }
        // Check if patient already has Telegra ID
        $existing_id = HLD_Patient::get_telegra_patient_id($email);
        if ($existing_id) {
            error_log("Patient already exists, Telegra ID: " . $existing_id);
            return $existing_id;
        }

        $patient_table = HEALSEND_PATIENTS_TABLE;
        $patient = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$patient_table} WHERE patient_email = %s", $email),
            ARRAY_A
        );

        // Extract info or use fallback values
        $first_name = $patient['first_name'] ?? ($user->first_name ?: 'John');
        $last_name  = $patient['last_name'] ?? ($user->last_name ?: 'Doe');
        $dob        = $patient['dob'] ?? '1970-01-01';
        $gender     = $patient['gender'] ?? 'male';
        $phone = isset($patient['phone']) ? preg_replace('/\D/', '', $patient['phone']) : '18008291040';
        // Build payload for API
        $payload = [
            'name'             => trim($first_name . ' ' . $last_name) ?: $email,
            'firstName'        => $first_name,
            'lastName'         => $last_name,
            'email'            => $email,
            'phone'            => $phone,
            'dateOfBirth'      => $dob,
            'gender'           => $gender,
            'genderBiological' => $gender,
            'affiliate'        => TELEGRAMD_AFFLIATE_ID,
        ];

        $args = [
            'method'  => 'POST',
            'headers' => [
                'accept'        => 'application/json',
                'authorization' => defined('TELEGRAMD_BEARER_TOKEN') ? 'Bearer ' . TELEGRAMD_BEARER_TOKEN : '',
                'content-type'  => 'application/json',
            ],
            'body'    => wp_json_encode($payload),
            'timeout' => 20,
        ];

        $endpoint = TELEGRA_BASE_URL . '/patients';
        $response = wp_remote_post($endpoint, $args);

        if (is_wp_error($response)) {
            error_log('HLD_Telegra API Error: ' . $response->get_error_message());
            return false;
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (!empty($data['id'])) {
            // Save to local patients table
            $saved = HLD_Patient::set_telegra_patient_id(
                $email,
                $data['id'],
                $first_name,
                $last_name
            );

            if ($saved) {
                return $data['id'];
            } else {
                error_log('HLD_Telegra: Failed to save patient ID locally for ' . $email);
                return false;
            }
        } else {
            error_log('HLD_Telegra API Response Missing ID: ' . $body);
            return false;
        }
    }







    /**
     * Submit questionnaire answers to Telegra for a given order + questionnaire instance.
     *
     * @param string $order_id   The Telegra order ID (for logging/reference).
     * @param string $quinst_id  The questionnaire instance ID (e.g., quinst::xxxx).
     * @param array  $answers    Array of answers in Telegra format:
     *                           [
     *                              [ "location" => "loc::Glp-intakeform-1", "value" => "A13BCAA0" ],
     *                              [ "location" => "loc::Glp-intakeform-2", "value" => "Some free text" ]
     *                           ]
     * @return array|WP_Error    API response array or WP_Error on failure.
     */
    public function submit_questionnaire_answers($order_id, $quinst_id, $responses = [], $last_location = null)
    {
        // Validate questionnaire instance ID
        if (empty($quinst_id) || strpos($quinst_id, 'quinst::') !== 0) {
            return new WP_Error('invalid_quinst', 'Invalid questionnaire instance ID.');
        }

        // Validate responses
        if (empty($responses) || !is_array($responses)) {
            return new WP_Error('no_responses', 'No questionnaire responses provided.');
        }

        // Prepare endpoint & headers
        $bearer_token = 'Bearer ' . TELEGRAMD_BEARER_TOKEN;
        $endpoint     = TELEGRA_BASE_URL . '/questionnaireInstances/' . rawurlencode($quinst_id) . '/actions/answer';

        // Body must match Telegra spec
        $body = [
            'responses' => $responses,
        ];

        if (!empty($last_location)) {
            $body['lastLocation'] = $last_location;
        }

        // Send PUT request
        $response = wp_remote_request($endpoint, [
            'method'  => 'PUT',
            'headers' => [
                'Authorization' => $bearer_token,
                'Content-Type'  => 'application/json',
                'Accept'        => 'application/json',
            ],
            'body'    => wp_json_encode($body),
            'timeout' => 20,
        ]);
        // error_log("body json");
        // error_log(wp_json_encode($body));

        // Handle transport error
        if (is_wp_error($response)) {
            error_log("[TelegraMD] Questionnaire Answer API Transport Error (Order: $order_id) → " . $response->get_error_message());
            return new WP_Error('api_error', 'Failed to send questionnaire responses: ' . $response->get_error_message());
        }

        $status_code   = wp_remote_retrieve_response_code($response);
        $response_body = wp_remote_retrieve_body($response);
        $data          = json_decode($response_body, true);

        // Telegra returns success only on 200/201
        if ($status_code !== 200 && $status_code !== 201) {
            error_log("[TelegraMD] Questionnaire Answer Failed (Order: $order_id, Quinst: $quinst_id) → HTTP $status_code → $response_body");
            var_dump(print_r($response));
            return new WP_Error('answer_failed', 'Telegra API returned error: ' . $response_body);
        }

        error_log("[TelegraMD] Questionnaire Responses Submitted Successfully (Order: $order_id, Quinst: $quinst_id) → " . print_r($data, true));

        return $data;
    }


    public function update_patient_on_telegra($telegra_patient_id, $data = [])
    {
        if (empty($telegra_patient_id)) {
            return new WP_Error('missing_patient_id', 'Missing Telegra patient ID.');
        }

        if (empty($data) || !is_array($data)) {
            return new WP_Error('invalid_data', 'Invalid or empty patient data.');
        }

        $bearer_token = 'Bearer ' . TELEGRAMD_BEARER_TOKEN;
        $endpoint     = TELEGRA_BASE_URL . '/patients/' . urlencode($telegra_patient_id);

        $args = [
            'method'  => 'PUT',
            'headers' => [
                'Authorization' => $bearer_token,
                'Content-Type'  => 'application/json',
                'Accept'        => 'application/json',
            ],
            'body'    => wp_json_encode($data),
            'timeout' => 20,
        ];

        $response = wp_remote_request($endpoint, $args);

        // Handle transport-level errors
        if (is_wp_error($response)) {
            error_log('[TelegraMD] Patient Update Error: ' . $response->get_error_message());
            return new WP_Error('api_error', 'Failed to update patient: ' . $response->get_error_message());
        }

        $status_code   = wp_remote_retrieve_response_code($response);
        $response_body = wp_remote_retrieve_body($response);
        $decoded       = json_decode($response_body, true);

        if ($status_code !== 200) {
            error_log("[TelegraMD] Failed to update patient ($telegra_patient_id) → HTTP $status_code → $response_body");
            return new WP_Error('update_failed', 'Telegra API error: ' . $response_body);
        }

        error_log("[TelegraMD] Patient Updated Successfully ($telegra_patient_id): " . print_r($decoded, true));

        return $decoded;
    }






    public function get_patient_id()
    {
        global $wpdb;


        error_log("function get_patint_id is called");
        if (!is_user_logged_in()) {
            return null;
        }

        $user_id = get_current_user_id();
        $user = get_userdata($user_id);

        if (!$user || empty($user->user_email)) {
            error_log("user or user email is empty");
            error_log("user 183" . $user);
            error_log("useremail 184" . $user->user_email);
            return null;
        }

        $email = $user->user_email;

        // Get the patients table name from HLD_DB_Tables
        $table = HLD_DB_Tables::get_table('patients');

        if (empty($table)) {
            return null; // table not found
        }

        // Fetch telegra_patient_id
        $patient_id = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT telegra_patient_id 
             FROM {$table} 
             WHERE patient_email = %s 
             AND is_deleted = 0 
             LIMIT 1",
                $email
            )
        );

        error_log("Patient id is206" . $patient_id);

        return !empty($patient_id) ? $patient_id : null;
    }
    // function ends





    public function create_order($telegra_patient_id, $medication_id, $symptoms = [])
    {
        $bearer_token = 'Bearer ' . TELEGRAMD_BEARER_TOKEN;
        $endpoint = TELEGRA_BASE_URL . '/orders';
        $patient = HLD_Patient::get_patient_info();
        error_log("Patient detail in create order function in class telegra function 423: " . print_r($patient, true));
        error_log("[HEALSEND NOTICE] product variation is " . $medication_id);
        $body = [
            "data" => [
                "someData" => "?"
            ],
            "patient" => $telegra_patient_id,
            "productVariations" => [
                [
                    "productVariation" => $medication_id,
                    "quantity" => 1
                ]
            ],
            "symptoms" => (array) $symptoms,
            "address" => [
                "billing" => [
                    "address1" => $patient['address'],
                    "address2" => null,
                    "city"     => $patient['city'],
                    "state"    => $patient['state'],
                    "zipcode"  => $patient['zip_code']
                ],
                "shipping" => [
                    "address1" => $patient['address'],
                    "address2" => null,
                    "city"     => $patient['city'],
                    "state"    => $patient['state'],
                    "zipcode"  => $patient['zip_code']
                ]
            ]
        ];
        $response = wp_remote_post($endpoint, [
            'method'    => 'POST',
            'headers'   => [
                'Authorization' => $bearer_token,
                'Content-Type'  => 'application/json',
                'Accept'        => 'application/json',
            ],
            'body'      => json_encode($body),
            'timeout'   => 20,
        ]);
        // Check for transport-level WP error
        if (is_wp_error($response)) {
            error_log('[TelegraMD Error] cURL Error: ' . $response->get_error_message());
            return new WP_Error('api_error', 'Failed to create order: ' . $response->get_error_message());
        }
        $status_code   = wp_remote_retrieve_response_code($response);
        $response_body = wp_remote_retrieve_body($response);
        $data          = json_decode($response_body, true);

        if ($status_code !== 200 && $status_code !== 201) {
            error_log('[TelegraMD Order Failed] HTTP ' . $status_code . ' → ' . $response_body);
            return new WP_Error('order_failed', 'Order API returned error: ' . $response_body);
        }


        error_log('[TelegraMD Order Created] Status: ' . $status_code . ' → ' . $response_body);
        return $data['id'];
    }
}

// Create an object and call the method
$hld_telegra = new HLD_Telegra();
