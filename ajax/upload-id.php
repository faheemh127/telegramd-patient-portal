<?php
// Handle ID / Document Upload
add_action('wp_ajax_id_upload', 'hld_id_upload_handler');
add_action('wp_ajax_nopriv_id_upload', 'hld_id_upload_handler');

function hld_id_upload_handler()
{
    // Validate file
    if (!isset($_FILES['patient_id']) || $_FILES['patient_id']['error'] !== UPLOAD_ERR_OK) {
        wp_send_json_error(['message' => 'No file uploaded or file error']);
    }

    $file      = $_FILES['patient_id'];
    $file_name = sanitize_file_name($file['name']);
    $file_tmp  = $file['tmp_name'];

    // Check size (limit 5MB here, though Telegra allows 25MB)
    if ($file['size'] > 25 * 1024 * 1024) {
        wp_send_json_error(['message' => 'File size exceeds 25MB']);
    }

    // Convert file to base64
    $file_data = base64_encode(file_get_contents($file_tmp));


    $patient_id =  HLD_Patient::get_telegra_patient_id();

    // Prepare request to Telegra API
    $api_url = TELEGRA_BASE_URL . "/patients/{$patient_id}/uploadFile";

    $body = [
        "data" => [
            "fileData" => $file_data,
            "fileName" => $file_name
        ],
        "patientId" => $patient_id
    ];

    $args = [
        'headers' => [
            'Authorization' => 'Bearer ' . TELEGRAMD_BEARER_TOKEN,
            'Content-Type'  => 'application/json',
        ],
        'body'    => wp_json_encode($body),
        'timeout' => 30,
    ];

    $response = wp_remote_post($api_url, $args);

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
