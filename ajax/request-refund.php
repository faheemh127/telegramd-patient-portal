<?php
add_action('wp_ajax_hld_request_refund', 'hld_request_refund_callback');
add_action('wp_ajax_nopriv_hld_request_refund', 'hld_request_refund_callback');

function hld_request_refund_callback()
{
    check_ajax_referer('hld_nonce', 'nonce');

    global $wpdb;
    $table = HEALSEND_SUBSCRIPTIONS_TABLE; // UPDATE THIS

    $order_id = sanitize_text_field($_POST['telegra_order_id']);

    if (empty($order_id)) {
        wp_send_json_error(['message' => 'Invalid order ID.']);
    }

    // Check if row exists
    $exists = $wpdb->get_var(
        $wpdb->prepare("SELECT COUNT(*) FROM {$table} WHERE telegra_order_id = %s", $order_id)
    );

    if (!$exists) {
        wp_send_json_error(['message' => 'Order not found.']);
    }

    // Update refund status
    $updated = $wpdb->update(
        $table,
        ['refund_status' => 'requested'],
        ['telegra_order_id' => $order_id],
        ['%s'],
        ['%s']
    );

    if ($updated === false) {
        wp_send_json_error(['message' => 'Database update failed.']);
    }

    wp_send_json_success(['message' => 'Refund status updated to requested.']);
}
