<?php
add_action('wp_ajax_hld_get_stripe_price', 'hld_get_stripe_price');
add_action('wp_ajax_nopriv_hld_get_stripe_price', 'hld_get_stripe_price');

function hld_get_stripe_price() {
    if (empty($_POST['price_id'])) {
        wp_send_json_error(['message' => 'Missing price_id']);
        return;
    }

    $price_id = sanitize_text_field($_POST['price_id']);

    require_once HLD_PLUGIN_PATH . 'vendor/autoload.php';
    \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

    try {
        $price = \Stripe\Price::retrieve($price_id);
        $amount = isset($price->unit_amount) ? $price->unit_amount / 100 : 0;
        $currency = isset($price->currency) ? strtoupper($price->currency) : 'USD';

        wp_send_json_success([
            'amount' => $amount,
            'currency' => $currency,
            'interval' => $price->recurring->interval ?? 'month',
        ]);
    } catch (Exception $e) {
        wp_send_json_error(['message' => $e->getMessage()]);
    }
}
