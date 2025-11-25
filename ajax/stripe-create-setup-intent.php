<?php

add_action('wp_ajax_create_setup_intent', 'my_create_setup_intent');
add_action('wp_ajax_nopriv_create_setup_intent', 'my_create_setup_intent');

function my_create_setup_intent()
{
    // Ensure user is logged in
    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'You must be logged in to continue.']);
        wp_die();
    }

    // Load dependencies
    require_once HLD_PLUGIN_PATH . 'vendor/autoload.php';
    \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

    // Get current user info
    $current_user = wp_get_current_user();
    $user_email   = $current_user->user_email;
    $first_name   = $current_user->first_name ?? '';
    $last_name    = $current_user->last_name ?? '';
    $user_name    = trim("$first_name $last_name");
    $description  = "Customer for GLP-1 Prefunnel: {$user_name} ({$user_email})";

    // ✅ Use your helper to get or create Stripe Customer properly
    $customer_id = HLD_Stripe::get_or_create_stripe_customer($user_email, $first_name, $last_name);

    if (empty($customer_id)) {
        wp_send_json_error(['message' => 'Unable to create or retrieve Stripe customer.']);
        wp_die();
    }

    try {
        // Create SetupIntent for this customer
        $setupIntent = \Stripe\SetupIntent::create([
            'customer' => $customer_id,
            'payment_method_types' => ['card'],
            'metadata' => [
                'plan' => 'glp_1_prefunnel',
                'source' => 'Healsend WordPress',
            ],
        ]);

        // ✅ Send client secret back to frontend
        wp_send_json_success([
            'clientSecret' => $setupIntent->client_secret,
            'customerId'   => $customer_id,
        ]);
    } catch (Exception $e) {
        wp_send_json_error(['message' => $e->getMessage()]);
    }

    wp_die();
}

add_action('wp_ajax_create_payment_intent', 'my_create_payment_intent');
add_action('wp_ajax_nopriv_create_payment_intent', 'my_create_payment_intent');

function my_create_payment_intent()
{
    // Ensure user is logged in
    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'You must be logged in to continue.']);
        wp_die();
    }

    // Load dependencies
    require_once HLD_PLUGIN_PATH . 'vendor/autoload.php';
    \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

    $bank = sanitize_text_field($_POST['for'] ?? '');
    // Get current user info
    $current_user = wp_get_current_user();
    $user_email   = $current_user->user_email;
    $first_name   = $current_user->first_name ?? '';
    $last_name    = $current_user->last_name ?? '';
    $user_name    = trim("$first_name $last_name");
    $description  = "Customer for GLP-1 Prefunnel: {$user_name} ({$user_email})";

    // ✅ Use your  to get or create Stripe Customer properly
    $customer_id = HLD_Stripe::get_or_create_stripe_customer($user_email, $first_name, $last_name);

    if (empty($customer_id)) {
        wp_send_json_error(['message' => 'Unable to create or retrieve Stripe customer.']);
        wp_die();
    }

    $intent_for = 'klarna';
    if ($bank == 'afterpay') {
        $intent_for = 'afterpay_clearpay';
    }

    try {
        // Create SetupIntent for this customer
        $paymentIntent = \Stripe\PaymentIntent::create([
            'payment_method_types' => [$intent_for],
            'amount' => 1099,
            'currency' => 'usd',
            'amount_details' => [
              'line_items' => [
                [
                  'product_name' => 'Your product name',
                  'unit_cost' => 1099,
                  'quantity' => 1,
                ],
              ],
            ],
            'shipping' => [
              'name' => 'Jenny Rosen',
              'address' => [
                'city' => 'Brothers',
                'country' => 'US',
                'line1' => '27 Fredrick Ave',
                'postal_code' => '97712',
                'state' => 'OR',
              ],
            ],
            'payment_method_data' => [
              'type' => $intent_for,
              'billing_details' => [
                'address' => [
                  'city' => 'Brothers',
                  'country' => 'US',
                  'line1' => '27 Fredrick Ave',
                  'postal_code' => '97712',
                  'state' => 'OR',
                ],
                'email' => 'jenny.rosen@example.com',
                'name' => 'Jenny Rosen',
              ],
            ],
          ]);

        wp_send_json_success([
            'clientSecret' => $paymentIntent->client_secret,
            'customerId'   => $customer_id,
        ]);
    } catch (Exception $e) {
        wp_send_json_error(['message' => $e->getMessage()]);
    }

    wp_die();
}
