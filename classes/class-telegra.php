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
            "Tirzepatide"   => "pvt::2ae48408-228e-4706-a714-645f774b2f2a",
        ];

        // Return code if exists, otherwise null
        return $codes[$medicine] ?? null;
    }




    public function get_order($order_id, $info)
    {
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



    /**
     * Create patient at TelegraMD for current logged-in user (if not exists locally).
     * Returns the Telegra patient ID string on success, or false on failure.
     */
    public static function create_patient()
    {
        if (!is_user_logged_in()) {
            return false;
        }

        $user = wp_get_current_user();

        // Optional: keep the same role check if you need it
        // if (!in_array('subscriber', (array) $user->roles)) {
        //     return false;
        // }

        $email      = $user->user_email ?: '';
        $first_name = $user->first_name ?: '';
        $last_name  = $user->last_name ?: '';

        if (empty($email) || !is_email($email)) {
            error_log('HLD_Telegra: invalid user email for create_patient');
            return false;
        }

        // 1) Check local DB for existing telegra_patient_id
        $existing_id = HLD_Patient::get_telegra_patient_id($email);
        if ($existing_id) {
            // Return existing Telegra ID — do NOT create a new one
            error_log("Patient already exists here is the patient id 16" .  $existing_id);
            return $existing_id;
        }

        // 2) Build payload for TelegraMD API
        // $payload = [
        //     'name'             => trim($first_name . ' ' . $last_name) ?: $email,
        //     'firstName'        => $first_name ?: '',
        //     'lastName'         => $last_name ?: '',
        //     'email'            => $email,
        //     'phone'            => '',            // leave blank or use placeholder
        //     'dateOfBirth'      => '',            // optional
        //     'gender'           => '',            // optional
        //     'genderBiological' => '',            // optional
        //     'affiliate'        => defined('TELEGRAMD_AFFLIATE_ID') ? TELEGRAMD_AFFLIATE_ID : '',
        // ];


        $payload = [
            'name'             => trim($first_name . ' ' . $last_name) ?: ($email ?: 'John Doe'),
            'firstName'        => $first_name ?: 'John',
            'lastName'         => $last_name ?: 'Doe',
            'email'            => $email ?: 'johndoe@example.com',
            'phone'            => '000-000-0000', // dummy fallback
            'dateOfBirth'      => '1970-01-01',     // default DOB
            'gender'           => 'unknown',     // fallback
            'genderBiological' => 'unknown',
            'affiliate'        => defined('TELEGRAMD_AFFLIATE_ID')
                ? TELEGRAMD_AFFLIATE_ID
                : 'AFFILIATE-123',
        ];




        $args = [
            'method'  => 'POST',
            'headers' => [
                'accept'        => 'application/json',
                'authorization' => defined('TELEGRAMD_BEARER_TOKEN') ? 'Bearer ' . TELEGRAMD_BEARER_TOKEN : '',
                'content-type'  => 'application/json',
            ],
            'body' => wp_json_encode($payload),
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
            // Save into local patients table and return ID
            $saved = HLD_Patient::set_telegra_patient_id($email, $data['id'], $first_name, $last_name);
            if ($saved) {
                return $data['id'];
            } else {
                error_log('HLD_Telegra: saved to local DB failed for telegra_id=' . $data['id']);
                return false;
            }
        } else {
            error_log('HLD_Telegra API Response missing ID: ' . $body);
            return false;
        }
    }

    public function get_patient_id()
    {
        if (!is_user_logged_in()) {
            return null;
        }
        $user_id = get_current_user_id();
        $meta_key = 'hld_patient_' . $user_id . '_telegra_id';
        $patient_id = get_user_meta($user_id, $meta_key, true);

        return !empty($patient_id) ? $patient_id : null;
    }




    function create_order($telegra_patient_id, $medication_id, $symptoms = [])
    {
        $bearer_token = 'Bearer ' . TELEGRAMD_BEARER_TOKEN;
        $endpoint = TELEGRA_BASE_URL . '/orders';

        // 🔧 Prepare the request body based on CURL sample
        $body = [
            "data" => [
                "someData" => "?"
            ],
            "patient" => $telegra_patient_id, // example: pat::f2b6ec7f-4b87-4988-9ebb-df663edaf872
            "productVariations" => [
                [
                    // "productVariation" => $medication_id,
                    "productVariation" => "pvt::6e5a3b9c-26d9-46af-89bb-f0ab864ed027",
                    "quantity" => 1
                ]
            ],
            "symptoms" => (array) $symptoms, // always array
            "address" => [
                "billing" => [
                    "address1" => "123 S Main St",
                    "address2" => null,
                    "city"     => "Kennewick",
                    "state"    => "state::07b1c554-5521-4bab-b65c-8436b72cfcb6",
                    "zipcode"  => 99337
                ],
                "shipping" => [
                    "address1" => "123 S Main St",
                    "address2" => null,
                    "city"     => "Kennewick",
                    "state"    => "state::07b1c554-5521-4bab-b65c-8436b72cfcb6",
                    "zipcode"  => 99337
                ]
            ]
        ];

        // 🛰 Send the POST request
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

        // API returned non-200/201
        if ($status_code !== 200 && $status_code !== 201) {
            error_log('[TelegraMD Order Failed] HTTP ' . $status_code . ' → ' . $response_body);
            return new WP_Error('order_failed', 'Order API returned error: ' . $response_body);
        }

        if (is_user_logged_in()) {
            $user_id = get_current_user_id();
            HLD_UserSubscriptions::add_order($user_id, $data['id']);
        } else {
            error_log("⚠️ User not logged in, cannot save order to user meta.");
        }

        // Success
        error_log('[TelegraMD Order Created] Status: ' . $status_code . ' → ' . $response_body);
        return $data;
    }
}

// Create an object and call the method
$hld_telegra = new HLD_Telegra();
