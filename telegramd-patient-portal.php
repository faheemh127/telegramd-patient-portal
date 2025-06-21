<?php

/**
 * Plugin Name: TelegraMD Patient Portal
 * Description: Patient portal with TelegraMD API integration for prescriptions, labs, and subscriptions.
 * Version: 1.0
 * Author: Faheem
 * Author URI: https://faheemhassan.dev
 * Prefix: hld
 */


// Include API
// require_once plugin_dir_path(__FILE__) . 'telegramd-api.php';
// // Register shortcodes
// require_once plugin_dir_path(__FILE__) . 'shortcodes.php';
// // Admin settings page
// require_once plugin_dir_path(__FILE__) . 'admin-settings.php';



include_once('api-keys.php');

add_action('fluentform/before_insert_submission', 'hld_before_submission_create_order_in_telegramd', 10, 3);

// function hld_before_submission_create_order_in_telegramd($insertData, $data, $form)
// {
//     // Decode submitted data from Fluent Forms
//     $responseData = json_decode($insertData['response'], true);

//     // STEP 1: Create the patient
//     $patient_data = [
//         'dateOfBirth'      => $responseData['dob']         ?? '10-05-1992',
//         'email'            => $responseData['email']       ?? 'Chewbacca1@rebellion.com',
//         'firstName'        => $responseData['first_name']  ?? 'Chewbacca',
//         'lastName'         => $responseData['last_name']   ?? 'Wookie',
//         'gender'           => $responseData['gender']      ?? 'male',
//         'genderBiological' => $responseData['gender_bio']  ?? 'male',
//         'phone'            => $responseData['phone']       ?? '8888888888',
//     ];

//     $auth_token = 'Bearer '. TELEGRAMD_BEARER_TOKEN; // TRUNCATED FOR BREVITY

//     $patient_response = wp_remote_post('https://dev-core-ias-rest.telegramd.com/patients', [
//         'headers' => [
//             'Content-Type'  => 'application/json',
//             'Accept'        => 'application/json',
//             'Authorization' => $auth_token,
//         ],
//         'body'    => wp_json_encode($patient_data),
//         'timeout' => 20,
//     ]);

//     if (is_wp_error($patient_response)) {
//         error_log('TelegraMD Patient API error: ' . $patient_response->get_error_message());
//         return; // Abort order creation
//     }

//     $patient_code = wp_remote_retrieve_response_code($patient_response);
//     $patient_body = json_decode(wp_remote_retrieve_body($patient_response), true);

//     if ($patient_code !== 200 || empty($patient_body['patient'])) {
//         error_log("TelegraMD Patient creation failed: HTTP $patient_code — " . print_r($patient_body, true));
//         return;
//     }

//     $patient_id = $patient_body['patient']; // e.g., "pat::xxxx"

//     error_log("telegraMD patient ID: ", $patient_id);
// }



// add_action('fluentform/before_insert_submission', 'hld_before_submission_create_order_in_telegramd', 10, 3);

// function hld_before_submission_create_order_in_telegramd($insertData, $data, $form)
// {
//     $auth_token = 'Bearer '. TELEGRAMD_BEARER_TOKEN;

//     $response = wp_remote_get('https://dev-core-ias-rest.telegramd.com/productVariations', [
//         'headers' => [
//             'Accept'        => 'application/json',
//             'Authorization' => $auth_token,
//         ],
//         'timeout' => 20,
//     ]);

//     if (is_wp_error($response)) {
//         error_log('TelegraMD productVariations API error: ' . $response->get_error_message());
//     } else {
//         $code = wp_remote_retrieve_response_code($response);
//         $body = wp_remote_retrieve_body($response);
//         error_log("TelegraMD productVariations response: HTTP $code — $body");
//     }
// }



add_action('fluentform/before_insert_submission', 'hld_before_submission_create_order_in_telegramd', 10, 3);

function hld_before_submission_create_order_in_telegramd($insertData, $data, $form)
{
    // Define your Basic Auth token constant (you can move this to wp-config.php or a secure location)
    if (!defined('TELEGRAMD_BEARER_TOKEN')) {
        define('TELEGRAMD_BEARER_TOKEN', 'Basic ZmFoZWVtaDEyN0BnbWFpbC5jb206IUAjNDU2Jioo');
    }

    $response = wp_remote_post('https://dev-core-ias-rest.telegramd.com/auth/client', [
        'headers' => [
            'Accept'        => 'application/json',
            'Authorization' => TELEGRAMD_BEARER_TOKEN,
        ],
        'timeout' => 20,
    ]);

    if (is_wp_error($response)) {
        error_log('TelegraMD auth/client API error: ' . $response->get_error_message());
    } else {
        $code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        error_log("TelegraMD auth/client response: HTTP $code — $body");
    }
}






add_shortcode('hld_orders', function () {
    ob_start();
    include_once('templates/show-orders.php');
    return ob_get_clean();
});

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style(
        'hld-plugin-css',
        plugin_dir_url(__FILE__) . 'css/style.css',
        [],
        '1.0'
    );
    wp_enqueue_style(
        'hld-plugin-scss',
        plugin_dir_url(__FILE__) . 'css/main.css',
        [],
        '1.0'
    );
});

require_once plugin_dir_path(__FILE__) . 'classes/dashboard-shortcode.php';
new DashboardShortcode();
