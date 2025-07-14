<?php
add_action('rest_api_init', function () {
    register_rest_route('telegramd/v1', '/prescription-approved', [
        'methods'  => 'POST',
        'callback' => 'hld_handle_prescription_approval',
        'permission_callback' => '__return_true',
    ]);
});

function hld_handle_prescription_approval(WP_REST_Request $request)
{
    $data = $request->get_json_params();

    error_log('Prescription Approval Webhook Received: ' . print_r($data, true));

    // Validate event type
    if (!isset($data['eventType']) || $data['eventType'] !== 'EVENT_TYPES.PRESCRIPTION_APPROVED_BY_PRACTITIONER') {
        return new WP_REST_Response(['message' => 'Ignored. Not a prescription approval event.'], 200);
    }

    // Extract relevant data
    $owner_entity         = $data['ownerEntity'] ?? null;
    $owner_model          = $data['ownerEntityModel'] ?? null;
    $target_entity        = $data['targetEntity'] ?? null;
    $target_model         = $data['targetEntityModel'] ?? null;
    $event_title          = $data['eventTitle'] ?? null;
    $event_type           = $data['eventType'] ?? null;

    $performed_by         = $data['eventData']['performedBy'] ?? [];

    $practitioner_id      = $performed_by['id'] ?? null;
    $practitioner_role    = $performed_by['role'] ?? null;
    $practitioner_name    = $performed_by['name'] ?? null;
    $prescription_id      = $performed_by['prescription'] ?? null;
    $order_id             = $performed_by['order'] ?? null;

    if (
        !$owner_entity || !$target_entity ||
        !$practitioner_id || !$prescription_id || !$order_id
    ) {
        return new WP_REST_Response(['message' => 'Missing required fields.'], 400);
    }

    // Store for later processing
    $key = 'prescription_approved_' . sanitize_key(str_replace(['::'], '_', $target_entity));
    $value = [
        'patient_id'         => $owner_entity,
        'prescription_id'    => $prescription_id,
        'order_id'           => $order_id,
        'fulfillment_id'     => $target_entity,
        'approver_id'        => $practitioner_id,
        'approver_role'      => $practitioner_role,
        'approver_name'      => $practitioner_name,
        'event_title'        => $event_title,
        'approved_at'        => current_time('mysql'),
        'raw'                => $data, // for debug/logging
    ];

    update_option($key, $value);

    error_log("Prescription approval saved under key: $key");

    error_log("Prescription Approved for" . $order_id . " and result is " . $result);

    $patient_id = $owner_entity;
    $user_id = get_user_id_by_telegra_patient_id($patient_id);

    if ($user_id) {
        // echo "Matched WP User ID: " . $user_id;
        $result = hld_charge_later($user_id, 167); // $500
    } else {
        // echo "No user found for Telegra patient ID.";
        return new WP_REST_Response(['message' => 'No user found for Telegra patient ID.'], 200);
    }

    return new WP_REST_Response(['message' => 'Prescription approved and saved.'], 200);
}
