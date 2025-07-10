<?php


add_action('wp_ajax_save_later_payment_method', 'my_save_later_payment_method');
add_action('wp_ajax_nopriv_save_later_payment_method', 'my_save_later_payment_method');

function my_save_later_payment_method() {
    if (!is_user_logged_in()) {
        wp_send_json_error([
            'message' => 'Please log in first to make payment and submit.',
        ]);
    }

    $customer_id = sanitize_text_field($_POST['customer_id']);
    $payment_method = sanitize_text_field($_POST['payment_method']);

    $user_id = get_current_user_id();
    update_user_meta($user_id, 'stripe_customer_id', $customer_id);
    update_user_meta($user_id, 'stripe_payment_method', $payment_method);

    wp_send_json_success([
        'message' => 'Payment method saved successfully.',
    ]);
}
