<?php
function get_telegra_patient_id_for_current_user()
{
    if (!is_user_logged_in()) {
        return null;
    }

    $user_id = get_current_user_id();
    $meta_key = 'hld_patient_' . $user_id . '_telegra_id';

    $patient_id = get_user_meta($user_id, $meta_key, true);

    return !empty($patient_id) ? $patient_id : null;
}


// $telegra_id =   get_telegra_patient_id_for_current_user();
// echo "telegraid";
// print_r($telegra_id);



function hld_charge_later($user_id, $amount_in_dollars) {
    $customer_id = get_user_meta($user_id, 'stripe_customer_id', true);
    $payment_method = get_user_meta($user_id, 'stripe_payment_method', true);

    if (!$customer_id || !$payment_method) {
        error_log("Missing payment info for user $user_id");
        return false;
    }

    \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

    try {
        $paymentIntent = \Stripe\PaymentIntent::create([
            'amount' => $amount_in_dollars * 100, // cents
            'currency' => 'usd',
            'customer' => $customer_id,
            'payment_method' => $payment_method,
            'off_session' => true,
            'confirm' => true,
        ]);

        return $paymentIntent;
    } catch (\Stripe\Exception\CardException $e) {
        error_log("Card declined: " . $e->getMessage());
        return false;
    }
}
