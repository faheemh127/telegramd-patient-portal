<?php

add_action('wp_ajax_cancel_card_reminders', 'hld_cancel_card_reminders');
add_action('wp_ajax_nopriv_cancel_card_reminders', 'hld_cancel_card_reminders');

function hld_cancel_card_reminders()
{
    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'You must be logged in.']);
        wp_die();
    }

    $payment_method = isset($_POST['pm_id']) ? sanitize_text_field($_POST['pm_id']) : '';

    $current_user = wp_get_current_user();
    $patient_email = $current_user->user_email;

    require_once HLD_PLUGIN_PATH . 'vendor/autoload.php';
    \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

    try {
        $pm = \Stripe\PaymentMethod::retrieve($payment_method);

        $card_last4 = $pm->card->last4 ?? null;
        $card_brand = $pm->card->brand ?? null;

        HLD_Payments::add_payment_method(
            $patient_email,
            $payment_method,
            $card_last4,
            $card_brand
        );
    } catch (Exception $e) {
        wp_send_json_error([
            'message' => 'Stripe error: ' . $e->getMessage()
        ]);
    }

    $status = HLD_Patient::cancel_email_reminders_to_add_card();

    
    if ($status) {
        wp_send_json_success(['success' => true, 'message' => 'Reminders cancelled and card saved for later use.']);
    } else {
        wp_send_json_error(['success' => false, 'message' => 'Internal Server Error.']);
    }

    wp_die();
}
