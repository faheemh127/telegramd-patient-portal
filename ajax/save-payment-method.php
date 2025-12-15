<?php
add_action('wp_ajax_save_later_payment_method', 'my_save_later_payment_method');
add_action('wp_ajax_nopriv_save_later_payment_method', 'my_save_later_payment_method');

function my_save_later_payment_method()
{
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



add_action('wp_ajax_save_payment_method', 'hld_save_payment_method_handler');
add_action('wp_ajax_nopriv_save_payment_method', 'hld_save_payment_method_handler');

function hld_save_payment_method_handler()
{

    // Get payment method & email

    $patient_email  = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';

    if (empty($payment_method) || empty($patient_email)) {
        wp_send_json_error(['message' => 'Missing payment method or email']);
    }

    require_once HLD_PLUGIN_PATH . 'vendor/autoload.php';
    \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

    try {

        // Retrieve payment method
        $pm = \Stripe\PaymentMethod::retrieve($payment_method);

        // Extract card details
        $card_last4 = $pm->card->last4 ?? null;
        $card_brand = $pm->card->brand ?? null;

        // Save to DB
        HLD_Payments::add_payment_method(
            $patient_email,
            $payment_method,
            $card_last4,
            $card_brand
        );

        wp_send_json_success([
            'message' => 'Payment method saved successfully',
            'last4'   => $card_last4,
            'brand'   => $card_brand
        ]);
    } catch (Exception $e) {

        wp_send_json_error([
            'message' => 'Stripe error: ' . $e->getMessage()
        ]);
    }

    wp_die();
}
