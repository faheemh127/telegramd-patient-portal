<?php
// Subscribe Patient (auto-cancel after X months)
add_action('wp_ajax_subscribe_patient', 'hld_subscribe_patient_handler');
add_action('wp_ajax_nopriv_subscribe_patient', 'hld_subscribe_patient_handler');

function hld_subscribe_patient_handler()
{
    if (!isset($_POST['customer_id']) || !isset($_POST['payment_method'])) {
        wp_send_json_error(['message' => 'Missing parameters']);
        wp_die();
    }

    $customer_id     = !empty($_POST['customer_id'])
        ? sanitize_text_field($_POST['customer_id'])
        : 'cus_T7CEARDGatwPyC'; // fallback for testing
    $payment_method  = sanitize_text_field($_POST['payment_method']);
    $price_id        = sanitize_text_field($_POST['price_id']);
    $duration        = sanitize_text_field($_POST['duration']);
    $months          = (int) $duration;

    try {
        require_once HLD_PLUGIN_PATH . 'vendor/autoload.php';
        \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

        // Attach the payment method to the customer
        $pm = \Stripe\PaymentMethod::retrieve($payment_method);
        $pm->attach(['customer' => $customer_id]);
        error_log("stripe payment method 12". $pm);

        // Set as default payment method
        \Stripe\Customer::update($customer_id, [
            'invoice_settings' => ['default_payment_method' => $payment_method]
        ]);

        // Create subscription (cancel after N months)
        $subscription = \Stripe\Subscription::create([
            'customer' => $customer_id,
            'items' => [[
                'price' => $price_id,
            ]],
            'cancel_at' => strtotime("+{$months} months"),
            'expand' => ['latest_invoice.payment_intent'],
        ]);
        
        error_log("subscription 13". $subscription);

        /**
         * Store payment method in custom table
         */
        if (is_user_logged_in()) {
            $user_id    = get_current_user_id();
            $user_info  = get_userdata($user_id);
            $patient_email = $user_info->user_email;

            // Extract card details
            $card_last4  = isset($pm->card->last4) ? $pm->card->last4 : null;
            $card_brand  = isset($pm->card->brand) ? $pm->card->brand : null;

            // Save into custom payments table
            HLD_Patient::ensure_patient_by_email($patient_email);
            HLD_Payments::add_payment_method(
                $patient_email,
                $payment_method, // store Stripe PaymentMethod ID
                $card_last4,
                $card_brand
            );
            HLD_Telegra::create_patient();
        }

        wp_send_json_success([
            'subscription_id' => $subscription->id,
            'status' => $subscription->status,
        ]);
    } catch (Exception $e) {
        wp_send_json_error(['message' => $e->getMessage()]);
    }

    wp_die();
}
