<?php
add_action('wp_ajax_charge_now', 'hld_charge_now_handler');
add_action('wp_ajax_nopriv_charge_now', 'hld_charge_now_handler');

function hld_charge_now_handler()
{
    if (!isset($_POST['customer_id']) || !isset($_POST['payment_method'])) {
        wp_send_json_error(['message' => 'Missing parameters']);
        wp_die();
    }

    $customer_id = sanitize_text_field($_POST['customer_id']);
    $payment_method = sanitize_text_field($_POST['payment_method']);
    $amount = sanitize_text_field($_POST['amount']);
    $amount_in_cents = $amount * 100;

    try {
        // Initialize Stripe with your secret key
        require_once HLD_PLUGIN_PATH . 'vendor/autoload.php';
        \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

        $paymentIntent = \Stripe\PaymentIntent::create([
            'amount' => $amount_in_cents, // Amount in cents, adjust as needed
            'currency' => 'usd',
            'customer' => $customer_id,
            'payment_method' => $payment_method,
            'off_session' => true,
            'confirm' => true,
        ]);

        wp_send_json_success([
            'payment_intent' => $paymentIntent->id
        ]);
    } catch (Exception $e) {
        wp_send_json_error(['message' => $e->getMessage()]);
    }

    wp_die();
}
