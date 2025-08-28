<?php

class hldTelegra
{

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
}

// Create an object and call the method
$telegra = new hldTelegra();
