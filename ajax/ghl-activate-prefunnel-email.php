<?php

add_action('hld_send_ghl_webhook_event', 'hld_ghl_execute_webhook_logic', 10, 2);
add_action('wp_ajax_activate_reminder', 'hld_ghl_activate_reminder');
add_action('wp_ajax_nopriv_activate_reminder', 'hld_ghl_activate_reminder');
// function hld_ghl_activate_reminder()
// {
//     global $wpdb;
//     $table = $wpdb->prefix . 'healsend_subscriptions';
//
//     // Ensure user is logged in
//     if (!is_user_logged_in()) {
//         wp_send_json_error(['message' => 'You must be logged in to continue.']);
//         wp_die();
//     }
//
//     $current_user = wp_get_current_user();
//     $patient_email = $current_user->user_email;
//
//
//     $api_key = 'pit-dcbcc991-8612-49ae-a5ff-31046d43da5b';
//     try {
//         $GhlApiClient = new GhlApiClient($api_key);
//         $data = [
//             "email" => $patient_email
//         ];
//
//         $GhlApiClient->sendToWebhook('https://services.leadconnectorhq.com/hooks/tqGhhCGePHa1hQkrrOQY/webhook-trigger/6Gq0WiCp523gtFLozsJX', $data);
//
//         wp_send_json_success([ 'success' => true ]);
//     } catch (Exception $e) {
//         wp_send_json_error(['message' => $e->getMessage()]);
//     }
//
//     wp_die();
// }


function hld_ghl_execute_webhook_logic($patient_email, $patient_phone)
{
    try {
        $GhlApiClient = new GhlApiClient(GHL_API_KEY);

        $data = [
            "email" => $patient_email,
            "phone" => $patient_phone
        ];


        $GhlApiClient->sendToWebhook('https://services.leadconnectorhq.com/hooks/tqGhhCGePHa1hQkrrOQY/webhook-trigger/iohdk7jyR5hu6jA1MhKI', $data);
        // $GhlApiClient->sendToWebhook('https://services.leadconnectorhq.com/hooks/tqGhhCGePHa1hQkrrOQY/webhook-trigger/6Gq0WiCp523gtFLozsJX', $data);
    } catch (Exception $e) {
        error_log('GHL Webhook Failed: ' . $e->getMessage());
    }
}





function hld_ghl_activate_reminder()
{
    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'You must be logged in.']);
        wp_die();
    }

    error_log("execution of remainders is paused due to testing on faheem side");
    return;
    $phone = $_POST['phone'];

    $delay_seconds = 900; // 15 minutes
    // $delay_seconds = 10; // 10 seconds

    $current_user = wp_get_current_user();
    $patient_email = $current_user->user_email;
    $patient = HLD_Patient::get_patient_info();
    $patient_phone = $patient['phone'];

    $patient_email = "vineethsreddy@gmail.com";
    hld_ghl_execute_webhook_logic($patient_email, $phone);
    // $args = [$patient_email, $patient_phone];
    // $hook_name = 'hld_send_ghl_webhook_event';

    // $timestamp = wp_next_scheduled($hook_name, $args);
    // if ($timestamp) {
    //     wp_unschedule_event($timestamp, $hook_name, $args);
    // }

    // wp_schedule_single_event(time() + $delay_seconds, $hook_name, $args);
    // wp_send_json_success(['success' => true, 'message' => 'Reminder scheduled after 15 minutes.']);
    // wp_die();
}
