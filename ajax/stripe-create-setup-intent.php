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
  $product_name = isset($_POST['product_name']) ? sanitize_text_field($_POST['product_name']) : '';

  // Read shipping info JSON
  $shipping_json = isset($_POST['shipping_info']) ? wp_unslash($_POST['shipping_info']) : '';
  $shipping_data = json_decode($shipping_json, true);

  // Store values
  $street = $shipping_data['street_address'] ?? '';
  $city   = $shipping_data['city'] ?? '';
  $zip    = $shipping_data['zip'] ?? '';
  $state  = $shipping_data['state'] ?? '';


  $bank = sanitize_text_field($_POST['for'] ?? '');

  $price_id = "price_1SVPuyAcgi1hKyLWgjOYvTcm";
  $duration = 3;

  if (empty($duration) || empty($price_id)) {
    wp_send_json_error(['message' => 'Error processing your payment.']);
    wp_die();
  }

  $current_user = wp_get_current_user();
  $user_email   = $current_user->user_email;
  // Get first/last name from user meta
  $first_name = get_user_meta($current_user->ID, 'first_name', true);
  $last_name  = get_user_meta($current_user->ID, 'last_name', true);

  // Fallback: if first+last empty, use display_name or username
  $user_name = trim("$first_name $last_name");

  if (empty($user_name)) {
    $user_name = $current_user->display_name ?: $current_user->user_login;
  }




  $details = fetch_stripe_product_details($price_id);

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
  error_log("Price was" . $price);
  error_log($product_name);
  error_log($duration);
  $discount =  HLD_Discount::getDiscount($product_name, $duration);
  error_log("Discount is " . $discount);

  $calculated_discount = 0;
  if ($is_first_order) {
    $price = $details['price'] * $duration;
    $calculated_discount = $price * ($discount / 100);
    $price -= $calculated_discount;
    error_log("price after discount" . $price);
  }
  //

  try {
    // Create SetupIntent for this customer
    $paymentIntent = \Stripe\PaymentIntent::create([
      'payment_method_types' => [$intent_for],
      'customer' => $customer_id,
      'amount' => $price,
      'currency' => HLD_CURRENCY,
      'amount_details' => [
        'line_items' => [
          [
            'product_name' => $details['title'],
            'unit_cost' =>   $details['price'],
            'quantity' => $duration,
            'discount_amount'   => intval($calculated_discount),
          ],
        ],
      ],
      'shipping' => [
        'name' => $user_name,
        'address' => [
          'city' => $city,
          'country' => HLD_BUISNESS_OPERATIONAL_COUNTRY,
          'line1' => $street,
          'postal_code' => $zip,
          'state' => $state,
        ],
      ],
      'payment_method_data' => [
        'type' => $intent_for,
        'billing_details' => [
          'address' => [
            'city' => $city,
            'country' => HLD_BUISNESS_OPERATIONAL_COUNTRY,
            'line1' => $street,
            'postal_code' => $zip,
            'state' => $state,
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
    error_log("Error in stripe setup intent");
    // error_log(print_r($e, true));
    error_log(print_r($e->getMessage(), true));
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
    // Retrieve the price
    $price = \Stripe\Price::retrieve($price_id);

    if (!$price->active) {
      throw new Exception("Price is inactive or archived");
    }

    // Retrieve the product if only ID is returned
    if (is_string($price->product)) {
      $product = \Stripe\Product::retrieve($price->product);
    } else {
      $product = $price->product;
    }

    return [
      'title'       => isset($product->name) ? $product->name : '',
      'description' => isset($product->description) ? $product->description : '',
      'price'       => isset($price->unit_amount) ? $price->unit_amount : 0,
      'currency'    => isset($price->currency) ? strtoupper($price->currency) : '',
      'formatted'   => isset($price->unit_amount, $price->currency)
        ? number_format($price->unit_amount / 100, 2) . ' ' . strtoupper($price->currency)
        : ''
    ];
  } catch (\Exception $e) {
    wp_send_json_error([
      'message' => 'Invalid product',
      'error'   => $e->getMessage()
    ], 400);
  }
}
