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

    $duration = isset($_POST['duration']) ? sanitize_text_field($_POST['duration']) : '';
    $price_id = isset($_POST['price']) ? sanitize_text_field($_POST['price']) : '';
    $bank = sanitize_text_field($_POST['for'] ?? '');

    if (empty($duration) || empty($price_id)) {
        wp_send_json_error(['message' => 'Error processing your payment.']);
        wp_die();
    }

    $current_user = wp_get_current_user();
    $user_email   = $current_user->user_email;
    $first_name   = $current_user->first_name ?? '';
    $last_name    = $current_user->last_name ?? '';
    $user_name    = trim("$first_name $last_name");

    $details = fetch_stripe_product_details($price_id);
    $description  = "Customer for {$details['title']}: {$user_name} ({$user_email})";

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

    $is_first_order = is_user_order_first($user_email);
    $price = $details['price'] * $duration;
    $discount = 12;

    if (is_first_order) {
        $price = $details['price'] * $duration;
        $price -= $price * (12 / 100);
    }

    try {
        // Create SetupIntent for this customer
        $paymentIntent = \Stripe\PaymentIntent::create([
          'payment_method_types' => [$intent_for],
          'customer' => $customer_id,
          'amount' => $price,
          'currency' => 'usd',
          'amount_details' => [
            'line_items' => [
              [
                'product_name' => $details['title'],
                'unit_cost' => $details['formatted'],
                'quantity' => $duration,
              ],
            ],
          ],
          'shipping' => [
            'name' => $user_name,
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
                'city' => 'Faisalabad',
                'country' => 'US',
                'line1' => '27 Fredrick Ave',
                'postal_code' => '97712',
                'state' => 'OR',
              ],
              'email' => $user_email,
              'name' => $user_name,
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

function is_user_order_first($email)
{
    return HLD_Patient::is_patient_new($email);
}


function fetch_stripe_product_details($price_id)
{
    require_once HLD_PLUGIN_PATH . 'vendor/autoload.php';
    \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

    try {
        $price = \Stripe\Price::retrieve($price_id, [
          'expand' => ['product']
        ]);

        if (!$price->active) {
            throw new Exception("Product is archived");
        }

        return [
          'title'       => $price->product->name,
          'description' => $price->product->description,
          'price'   => $price->unit_amount,
          'currency'    => strtoupper($price->currency),
          'formatted'   => number_format($price->unit_amount / 100, 2) . ' ' . strtoupper($price->currency)
        ];
    } catch (\Exception $e) {
        wp_send_json_error([
          'message' => 'Invalid product',
          'error'   => $e->getMessage()
        ], 400);
    }
}
