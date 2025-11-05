<?php
// Register AJAX handlers
add_action('wp_ajax_hld_view_order_detail', 'hld_view_order_detail_handler');
add_action('wp_ajax_nopriv_hld_view_order_detail', 'hld_view_order_detail_handler');

function hld_view_order_detail_handler() {
    global $hld_telegra;
    // Verify nonce for security
    check_ajax_referer('hld_ajax_nonce', 'nonce');

    $order_id = isset($_POST['order_id']) ? sanitize_text_field($_POST['order_id']) : '';

    if (empty($order_id)) {
        wp_send_json_error(['message' => 'Order ID is missing.']);
    }

    // Load the Telegra class
    if (!class_exists('HLD_Telegra')) {
        wp_send_json_error(['message' => 'Telegra class not found.']);
    }

    
    $response = $hld_telegra->get_order($order_id);

    // Handle WP_Error
    if (is_wp_error($response)) {
        wp_send_json_error(['message' => $response->get_error_message()]);
    }

    // Return the order data
    wp_send_json_success($response);
}
