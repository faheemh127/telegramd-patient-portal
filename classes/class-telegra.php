<?php

class hldTelegra
{

    function get_order($order_id, $info)
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



    public function create_patient()
    {
        if (!is_user_logged_in()) {
            return;
        }

        $user = wp_get_current_user();
        if (!in_array('subscriber', (array) $user->roles)) {
            return;
        }

        $user_id = $user->ID;
        // Don't change this key other developers are using this.
        $meta_key = 'hld_patient_' . $user_id . '_telegra_id';

        // Skip if already exists
        if (get_user_meta($user_id, $meta_key, true)) {
            error_log("TelegraMD: Patient already exists for user $user_id");
            return;
        }

        // Prepare payload with available user data
        $first_name = $user->first_name ?: 'Chewbacca';
        $last_name  = $user->last_name ?: 'Wookie';
        $email      = $user->user_email ?: 'johndoe@example.com';

        $payload = [
            'name'             => $first_name . ' ' . $last_name,
            'firstName'        => $first_name,
            'lastName'         => $last_name,
            'email'            => $email,
            'phone'            => '1111111111', // Placeholder
            'dateOfBirth'      => '1990-01-01', // Placeholder
            'gender'           => 'male',       // Placeholder
            'genderBiological' => 'male',       // Placeholder
            'affiliate'        => TELEGRAMD_AFFLIATE_ID,
        ];

        $response = wp_remote_post('https://dev-core-ias-rest.telegramd.com/patients', [
            'method'  => 'POST',
            'headers' => [
                'accept'        => 'application/json',
                'authorization' => 'Bearer ' . TELEGRAMD_BEARER_TOKEN,
                'content-type'  => 'application/json',
            ],
            'body' => json_encode($payload),
        ]);

        if (is_wp_error($response)) {
            error_log('TelegraMD API Error: ' . $response->get_error_message());
        } else {
            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body, true);

            if (!empty($data['id'])) {
                update_user_meta($user_id, $meta_key, $data['id']);
                error_log("TelegraMD: Patient created and saved for user $user_id with ID: {$data['id']}");
            } else {
                error_log("TelegraMD API Response missing ID: " . $body);
            }
        }
    }

    function get_patient_id()
    {
        if (!is_user_logged_in()) {
            return null;
        }
        $user_id = get_current_user_id();
        $meta_key = 'hld_patient_' . $user_id . '_telegra_id';
        $patient_id = get_user_meta($user_id, $meta_key, true);

        return !empty($patient_id) ? $patient_id : null;
    }




    function create_order($telegra_patient_id)
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
                    "productVariation" => "pvt::6e5a3b9c-26d9-46af-89bb-f0ab864ed027",
                    "quantity" => 1
                ]
            ],
            "symptoms" => [
                "symp::9d65e74b-caed-4b38-b343-d7f84946da60"
            ],
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
            HLD_UserOrders::add_order($user_id, $data['id']);
        } else {
            error_log("⚠️ User not logged in, cannot save order to user meta.");
        }

        // Success
        error_log('[TelegraMD Order Created] Status: ' . $status_code . ' → ' . $response_body);
        return $data;
    }
}

// Create an object and call the method
$hld_telegra = new hldTelegra();
