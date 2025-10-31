<?php
global $hld_fluent_handler;
$current_user = wp_get_current_user();

if (!$current_user || empty($current_user->ID)) {
    hld_not_found("You must be logged in to view subscriptions.");
    return;
}

$subscription = HLD_UserSubscriptions::get_user_subscription($current_user->ID);

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
                        <?php echo date("Y-m-d", $subscription['subscription_start']); ?></p>
                    <p class="hld-text"><strong class="hld-label">End Date:</strong>
                        <?php echo $subscription['subscription_end'] ? date("Y-m-d", $subscription['subscription_end']) : "Ongoing"; ?></p>
                </div>
                <div class="col-md-6 hld-col">
                    <p class="hld-text"><strong class="hld-label">Monthly Price:</strong>
                        $<?php echo number_format($subscription['subscription_monthly_amount'], 2); ?></p>
                    <p class="hld-text"><strong class="hld-label">Next Payment Due:</strong>
                        <?php echo date("Y-m-d", strtotime("+1 month", $subscription['subscription_start'])); ?></p>
                    <p class="hld-text"><strong class="hld-label">Pending Amount:</strong>
                        $<?php echo number_format($subscription['subscription_monthly_amount'], 2); ?></p>
                    <p class="hld-text"><strong class="hld-label">Status:</strong>
                        <?php echo ucfirst($subscription['subscription_status']); ?></p>
                </div>
            </div>
            <hr class="hld-divider">
            <div class="row hld-row">
                <div class="col-md-6 hld-col">
                    <p class="hld-text"><strong class="hld-label">Doctor Assigned:</strong>
                        <?php echo !empty($subscription['doctor_name']) ? esc_html($subscription['doctor_name']) : "Not assigned"; ?></p>
                </div>
                <div class="col-md-6 hld-col">
                    <p class="hld-text"><strong class="hld-label">Support Contact:</strong>
                        <?php echo !empty($subscription['support_email']) ? esc_html($subscription['support_email']) : get_bloginfo('admin_email'); ?></p>
                </div>
            </div>
            <div class="row hld-row">
                <span class="hld-last-updated">Last updated:
                    <?php echo date("jS M Y", $subscription['subscription_start']); ?>
                </span>
            </div>

            <?php if (!empty($subscription['invoice_pdf_url'])) : ?>
                <div class="row hld-row mt-3" style="margin-left: auto;margin-right: auto; margin-top: 20px; ">
                    <a class="hld-view-invoice btn btn-primary" href="<?php echo esc_url($subscription['invoice_pdf_url']); ?>" target="_blank">
                        View Last Invoice
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php } ?>