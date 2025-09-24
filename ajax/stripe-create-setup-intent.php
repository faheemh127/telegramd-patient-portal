<?php
add_action('wp_ajax_create_setup_intent', 'my_create_setup_intent');
add_action('wp_ajax_nopriv_create_setup_intent', 'my_create_setup_intent');

function my_create_setup_intent()
{
    // Get current user info
    $current_user = wp_get_current_user();
    $user_name = $current_user->display_name ?: $current_user->user_login;
    $user_email = $current_user->user_email;
    $description = "Customer for GLP-1 Prefunnel: {$user_name} ({$user_email})";    // Check if user is logged in

    // Proceed only if logged in
    require_once HLD_PLUGIN_PATH . 'vendor/autoload.php';
    \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

    // Optionally: you could also store or reuse the Stripe customer ID from user meta
    $customer = \Stripe\Customer::create([
        'description' => $description,
    ]);

    $setupIntent = \Stripe\SetupIntent::create([
        'customer' => $customer->id,
        'payment_method_types' => ['card'],
    ]);

    wp_send_json_success([
        'clientSecret' => $setupIntent->client_secret,
        'customerId' => $customer->id,
    ]);
}
