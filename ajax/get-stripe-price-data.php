<?php

add_action('wp_ajax_hld_get_stripe_price_data', 'hld_get_stripe_price_data');
add_action('wp_ajax_nopriv_hld_get_stripe_price_data', 'hld_get_stripe_price_data');

function hld_get_stripe_price_data($should_send_json = true, $price_id = "", $promo_code = "", $duration = 1)
{
    if ($should_send_json && empty($_POST['price_id'])) {
        wp_send_json_error(['message' => 'price_id missing']);
    }
    if ($should_send_json) {
        $price_id   = sanitize_text_field($_POST['price_id']);
        $promo_code = sanitize_text_field($_POST['promo_code']);
    }

    require_once HLD_PLUGIN_PATH . 'vendor/autoload.php';
    \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

    try {
        // Get base price
        $price = \Stripe\Price::retrieve($price_id);
        $unit_amount = $price->unit_amount; // cents

        $data = [
            'price_id' => $price_id,
            'currency' => $price->currency,
            'original_amount'   => $unit_amount * $duration,
            'final_amount' => $unit_amount * $duration,
            'final_amount_display' => $unit_amount * $duration,
            'discount_applied' => '',
            'discount_display' => '',
            'message'  => 'Base price returned'
        ];

        if ($should_send_json && empty($promo_code)) {
            wp_send_json_success($data);
        } else {
            return $data;
        }

        // Retrieve promotion code
        $promo = \Stripe\PromotionCode::retrieve($promo_code);
        $coupon = $promo->coupon;

        // Calculate discount
        $discountAmount = 0;

        if ($should_send_json && !empty($coupon->percent_off)) {
            $discountAmount = ($unit_amount * $coupon->percent_off) / 100;
        } else {
            $discountAmount = ($unit_amount * $duration * $coupon->percent_off) / 100;
        }

        if ($should_send_json && !empty($coupon->amount_off)) {
            $discountAmount = $coupon->amount_off;
        } else {
            $discountAmount = $unit_amount * $duration - $coupon->amount_off;
        }

        if ($should_send_json) {
            $finalAmount = max($unit_amount - $discountAmount, 0);
        } else {
            $finalAmount = max($unit_amount - $discountAmount, 0);
        }

        $data = [
            'price_id' => $price_id,
            'currency' => $price->currency,
            'original_amount' => $unit_amount,
            'final_amount'    => $finalAmount,
            'final_amount_display' => number_format($finalAmount / 100, 2),
            'discount_applied' => $discountAmount,
            'discount_display' => number_format($discountAmount / 100, 2),
            'message'          => 'Price calculated manually (no upcoming or preview)'
        ];

        if ($should_send_json) {
            wp_send_json_success($data);
        } else {
            return $data;
        }
    } catch (Exception $e) {
        wp_send_json_error(['error' => $e->getMessage()]);
    }
}
