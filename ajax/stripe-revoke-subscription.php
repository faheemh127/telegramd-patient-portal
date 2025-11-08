<?php

// Subscribe Patient (auto-cancel after X months)
add_action('wp_ajax_revoke_patient_subscription', 'hld_revoke_patient_subscription');
add_action('wp_ajax_nopriv_revoke_patient_subscription', 'hld_subscribe_patient_handler');

function hld_revoke_patient_subscription()
{

    global $wpdb;
    $table = $wpdb->prefix . 'healsend_subscriptions';

    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'Internal Server Error']);
        wp_die();
    }

    if (!isset($_POST['data']) || !isset($_POST['nonce'])) {
        wp_send_json_error(['message' => 'Missing parameters']);
        wp_die();
    }

    require_once HLD_PLUGIN_PATH . 'vendor/autoload.php';
    \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

    $data = sanitize_text_field($_POST['data']);
    $nonce = sanitize_text_field($_POST['nonce']);

    if (!wp_verify_nonce($nonce, 'sub_nonce')) {
        wp_send_json_error(['message' => 'Security check failed.']);
        wp_die();
    }

    $subscription_id = "sub_". substr($data, 32);
    $hash = substr($data, 0, 32);
    $user_id = get_current_user_id();

    $subscription = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT * FROM {$table} WHERE stripe_subscription_id = %s",
            $subscription_id
        ),
    );

    if ($subscription->user_id !== "" . $user_id) {
        wp_send_json_error(['message' => 'Internal server error.']);
        wp_die();
    }

    try {
        $stripe_subscription = \Stripe\Subscription::retrieve($subscription_id);
        $stripe_subscription->cancel();
        $invoice = \Stripe\Invoice::retrieve($stripe_subscription->latest_invoice);
        if ($invoice) {
            $invoice_payment = \Stripe\InvoicePayment::all(['invoice' => $stripe_subscription->latest_invoice]);
            $payment_intent_id = $invoice_payment->data[0]->payment->payment_intent;
            if ($payment_intent_id) {
                $refund = \Stripe\Refund::create(['payment_intent' => $payment_intent_id]);
            } else {
                echo "No payment found for this subscription.\n";
            }

            wp_send_json_success([
                'subscription_id' => $subscription->id,
                'status' => $subscription->status,
                'customer_id' => $customer_id,
            ]);
        }
    } catch (Exception $e) {
        wp_send_json_error(['message' => $e->getMessage()]);
    }

    wp_die();
}
