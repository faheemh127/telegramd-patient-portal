<?php

// Handle ID / Document Upload
add_action('wp_ajax_glp_agreement_upload', 'hld_glp_agreement_upload_handler');
add_action('wp_ajax_nopriv_glp_agreement_upload', 'hld_glp_agreement_upload_handler');




function hld_glp_agreement_upload_handler()
{
    global $hld_telegra;

    // Validate file

    if (empty($_POST['signature'])) {
        wp_send_json_error(['message' => 'Missing Signature.']);
    }

    if (empty($_POST['telegra_order_id'])) {
        wp_send_json_error(['message' => 'Missing order ID']);
    }

    $signature = sanitize_text_field($_POST['signature']);
    $telegra_order_id = sanitize_text_field($_POST['telegra_order_id']);










    $order_detail = $hld_telegra->get_order($telegra_order_id);
    $quinst_id = HLD_Telegra::get_informed_consent_quinst_id($order_detail);

    if (!$quinst_id) {
        wp_send_json_error(['message' => 'Questionnaire Instance ID not found']);
        wp_die();
    }



    // // @todo make it dynamic quinstn id
    // if (
    //     !isset($order_detail["questionnaireInstances"][2]["id"])
    // ) {
    //     wp_send_json_error(['message' => 'Invalid questionnaire instance']);
    // }

    // $quest_inst = $order_detail["questionnaireInstances"][2]["id"];






    $quest_inst = $quinst_id;



    // Build API payload
    $bearer_token = 'Bearer ' . TELEGRAMD_BEARER_TOKEN;
    $api_url = TELEGRA_BASE_URL . '/questionnaireInstances/' . rawurlencode($quest_inst) . '/actions/answerLocation';

    $value = [
        'agreementData' => [
            'consent' => true,
            'consentDate' => gmdate('Y-m-d\TH:i:s.v\Z'),
            'signature' => $signature,
        ]
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
        HLD_ActionItems_Manager::mark_action_item_completed($telegra_order_id, "agreement");
        wp_send_json_success([
            'message' => 'File uploaded successfully',
            'response' => $response_body,
            "patient_dashboard_url" => HLD_PATIENT_DASHBOARD_URL
        ]);
    }

    wp_send_json_error(['message' => 'API error', 'status' => $status_code, 'response' => $response_body]);
}
