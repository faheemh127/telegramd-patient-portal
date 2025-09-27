<?php
// Subscribe Patient (auto-cancel after 3 months)
add_action('wp_ajax_subscribe_patient', 'hld_subscribe_patient_handler');
add_action('wp_ajax_nopriv_subscribe_patient', 'hld_subscribe_patient_handler');

function hld_subscribe_patient_handler()
{
    if (!isset($_POST['customer_id']) || !isset($_POST['payment_method'])) {
        wp_send_json_error(['message' => 'Missing parameters']);
        wp_die();
    }

    $customer_id = isset($_POST['customer_id'])
        ? sanitize_text_field($_POST['customer_id'])
        : 'cus_T7CEARDGatwPyC'; // fallback for testing
    $payment_method = sanitize_text_field($_POST['payment_method']);
    $price_id = sanitize_text_field($_POST['price_id']);
    $duration = sanitize_text_field($_POST['duration']);
    $months = $duration;
    try {
        require_once HLD_PLUGIN_PATH . 'vendor/autoload.php';
        \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

        // Attach the payment method to the customer
        \Stripe\PaymentMethod::retrieve($payment_method)->attach(['customer' => $customer_id]);

        // Set as default payment method
        \Stripe\Customer::update($customer_id, [
            'invoice_settings' => ['default_payment_method' => $payment_method]
        ]);

        // Create subscription (cancel after 3 months)
        $subscription = \Stripe\Subscription::create([
            'customer' => $customer_id,
            'items' => [[
                'price' => $price_id,
            ]],
            'cancel_at' => strtotime("+{$months} months"),
            'expand' => ['latest_invoice.payment_intent'],
        ]);

        wp_send_json_success([
            'subscription_id' => $subscription->id,
            'status' => $subscription->status,
        ]);
    } catch (Exception $e) {
        wp_send_json_error(['message' => $e->getMessage()]);
    }

    wp_die();
}
