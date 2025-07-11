<?php

add_action('rest_api_init', function () {
    register_rest_route('telegramd/v1', '/prescription-approved', [
        'methods'  => 'POST',
        'callback' => 'hld_telegra_prescription_approved_handler',
        'permission_callback' => '__return_true',
    ]);
});



function hld_telegra_prescription_approved_handler(WP_REST_Request $request)
{
    $data = $request->get_json_params();

    error_log('Prescription Approved Webhook Received: ' . print_r($data, true));

    // Confirm it’s the correct event
    if (!isset($data['eventType']) || $data['eventType'] !== 'EVENT_TYPES.PRESCRIPTION_APPROVED_BY_PRACTITIONER') {
        return new WP_REST_Response(['message' => 'Not a prescription approval event.'], 200);
    }

    $prescription_fulfillment_id = $data['targetEntity'] ?? null;
    $performedBy = $data['eventData']['performedBy'] ?? [];

    if (!$prescription_fulfillment_id || empty($performedBy)) {
        return new WP_REST_Response(['message' => 'Missing prescription or performer info'], 400);
    }

    $approver_name = $performedBy['name'] ?? 'Unknown';
    $prescription_id = $performedBy['prescription'] ?? 'Unknown';
    $order_id = $performedBy['order'] ?? 'Unknown';

    // Store the data (simple version using options)
    $key = 'approved_prescription_' . sanitize_key(str_replace(['::'], '_', $prescription_fulfillment_id));
    $value = [
        'approver' => $approver_name,
        'prescription_id' => $prescription_id,
        'order_id' => $order_id,
        'approved_at' => current_time('mysql'),
    ];
    update_option($key, $value);

    error_log("Prescription approved saved under key: $key");

    return new WP_REST_Response(['message' => 'Prescription approval saved.'], 200);
}



// $prfm_id = 'prfm::f75dc16c-5cea-4f4e-beb4-19516c7cab1a';
// $key = 'approved_prescription_' . sanitize_key(str_replace(['::'], '_', $prfm_id));
// $approval_data = get_option($key);

// if ($approval_data) {
//     echo '<strong>Approved By:</strong> ' . esc_html($approval_data['approver']) . '<br>';
//     echo '<strong>Order ID:</strong> ' . esc_html($approval_data['order_id']) . '<br>';
//     echo '<strong>Prescription ID:</strong> ' . esc_html($approval_data['prescription_id']) . '<br>';
//     echo '<strong>Time:</strong> ' . esc_html($approval_data['approved_at']);
// }



// link need to add in telegra 
// https://healsend.com/wp-json/telegramd/v1/prescription-approved


error_log("Prescription approved saved under key");
