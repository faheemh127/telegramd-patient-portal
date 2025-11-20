<?php
global $hld_fluent_handler;
$current_user = wp_get_current_user();

if (!$current_user || empty($current_user->ID)) {
    hld_not_found("You must be logged in to view subscriptions.");
    return;
}

$subscription = HLD_UserSubscriptions::get_user_subscription($current_user->ID);
if (!is_array($subscription) || empty($subscription['stripe_subscription_id'])) {
    error_log('Subscription data missing or invalid.');
    return; // or handle however you want
}

$sub_nonce = wp_create_nonce('sub_nonce');
$sub_hash = wp_hash($sub_nonce . $subscription['stripe_subscription_id']);

// error_log("[SubNonce issue]" . $sub_nonce);
// error_log(print_r($subscription, true));

if ($subscription == null) {
    hld_not_found("You have no subscriptions yet.");
} else {
?>
    <h3 class="hld-subscription-title">Your Subscription Overview</h3>
    <div class="w-100 hld-card hld-subscription-card">
        <div class="card-body hld-card-body">
            <div class="row">
                <?php if ($subscription['subscription_status'] === 'active') : ?>
                    <!-- <button class="btn_payment_method btn_edit_settings hld_btn_edit_profile max-w-150 hld_btn_cancel">
                        Cancel Subscription
                    </button> -->
                <?php endif; ?>
            </div>
            <div class="row mb-3 hld-row">
                <div class="col-md-6 hld-col">
                    <p class="hld-text"><strong class="hld-label">Package Name:</strong>
                        <?php echo esc_html($subscription['subscription_duration']); ?></p>
                    <p class="hld-text"><strong class="hld-label">Medication:</strong>
                        <?php echo esc_html($subscription['medication_name']); ?></p>
                    <p class="hld-text"><strong class="hld-label">Start Date:</strong>
                        <?php echo date("jS M Y", $subscription['subscription_start']); ?>

                    <p class="hld-text"><strong class="hld-label">End Date:</strong>
                        <?php echo $subscription['subscription_end'] ? date("jS M Y", $subscription['subscription_end']) : "Ongoing"; ?>
                    </p>
                </div>
                <div class="col-md-6 hld-col">
                    <p class="hld-text"><strong class="hld-label">Monthly Price:</strong>
                        $<?php echo number_format($subscription['subscription_monthly_amount'], 2); ?></p>
                    <p class="hld-text"><strong class="hld-label">Next Payment Due:</strong>
                        <?php echo date("jS M Y", strtotime("+1 month", $subscription['subscription_start'])); ?></p>
                    <p class="hld-text"><strong class="hld-label">Status:</strong>
                        <?php echo ucfirst($subscription['subscription_status']); ?></p>
                </div>
            </div>

            <?php if (!empty($subscription['invoice_pdf_url'])) : ?>
                <div class="row hld-row mt-3" style="margin-left: auto;margin-right: auto; margin-top: 20px; ">
                    <div class="hld-invoice-btns-wrap">
                        <a class="hld-view-invoice btn btn-primary" href="<?php echo esc_url($subscription['invoice_pdf_url']); ?>" target="_blank">
                            View Last Invoice
                        </a>


                        <span class="hld-view-invoice btn btn-primary" id="hld-revoke-sub" sub-nonce="<?php echo $sub_nonce;  ?>" data="<?php echo $sub_hash . explode('_', $subscription['stripe_subscription_id'])[1]; ?>">
                            Revoke Subscription
                        </span>

                    </div>

                </div>
            <?php endif; ?>
        </div>
    </div>
<?php } ?>