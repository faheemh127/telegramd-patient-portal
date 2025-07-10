<?php

add_action('rest_api_init', function () {
    register_rest_route('telegramd/v1', '/track-update', [
        'methods'  => 'POST',
        'callback' => 'hld_telegra_webhook_handler',
        'permission_callback' => '__return_true' // Make public (secure via secret in header if needed)
    ]);
});



function hld_telegra_webhook_handler(WP_REST_Request $request)
{
    $data = $request->get_json_params();

    error_log('📦 TelegraMD Tracking Webhook Received: ' . print_r($data, true));

    // Only proceed if it’s the shipping details event
    if (!isset($data['eventType']) || $data['eventType'] !== 'shipping_details_set') {
        return new WP_REST_Response(['message' => 'Ignored non-shipping event'], 200);
    }

    $targetEntityId = $data['targetEntity'] ?? null; // e.g., prfm::xxxx
    $trackingData   = $data['eventData']['shippingDetails'] ?? null;

    if (!$targetEntityId || !$trackingData) {
        error_log('❌ Missing targetEntity or shippingDetails in webhook.');
        return new WP_REST_Response(['message' => 'Invalid data'], 400);
    }

    // Save tracking data - choose how you want to store it
    // Option 1: Save to wp_options (simple)
    // Option 2: Save as custom post type or order meta if linked to WooCommerce

    $option_key = 'tracking_' . sanitize_key(str_replace(['::'], '_', $targetEntityId));
    update_option($option_key, $trackingData);

    error_log("✅ Tracking info saved under: $option_key");

    return new WP_REST_Response(['message' => 'Tracking info saved.'], 200);
}




// $prfm_id = 'prfm::bfdbf944-9a45-4873-9611-eac000000'; // from your order
// $option_key = 'tracking_' . sanitize_key(str_replace(['::'], '_', $prfm_id));
// $tracking_info = get_option($option_key);

// if ($tracking_info) {
//     echo '<strong>Shipping Company:</strong> ' . esc_html($tracking_info['shippingCompany']);
//     echo '<br><strong>Tracking Number:</strong> ' . esc_html($tracking_info['trackingNumber']);
// }

// if (!empty($tracking_info['trackingNumber'])) {
//     $url = 'https://www.ups.com/track?tracknum=' . urlencode($tracking_info['trackingNumber']);
//     echo '<a target="_blank" href="' . esc_url($url) . '" class="btn btn-sm btn-primary">Track Package</a>';
// }


// 
// https://healsend.com/wp-json/telegramd/v1/track-update

