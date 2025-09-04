<?php
global $hld_fluent_handler;
$get_patient_package = $hld_fluent_handler->get_patient_package();
if ($get_patient_package == null) {
    hld_not_found("You have no subscriptions yet.");
} else {


?>
    <!-- Bootstrap full-width card -->
    <h3 class="hld-subscription-title">Your Subscription Overview</h3>
    <div class="w-100 hld-card hld-subscription-card">
        <div class="card-body hld-card-body">
            <div class="row">
                <button class="btn_payment_method btn_edit_settings hld_btn_edit_profile max-w-150 hld_btn_cancel">Cancel Subscription</button>
            </div>
            <div class="row mb-3 hld-row">
                <div class="col-md-6 hld-col">
                    <p class="hld-text"><strong class="hld-label">Package Name:</strong> Premium Health Plan</p>
                    <p class="hld-text"><strong class="hld-label">Medication:</strong> Semaglutide</p>
                    <p class="hld-text"><strong class="hld-label">Start Date:</strong> 2025-01-01</p>
                    <p class="hld-text"><strong class="hld-label">End Date:</strong> 2025-12-31</p>
                </div>
                <div class="col-md-6 hld-col">
                    <p class="hld-text"><strong class="hld-label">Monthly Price:</strong> $199</p>
                    <p class="hld-text"><strong class="hld-label">Next Payment Due:</strong> 2025-10-01</p>
                    <p class="hld-text"><strong class="hld-label">Pending Amount:</strong> $199</p>
                    <p class="hld-text"><strong class="hld-label">Status:</strong> Active</p>
                </div>
            </div>
            <hr class="hld-divider">
            <div class="row hld-row">
                <div class="col-md-6 hld-col">
                    <p class="hld-text"><strong class="hld-label">Doctor Assigned:</strong> Dr. Sarah Johnson</p>
                </div>
                <div class="col-md-6 hld-col">
                    <p class="hld-text"><strong class="hld-label">Support Contact:</strong> support@healthcare.com</p>
                </div>
            </div>
            <div class="row hld-row">
                <span class="hld-last-updated">Last updated: 2nd Sep 2025</span>
            </div>
        </div>
    </div>

<?php } ?>