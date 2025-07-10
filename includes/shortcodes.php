<?php
// Register Shortcode [stripe_payment_form]
function my_stripe_payment_form_shortcode() {
    ob_start();
    ?>
    <div id="stripe-form-container">
        <div id="card-element"></div>
        <div id="card-errors" style="color:red; margin-top:10px;"></div>
        <input type="hidden" id="payment_intent_id" value="">
        <button id="testStripePayment">Pay Now</button>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('stripe_payment_form', 'my_stripe_payment_form_shortcode');