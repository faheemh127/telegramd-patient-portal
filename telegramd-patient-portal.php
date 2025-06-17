<?php

/**
 * Plugin Name: TelegraMD Patient Portal
 * Description: Patient portal with TelegraMD API integration for prescriptions, labs, and subscriptions.
 * Version: 1.0
 * Author: faheemhassan.dev
 */

// Include API
// require_once plugin_dir_path(__FILE__) . 'telegramd-api.php';
// // Register shortcodes
// require_once plugin_dir_path(__FILE__) . 'shortcodes.php';
// // Admin settings page
// require_once plugin_dir_path(__FILE__) . 'admin-settings.php';
include_once('api-keys.php');

add_action('fluentform/before_insert_submission', 'hsd_before_submission_create_order_in_telegramd', 10, 3);

function hsd_before_submission_create_order_in_telegramd($insertData, $data, $form)
{
    // Decode submitted data
    $responseData = json_decode($insertData['response'], true);

    // Prepare request payload for TelegraMD—a sample structure
    $payload = [
        'patient' => [
            'radio_choice'     => $responseData['input_radio'] ?? '',
            'dosage'           => $responseData['input_radio_1'] ?? '',
            'duration'         => $responseData['input_radio_3'] ?? '',
            'quantity'         => $responseData['numeric_field_1'] ?? '',
            'weight_loss_goal' => $responseData['input_radio_5'] ?? '',
            'side_effects'     => $responseData['checkbox'] ?? [],
            // Add more fields as needed...
        ],
        'meta' => [
            'form_id'  => $insertData['form_id'],
            'entry_at' => $insertData['created_at'],
        ],
    ];

    // Log the payload for debugging
    error_log('TelegraMD payload: ' . print_r($payload, true));

    // Perform HTTP POST to TelegraMD's order endpoint
    $wpr = wp_remote_post(
        'https://api.telegramd.com/v1/orders',
        [
            'headers' => [
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer ' . defined('TELEGRAMD_API_KEY') ? TELEGRAMD_API_KEY : '',
            ],
            'body'    => wp_json_encode($payload),
            'timeout' => 20,
        ]
    );

    if (is_wp_error($wpr)) {
        error_log('TelegraMD API error: ' . $wpr->get_error_message());
    } else {
        $code     = wp_remote_retrieve_response_code($wpr);
        $response = wp_remote_retrieve_body($wpr);
        error_log("TelegraMD responded: HTTP $code — $response");
    }
}
