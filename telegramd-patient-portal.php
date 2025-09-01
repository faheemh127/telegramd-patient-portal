<?php

/**
 * Plugin Name: TelegraMD Patient Portal
 * Description: Patient portal with TelegraMD API integration for prescriptions, labs, and subscriptions.
 * Version: 1.0
 * Author: Faheem
 * Author URI: https://faheemhassan.dev
 */


// Constants
define('TELEGRA_PATIENT_PORTAL_PATH', plugin_dir_path(__FILE__));

add_action('wp_ajax_create_payment_intent', 'my_create_payment_intent');
add_action('wp_ajax_nopriv_create_payment_intent', 'my_create_payment_intent');

function my_create_payment_intent()
{
    require_once __DIR__ . '/vendor/autoload.php';
    \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

    $intent = \Stripe\PaymentIntent::create([
        'amount' => 1000, // $10.00 in cents
        'currency' => 'usd',
    ]);

    wp_send_json_success([
        'clientSecret' => $intent->client_secret,
        'paymentIntentId' => $intent->id,
    ]);
}

add_action('wp_ajax_create_setup_intent', 'my_create_setup_intent');
add_action('wp_ajax_nopriv_create_setup_intent', 'my_create_setup_intent');
function my_create_setup_intent()
{
    // Check if user is logged in
    if (!is_user_logged_in()) {
        wp_send_json_error([
            'message' => 'You must be logged in to save a payment method.',
        ]);
    }

    // Proceed only if logged in
    require_once __DIR__ . '/vendor/autoload.php';
    \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

    // Optionally: you could also store or reuse the Stripe customer ID from user meta
    $customer = \Stripe\Customer::create([
        'description' => 'Customer for Pay Later',
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



require_once __DIR__ . '/vendor/autoload.php';

foreach (glob(plugin_dir_path(__FILE__) . 'helper/*.php') as $file) {
    require_once $file;
}

// Include All Necessary files

require_once plugin_dir_path(__FILE__) . 'includes/api-keys.php';
require_once plugin_dir_path(__FILE__) . 'includes/functions.php';
require_once plugin_dir_path(__FILE__) . 'classes/class-hld-user-orders.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-hld-user-notifications.php';
require_once plugin_dir_path(__FILE__) . 'classes/class-hld-assets.php';
require_once plugin_dir_path(__FILE__) . 'classes/class-telegra.php';
require_once plugin_dir_path(__FILE__) . 'classes/class-fluent-handler.php';
require_once plugin_dir_path(__FILE__) . 'classes/class-dashboard-shortcode.php';
require_once plugin_dir_path(__FILE__) . 'ajax/save-payment-method.php';
require_once plugin_dir_path(__FILE__) . 'ajax/log-payment-success.php';
require_once plugin_dir_path(__FILE__) . 'includes/shortcodes.php';
require_once plugin_dir_path(__FILE__) . 'includes/order-tracking-webhook.php';
require_once plugin_dir_path(__FILE__) . 'includes/prescription-received-webhook.php';
require_once plugin_dir_path(__FILE__) . 'includes/hooks.php';
require_once plugin_dir_path(__FILE__) . 'ajax/save-form-url.php';
require_once plugin_dir_path(__FILE__) . 'includes/patient-login.php';
require_once plugin_dir_path(__FILE__) . 'ajax/patient-login.php';

