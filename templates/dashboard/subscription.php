<?php
global $hld_fluent_handler;
$current_user = wp_get_current_user();

if (!$current_user || empty($current_user->ID)) {
    hld_not_found("You must be logged in to view subscriptions.");
    return;
}

// Get ALL subscriptions (no parameter needed if you updated the function)
$subscriptions = HLD_UserSubscriptions::get_subscriptions();

if (empty($subscriptions) || !is_array($subscriptions)) {
    hld_not_found("You have no subscriptions yet.");
    return;
}
error_log(print_r($subscriptions, true));

?>

<h3 class="hld-subscription-title">Your Active Subscriptions</h3>

<?php foreach ($subscriptions as $subscription): ?>

    <?php
    // Generate nonce + hash per subscription
    $sub_nonce = wp_create_nonce('sub_nonce');
    $sub_hash = wp_hash($sub_nonce . $subscription['stripe_subscription_id']);
    $stripe_subscription = HLD_Stripe::get_subscription_details($subscription['stripe_subscription_id']);
    // error_log(print_r($stripe_subscription, true));

    // Subscription core
    $subscription_id        = $stripe_subscription->id;
    $subscription_status    = $stripe_subscription->status; // active, canceled, past_due
    $start_date             = $stripe_subscription->start_date;
    $current_period_start   = $stripe_subscription->items->data[0]->current_period_start;
    $current_period_end     = $stripe_subscription->items->data[0]->current_period_end;
    $cancel_at_period_end   = (bool) $stripe_subscription->cancel_at_period_end;
    $canceled_at            = $stripe_subscription->canceled_at;



    // Pricing & Plan
    $price_id               = $stripe_subscription->items->data[0]->price->id;
    $product_id             = $stripe_subscription->items->data[0]->price->product->id;
    $product_name           = $stripe_subscription->items->data[0]->price->product->name;

    $amount_cents           = $stripe_subscription->items->data[0]->price->unit_amount;
    $amount_monthly         = $amount_cents / 100;

    $currency               = strtoupper($stripe_subscription->currency);
    $billing_interval       = $stripe_subscription->items->data[0]->price->recurring->interval;
    $interval_count         = $stripe_subscription->items->data[0]->price->recurring->interval_count;





    // Payment method
    $payment_method_id      = $stripe_subscription->default_payment_method->id;

    $card_brand             = $stripe_subscription->default_payment_method->card->brand;
    $card_last4             = $stripe_subscription->default_payment_method->card->last4;
    $card_exp_month         = $stripe_subscription->default_payment_method->card->exp_month;
    $card_exp_year          = $stripe_subscription->default_payment_method->card->exp_year;
    $card_funding           = $stripe_subscription->default_payment_method->card->funding;
    $card_country           = $stripe_subscription->default_payment_method->card->country;

    $card_holder_name       = $stripe_subscription->default_payment_method->billing_details->name;
    $card_postal_code       = $stripe_subscription->default_payment_method->billing_details->address->postal_code;


    // Customer
    $stripe_customer_id     = $stripe_subscription->customer;
    $customer_email         = $stripe_subscription->latest_invoice->customer_email;


    // Last Invoice (Very important)
    $invoice_id             = $stripe_subscription->latest_invoice->id;
    $invoice_status         = $stripe_subscription->latest_invoice->status;

    $invoice_total_cents    = $stripe_subscription->latest_invoice->total;
    $invoice_total          = $invoice_total_cents / 100;

    $invoice_subtotal       = $stripe_subscription->latest_invoice->subtotal / 100;
    $invoice_discount       = $stripe_subscription->latest_invoice->total_discount_amounts[0]->amount / 100 ?? 0;

    $invoice_pdf_url        = $stripe_subscription->latest_invoice->invoice_pdf;
    $hosted_invoice_url     = $stripe_subscription->latest_invoice->hosted_invoice_url;

    $invoice_paid_at        = $stripe_subscription->latest_invoice->status_transitions->paid_at;


    // Discounts/ Promo
    $has_discount           = !empty($stripe_subscription->latest_invoice->discounts);
    $discount_amount        = $invoice_discount;



    // Upcoming payments
    $next_payment_timestamp = $current_period_end;
    $next_payment_amount    = $amount_monthly; // unless prorations applied later


    // Pause / Cancelation Control
    $is_paused              = !empty($stripe_subscription->pause_collection);
    $is_scheduled_cancel    = !empty($stripe_subscription->cancel_at);

    ?>

    <div class="w-100 hld-card hld-subscription-card" style="margin-bottom: 25px;">
        <div class="card-body hld-card-body">
            <div class="row">
                <?php if ($subscription['subscription_status'] === 'active') : ?>
                    <!-- Cancel button hidden in your existing code -->
                <?php endif; ?>
            </div>

            <style>

            </style>


            <div class="hld-subscription">
                <div class="left">
                    <div class="sub-title">Subscription <span class="status <?php echo $subscription_status; ?>"><?php echo $subscription_status; ?></span></div>
                    <div class="title"> <?php echo esc_html($subscription['medication_name']); ?></div>
                    <span>started from <?php echo date("jS M Y", $subscription['subscription_start']); ?></span>
                </div>
                <div class="right">
                    <span class="sub-title">Price</span>
                    <div class="title"><span>$<?php echo number_format($amount_monthly, 2); ?></span>/month</div>
                    <span>(billed every 3 months)</span>
                </div>
            </div>





            <?php
            include HLD_PLUGIN_PATH . 'templates/dashboard/subscription/payment-method.php';
            include HLD_PLUGIN_PATH . 'templates/dashboard/subscription/last-invoice.php';
            include HLD_PLUGIN_PATH . 'templates/dashboard/subscription/info.php';
            ?>






            <?php if (!empty($subscription['invoice_pdf_url'])) : ?>
                <div class="row hld-row mt-3" style="margin-left: auto; margin-right: auto; margin-top: 20px;">
                    <div class="hld-invoice-btns-wrap">

                        <a class="hld-view-invoice btn btn-primary"
                            href="<?php echo esc_url($subscription['invoice_pdf_url']); ?>"
                            target="_blank">
                            View Last Invoice
                        </a>

                        <?php //if (isset($subscription["refund_status"]) && $subscription["refund_status"] == "requested") : 
                        ?>

                        <span style="display: none;" class="hld-view-invoice btn btn-primary"
                            id="hld-revoke-sub"
                            sub-nonce="<?php echo $sub_nonce; ?>"
                            data="<?php echo $sub_hash . explode('_', $subscription['stripe_subscription_id'])[1]; ?>">
                            Revoke Subscription
                        </span>

                        <?php // endif; 
                        ?>

                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

<?php endforeach; ?>