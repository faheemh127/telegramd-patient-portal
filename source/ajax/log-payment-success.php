<?php
// log payment success on server
add_action('wp_ajax_log_payment_success', 'my_log_payment_success');
add_action('wp_ajax_nopriv_log_payment_success', 'my_log_payment_success');
function my_log_payment_success()
{
    $payment_id = sanitize_text_field($_POST['payment_id']);
    error_log("Stripe payment succeeded. ID: $payment_id");
    wp_send_json_success();
}
