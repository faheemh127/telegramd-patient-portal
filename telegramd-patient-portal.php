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
define("HLD_DEVELOPER_ENVIRONMENT", true);
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
// add_action('wp_ajax_nopriv_create_setup_intent', 'my_create_setup_intent');
function my_create_setup_intent()
{

    // Check if user is logged in
    // if (!is_user_logged_in()) {
    //     wp_send_json_error([
    //         'message' => 'You must be logged in to save a payment method.',
    //     ]);
    //     wp_die(); // Stop execution
    // }



    // Get current user info
    $current_user = wp_get_current_user();
    $user_name = $current_user->display_name ?: $current_user->user_login;
    $user_email = $current_user->user_email;

    // Dynamic description
    $description = "Customer for GLP-1 Prefunnel: {$user_name} ({$user_email})";    // Check if user is logged in

    // Proceed only if logged in
    require_once __DIR__ . '/vendor/autoload.php';
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


// Pay Upfront
add_action('wp_ajax_charge_now', 'hld_charge_now_handler');
add_action('wp_ajax_nopriv_charge_now', 'hld_charge_now_handler');

function hld_charge_now_handler()
{
    if (!isset($_POST['customer_id']) || !isset($_POST['payment_method'])) {
        wp_send_json_error(['message' => 'Missing parameters']);
        wp_die();
    }

    $customer_id = sanitize_text_field($_POST['customer_id']);
    $payment_method = sanitize_text_field($_POST['payment_method']);
    $amount = sanitize_text_field($_POST['amount']);
    $amount_in_cents = $amount * 100;
    try {
        // Initialize Stripe with your secret key
        \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

        $paymentIntent = \Stripe\PaymentIntent::create([
            'amount' => $amount_in_cents, // Amount in cents, adjust as needed
            'currency' => 'usd',
            'customer' => $customer_id,
            'payment_method' => $payment_method,
            'off_session' => true,
            'confirm' => true,
        ]);

        wp_send_json_success([
            'payment_intent' => $paymentIntent->id
        ]);
    } catch (Exception $e) {
        wp_send_json_error(['message' => $e->getMessage()]);
    }

    wp_die();
}




// // Subscribe Patient (auto-cancel after 3 months)
// add_action('wp_ajax_subscribe_patient', 'hld_subscribe_patient_handler');
// add_action('wp_ajax_nopriv_subscribe_patient', 'hld_subscribe_patient_handler');

// function hld_subscribe_patient_handler()
// {
//     if (!isset($_POST['customer_id']) || !isset($_POST['payment_method']) || !isset($_POST['price_id'])) {
//         wp_send_json_error(['message' => 'Missing parameters']);
//         wp_die();
//     }

//     $customer_id    = sanitize_text_field($_POST['customer_id']);
//     $payment_method = sanitize_text_field($_POST['payment_method']);
//     // $price_id       = sanitize_text_field($_POST['price_id']); // recurring price ID (price_xxx)
//     $price_id = "prod_T6gWAGad8vzvqU";

//     try {
//         \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

//         // Attach the payment method to the customer
//         \Stripe\PaymentMethod::attach($payment_method, ['customer' => $customer_id]);

//         // Set as default payment method
//         \Stripe\Customer::update($customer_id, [
//             'invoice_settings' => ['default_payment_method' => $payment_method]
//         ]);

//         // Create subscription (cancel after 3 months)
//         $subscription = \Stripe\Subscription::create([
//             'customer' => $customer_id,
//             'items' => [[
//                 'price' => $price_id,
//             ]],
//             'cancel_at' => strtotime("+3 months"), // auto cancel after 3 months
//             'expand' => ['latest_invoice.payment_intent'],
//         ]);

//         wp_send_json_success([
//             'subscription_id' => $subscription->id,
//             'status' => $subscription->status,
//         ]);
//     } catch (Exception $e) {
//         wp_send_json_error(['message' => $e->getMessage()]);
//     }

//     wp_die();
// }






require_once __DIR__ . '/vendor/autoload.php';
foreach (glob(plugin_dir_path(__FILE__) . 'helper/*.php') as $file) {
    require_once $file;
}

// Include All Necessary files
require_once plugin_dir_path(__FILE__) . 'includes/api-keys.php';
require_once plugin_dir_path(__FILE__) . 'classes/class-hld-settings.php';
require_once plugin_dir_path(__FILE__) . 'includes/functions.php';
require_once plugin_dir_path(__FILE__) . 'classes/class-hld-user-orders.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-hld-user-notifications.php';
require_once plugin_dir_path(__FILE__) . 'classes/class-hld-assets.php';
require_once plugin_dir_path(__FILE__) . 'classes/class-telegra.php';
require_once plugin_dir_path(__FILE__) . 'classes/class-fluent-handler.php';
require_once plugin_dir_path(__FILE__) . 'classes/class-patient.php';
require_once plugin_dir_path(__FILE__) . 'classes/class-dashboard-shortcode.php';
require_once plugin_dir_path(__FILE__) . 'ajax/save-payment-method.php';
require_once plugin_dir_path(__FILE__) . 'ajax/log-payment-success.php';
require_once plugin_dir_path(__FILE__) . 'includes/shortcodes.php';
require_once plugin_dir_path(__FILE__) . 'includes/order-tracking-webhook.php';
require_once plugin_dir_path(__FILE__) . 'includes/prescription-received-webhook.php';
require_once plugin_dir_path(__FILE__) . 'includes/hooks.php';
require_once plugin_dir_path(__FILE__) . 'ajax/save-form-url.php';
require_once plugin_dir_path(__FILE__) . 'includes/patient-login.php';
require_once plugin_dir_path(__FILE__) . 'includes/patient-signup.php';
require_once plugin_dir_path(__FILE__) . 'ajax/patient-login.php';
