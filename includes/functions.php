<?php

if (!function_exists('hld_log')) {
    function hld_log($message)
    {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            if (is_array($message) || is_object($message)) {
                error_log(print_r($message, true));
            } else {
                error_log($message);
            }
        }
    }
}



function hld_action_item($title, $msg, $link)
{
?>
    <div class="hld_action_item_wrap">
        <p class="title"><?php echo  $title; ?></p>
        <p class="desc"><?php echo $msg;  ?></p>
        <a href="<?php echo  $link; ?>">Complete Visit</a>
    </div>
<?php
}
function hld_not_found($msg)
{
    // Build a dynamic URL for the "Find a Treatment" page
    $treatment_url = home_url('/glp-1-prefunnel/'); // relative path

?>
    <div class="hld_no_found_wrap">
        <p><?php echo esc_html($msg); ?></p>
        <a href="<?php echo esc_url($treatment_url); ?>">Find a Treatment</a>
    </div>
<?php
}



function get_user_id_by_telegra_patient_id($patient_id)
{
    if (empty($patient_id)) {
        return null;
    }

    // Search for user meta where meta_value = $patient_id and meta_key LIKE 'hld_patient_%_telegra_id'
    global $wpdb;

    $meta_key_like = 'hld_patient_%_telegra_id';

    $user_id = $wpdb->get_var($wpdb->prepare(
        "
        SELECT user_id
        FROM $wpdb->usermeta
        WHERE meta_key LIKE %s
        AND meta_value = %s
        LIMIT 1
        ",
        $meta_key_like,
        $patient_id
    ));

    return $user_id ? intval($user_id) : null;
}






function hld_charge_later($user_id, $amount_in_dollars)
{
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


function hld_should_display_dashboard_nav()
{
    if (isset($_GET['upload-id'])) {
        return false; // Show dashboard nav if both are not set
    } elseif (isset($_GET['some-other-param'])) {
        return false; // Example: handle another URL param
    } else {
        return true; // Default case
    }
}
