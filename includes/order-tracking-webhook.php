<?php
add_action('rest_api_init', function () {
    register_rest_route('telegramd/v1', '/track-update', [
        'methods'  => 'POST',
        'callback' => 'hld_telegra_webhook_handler',
        'permission_callback' => '__return_true',
    ]);
});

function hld_telegra_webhook_handler(WP_REST_Request $request)
{
    $data = $request->get_json_params();

    error_log('Webhook Received: ' . print_r($data, true));

    // Save common payload data into variables
    $event_id              = $data['_id'] ?? null;
    $event_type            = $data['eventType'] ?? null;
    $owner_entity_model    = $data['ownerEntityModel'] ?? null;
    $target_entity_model   = $data['targetEntityModel'] ?? null;
    $owner_entity          = $data['ownerEntity'] ?? null;
    $target_entity         = $data['targetEntity'] ?? null;
    $event_title           = $data['eventTitle'] ?? null;
    $event_description     = $data['eventDescription'] ?? null;
    $created_at            = $data['createdAt'] ?? null;
    $updated_at            = $data['updatedAt'] ?? null;
    $deleted               = $data['deleted'] ?? false;
    $version               = $data['__v'] ?? null;

    $event_data            = $data['eventData'] ?? [];
    $performed_by          = $event_data['performedBy'] ?? [];
    $shipping_details      = $event_data['shippingDetails'] ?? [];

    // Extracted fields from performedBy
    $approver_name         = $performed_by['name'] ?? 'Unknown';
    $prescription_id       = $performed_by['prescription'] ?? 'Unknown';
    $order_id              = $performed_by['order'] ?? 'Unknown';

    // Optional: Extract tracking info
    $tracking_number       = $shipping_details['trackingNumber'] ?? null;
    $shipping_company      = $shipping_details['shippingCompany'] ?? null;
    $shipped               = $shipping_details['shipped'] ?? null;

    // Only continue if this is the prescription approval event
    if ($event_type !== 'EVENT_TYPES.PRESCRIPTION_APPROVED_BY_PRACTITIONER') {
        return new WP_REST_Response(['message' => 'Ignored event type'], 200);
    }

    // Ensure targetEntity and performer are present
    if (empty($target_entity) || empty($performed_by)) {
        return new WP_REST_Response(['message' => 'Missing prescription or performer info'], 400);
    }

    // Create a safe option key
    $key = 'approved_prescription_' . sanitize_key(str_replace(['::'], '_', $target_entity));

    // Prepare full value for future use
    $value = [
        'approver'         => $approver_name,
        'prescription_id'  => $prescription_id,
        'order_id'         => $order_id,
        'approved_at'      => current_time('mysql'),
        'event_id'         => $event_id,
        'event_title'      => $event_title,
        'event_description' => $event_description,
        'tracking_number'  => $tracking_number,
        'shipping_company' => $shipping_company,
        'shipped'          => $shipped,
        'raw_data'         => $data, // Store raw in case you need debugging later
    ];

    // Store in options table
    update_option($key, $value);
    error_log("✅ Prescription approved saved under key: $key");

    // pat::7f7b95c2-109b-48ed-a55d-5540e370292920
    $user_id = get_user_id_by_telegra_patient_id($owner_entity);
    $notification = $event_title . " " .   $event_description  . ". Shipping Company name is " . $shipping_company;
    HLD_UserNotifications::add_notification($user_id, $notification);
    return new WP_REST_Response(['message' => 'Order Tracing Received.'], 200);
}


// https://healsend.com/wp-json/telegramd/v1/track-update
