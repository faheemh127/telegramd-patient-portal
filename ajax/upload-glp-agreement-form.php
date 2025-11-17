<?php

// Handle ID / Document Upload
add_action('wp_ajax_glp_agreement_upload', 'hld_glp_agreement_upload_handler');
add_action('wp_ajax_nopriv_glp_agreement_upload', 'hld_glp_agreement_upload_handler');



function hld_glp_agreement_upload_handler()
{

    global $hld_telegra;
    // Validate file
    if (!isset($_FILES['patient_id']) || $_FILES['patient_id']['error'] !== UPLOAD_ERR_OK) {
        wp_send_json_error(['message' => 'No file uploaded or file error']);
    }

    $telegra_order_id = isset($_POST['telegra_order_id']) ? sanitize_text_field($_POST['telegra_order_id']) : '';
    $order_detail = $hld_telegra->get_order($telegra_order_id);
    $quest_inst = $order_detail["questionnaireInstances"][2]["id"];
    // $order_id = "order::8f4d4e93-32c1-4202-868e-c4c30da48b9d";
    // $quest_inst = "quinst::23c1c69e-a9dd-4792-a871-b525e0bfe793";

    $file      = $_FILES['patient_id'];
    $file_name = sanitize_file_name($file['name']);
    $file_tmp  = $file['tmp_name'];

    if ($file['size'] > 25 * 1024 * 1024) {
        wp_send_json_error(['message' => 'File size exceeds 25MB']);
    }

    $file_url = "";
    $attachment_id = media_handle_upload('patient_id', 0);

    if (is_wp_error($attachment_id)) {
        echo 'Error uploading file: ' . $attachment_id->get_error_message();
    } else {
        echo 'File uploaded! Attachment ID is: ' . $attachment_id;

        $file_url = wp_get_attachment_url($attachment_id);
    }

    $bearer_token = 'Bearer ' . TELEGRAMD_BEARER_TOKEN;
    $api_url = TELEGRA_BASE_URL . '/questionnaireInstances/' . rawurlencode($quest_inst) . '/actions/answerLocation';

    $value = [];
    $value['agreementData'] = [
        'consent' => true,
        'consentDate' => new DateTime("now", new DateTimeZone('UTC'))->format('Y-m-d\TH:i:s.v\Z'),
        'signature' => $file_url,
      ];

    $body = [
        'location' => 'loc::informed-consent:1',
        'data' => $value
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
        wp_send_json_success(['message' => 'File uploaded successfully', 'response' => $response_body]);
    } else {
        wp_send_json_error(['message' => 'API error', 'status' => $status_code, 'response' => $response_body]);
    }
}
