<?php

add_action('wp_ajax_hld_get_stripe_price_data', 'hld_get_stripe_price_data');
add_action('wp_ajax_nopriv_hld_get_stripe_price_data', 'hld_get_stripe_price_data');

function hld_get_stripe_price_data()
{
    if (empty($_POST['price_id'])) {
        wp_send_json_error(['message' => 'price_id missing']);
    }

    $price_id   = sanitize_text_field($_POST['price_id']);
    $promo_code = sanitize_text_field($_POST['promo_code'] ?? "");
    $duration   = intval($_POST['duration'] ?? 1);

    $response = HLD_Stripe::hld_calculate_stripe_price($price_id, $promo_code, $duration);

    if (!empty($response['error'])) {
        wp_send_json_error($response);
    }

    wp_send_json_success($response);
}
