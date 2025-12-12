<?php

// Handle ID / Document Upload
add_action('wp_ajax_id_upload', 'hld_id_upload_handler');
add_action('wp_ajax_nopriv_id_upload', 'hld_id_upload_handler');



function hld_id_upload_handler()
{
    global $hld_telegra;

    $file = isset($_POST['signature']) ? sanitize_text_field($_POST['signature']) : '';
    $telegra_order_id = isset($_POST['telegra_order_id']) ? sanitize_text_field($_POST['telegra_order_id']) : '';
    $order_detail = $hld_telegra->get_order($telegra_order_id);
    $quest_inst = $order_detail["questionnaireInstances"][1]["id"];

    $bearer_token = 'Bearer ' . TELEGRAMD_BEARER_TOKEN;
    $api_url = TELEGRA_BASE_URL . '/questionnaireInstances/' . rawurlencode($quest_inst) . '/actions/answerLocation';

    // $api_url = TELEGRA_BASE_URL . "/patients/{$patient_id}/uploadFile";

    $body = [
        "location" => "loc::identification-questionnaire:2",
        "value" => $file
    ];

    $response = wp_remote_request($api_url, [
        'method'  => 'PUT',
        'headers' => [
            'Authorization' => $bearer_token,
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/json',
        ],
        'body'    => wp_json_encode($body),
        'timeout' => 20,
    ]);

    if (is_wp_error($response)) {
        wp_send_json_error(['message' => $response->get_error_message()]);
    }

    $status_code = wp_remote_retrieve_response_code($response);
    $response_body = wp_remote_retrieve_body($response);

    if ($status_code == 200) {
        HLD_ActionItems_Manager::mark_action_item_completed($telegra_order_id, "id_upload");


        wp_send_json_success([
            'message' => 'File uploaded successfully',
            'response' => $response_body,
            "patient_dashboard_url" => HLD_PATIENT_DASHBOARD_URL
        ]);
    } else {
        wp_send_json_error(['message' => 'API error', 'status' => $status_code, 'response' => $response_body]);
    }
}
