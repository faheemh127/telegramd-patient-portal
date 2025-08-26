<?php
/**
 * Plugin Name: TelegraMD Patient Portal
 * Description: Patient portal with TelegraMD API integration for prescriptions, labs, and subscriptions.
 * Version: 1.0
 * Author: Faheem
 * Author URI: https://faheemhassan.dev
 */


// Include API
// require_once plugin_dir_path(__FILE__) . 'telegramd-api.php';
// // Register shortcodes
// require_once plugin_dir_path(__FILE__) . 'shortcodes.php';
// // Admin settings page
// require_once plugin_dir_path(__FILE__) . 'admin-settings.php';


define( 'TELEGRA_PATIENT_PORTAL_PATH', plugin_dir_path( __FILE__ ) );

include_once('api-keys.php');

require_once __DIR__ . '/vendor/autoload.php';


include_once(plugin_dir_path(__FILE__) . 'includes/class-hld-user-orders.php');
include_once(plugin_dir_path(__FILE__) . 'includes/class-hld-user-notifications.php');


foreach (glob(plugin_dir_path(__FILE__) . 'helper/*.php') as $file) {
    require_once $file;
}


add_shortcode('hld_orders', function () {
    ob_start();

    include_once('templates/show-orders.php');
    return ob_get_clean();
});

add_action('wp_enqueue_scripts', function () {

    wp_enqueue_style(
        'hld-plugin-custom-css',
        plugin_dir_url(__FILE__) . 'css/custom-style.css',
        [],
        '1.0'
    );
    wp_enqueue_style(
        'hld-plugin-scss',
        plugin_dir_url(__FILE__) . 'css/main.css',
        [],
        '1.0'
    );
    wp_enqueue_style(
        'hld-bootstrap',
        plugin_dir_url(__FILE__) . 'libs/bootstrap.min.css',
        [],
        '1.0'
    );
    wp_enqueue_script(
        'hld-bootstrap',
        plugin_dir_url(__FILE__) . 'libs/bootstrap.min.js',
        ['jquery'],
        '1.0',
        true
    );

    // //Enqueue your custom JavaScript file
    // wp_enqueue_script(
    //     'hld-custom-js',
    //     plugin_dir_url(__FILE__) . 'js/custom-script.js', // Your JS file path
    //     ['jquery'], // or [] if no dependency
    //     '1.0',
    //     true
    // );


    // Enqueue your custom JavaScript file
    wp_enqueue_script(
        'hld-custom-js',
        plugin_dir_url(__FILE__) . 'js/custom-script.js', // Your JS file path
        ['jquery'], // or [] if no dependency
        '1.0',
        true
    );

     wp_enqueue_script(
        'hld-class-patient-login',
        plugin_dir_url(__FILE__) . 'js/class-patient-login.js', // Your JS file path
        ['jquery'], // or [] if no dependency
        '1.0',
        true
    );


    wp_localize_script(
    'hld-class-patient-login',
    'hld_ajax_obj',
    [
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('hld_patient_login_nonce')
    ]
);



    $form_id = 13; // Or dynamically get this
    $active_step_key = 'active_step_fluent_form_' . $form_id;
    $active_step = get_user_meta(get_current_user_id(), $active_step_key, true);

    // Localize ajaxurl for use in custom-script.js
    // wp_localize_script('hld-custom-js', 'ajaxurl', admin_url('admin-ajax.php'));


    // Localize ajaxurl for use in custom-script.js
    wp_localize_script('hld-custom-js', 'hldFormData', [
        'ajaxurl'      => admin_url('admin-ajax.php'),
        'formId'       => $form_id,
        'activeStep'   => $active_step,
    ]);


    // Get the current user
    $current_user = wp_get_current_user();

    // Determine if the user is logged in and get their email, or set null
    $user_email = is_user_logged_in() ? $current_user->user_email : null;

    // Pass the email to the JavaScript file
    wp_localize_script('hld-custom-js', 'hldData', [
        'hldPatientEmail' => $user_email,
    ]);
});



add_action('wp_enqueue_scripts', function () {
    wp_enqueue_script('stripe-js', 'https://js.stripe.com/v3/');
    wp_enqueue_script('my-stripe-handler', plugin_dir_url(__FILE__) . 'js/stripe-handler.js', ['stripe-js'], '1.0', true);

    wp_localize_script('my-stripe-handler', 'MyStripeData', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'publishableKey' => STRIPE_PUBLISHABLE_KEY,
    ]);
});

// log payment success on server
add_action('wp_ajax_log_payment_success', 'my_log_payment_success');
function my_log_payment_success()
{
    $payment_id = sanitize_text_field($_POST['payment_id']);
    // You can log to a file or store in DB
    error_log("Stripe payment succeeded. ID: $payment_id");
    wp_send_json_success();
}


require_once plugin_dir_path(__FILE__) . 'classes/class-dashboard-shortcode.php';

new DashboardShortcode();

include_once('includes/functions.php');
include_once('includes/ajax.php');

function create_patient_if_not_exists_on_telegra_md()
{
    if (!is_user_logged_in()) {
        return;
    }
    $user = wp_get_current_user();
    if (!in_array('subscriber', (array) $user->roles)) {
        return;
    }

    $user_id = $user->ID;
    // don't change this key other developers are using this.
    $meta_key = 'hld_patient_' . $user_id . '_telegra_id';

    // todo: If already exists, skip API call
    // if (get_user_meta($user_id, $meta_key, true)) {
    //     error_log("TelegraMD: Patient already exists for user $user_id");
    //     return;
    // }

    // Prepare payload with available user data
    $first_name = $user->first_name ?: 'Chewbacca';
    $last_name  = $user->last_name ?: 'Wookie';
    $email      = $user->user_email ?: 'johndoe@example.com';

    $payload = [
        'name'             => $first_name . ' ' . $last_name,
        'firstName'        => $first_name,
        'lastName'         => $last_name,
        'email'            => $email,
        'phone'            => '1111111111', // Placeholder
        'dateOfBirth'      => '1990-01-01', // Placeholder
        'gender'           => 'male', // Placeholder
        'genderBiological' => 'male', // Placeholder
        'affiliate'        => TELEGRAMD_AFFLIATE_ID,
    ];

    $response = wp_remote_post('https://dev-core-ias-rest.telegramd.com/patients', [
        'method'  => 'POST',
        'headers' => [
            'accept'        => 'application/json',
            'authorization' => 'Bearer ' . TELEGRAMD_BEARER_TOKEN,
            'content-type'  => 'application/json',
        ],
        'body' => json_encode($payload),
    ]);

    if (is_wp_error($response)) {
        error_log('TelegraMD API Error: ' . $response->get_error_message());
    } else {
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (!empty($data['id'])) {
            update_user_meta($user_id, $meta_key, $data['id']);
            error_log("TelegraMD: Patient created and saved for user $user_id with ID: {$data['id']}");
        } else {
            error_log("TelegraMD API Response missing ID: " . $body);
        }
    }
}



// New chatgpt code for stripe
include_once('includes/shortcodes.php');

add_action('init', function () {
    if (isset($_GET['test_charge'])) {
        $result = hld_charge_later(get_current_user_id(), 500); // $500
        var_dump($result);
        exit;
    }
});



// Create PaymentIntent via AJAX
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



function hld_create_order_on_telegramd($telegra_patient_id)
{
    $bearer_token = 'Bearer ' . TELEGRAMD_BEARER_TOKEN;
    $endpoint = TELEGRA_BASE_URL . '/orders';

    // 🔧 Prepare the request body based on CURL sample
    $body = [
        "data" => [
            "someData" => "?"
        ],
        "patient" => $telegra_patient_id, // example: pat::f2b6ec7f-4b87-4988-9ebb-df663edaf872
        "productVariations" => [
            [
                "productVariation" => "pvt::6e5a3b9c-26d9-46af-89bb-f0ab864ed027",
                "quantity" => 1
            ]
        ],
        "symptoms" => [
            "symp::9d65e74b-caed-4b38-b343-d7f84946da60"
        ],
        "address" => [
            "billing" => [
                "address1" => "123 S Main St",
                "address2" => null,
                "city"     => "Kennewick",
                "state"    => "state::07b1c554-5521-4bab-b65c-8436b72cfcb6",
                "zipcode"  => 99337
            ],
            "shipping" => [
                "address1" => "123 S Main St",
                "address2" => null,
                "city"     => "Kennewick",
                "state"    => "state::07b1c554-5521-4bab-b65c-8436b72cfcb6",
                "zipcode"  => 99337
            ]
        ]
    ];

    // 🛰 Send the POST request
    $response = wp_remote_post($endpoint, [
        'method'    => 'POST',
        'headers'   => [
            'Authorization' => $bearer_token,
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/json',
        ],
        'body'      => json_encode($body),
        'timeout'   => 20,
    ]);

    // Check for transport-level WP error
    if (is_wp_error($response)) {
        error_log('[TelegraMD Error] cURL Error: ' . $response->get_error_message());
        return new WP_Error('api_error', 'Failed to create order: ' . $response->get_error_message());
    }

    $status_code   = wp_remote_retrieve_response_code($response);
    $response_body = wp_remote_retrieve_body($response);
    $data          = json_decode($response_body, true);

    // API returned non-200/201
    if ($status_code !== 200 && $status_code !== 201) {
        error_log('[TelegraMD Order Failed] HTTP ' . $status_code . ' → ' . $response_body);
        return new WP_Error('order_failed', 'Order API returned error: ' . $response_body);
    }

    if (is_user_logged_in()) {
        $user_id = get_current_user_id();
        HLD_UserOrders::add_order($user_id, $data['id']);
    } else {
        error_log("⚠️ User not logged in, cannot save order to user meta.");
    }

    // Success
    error_log('[TelegraMD Order Created] Status: ' . $status_code . ' → ' . $response_body);
    return $data;
}

add_action('fluentform/before_insert_submission', function (&$insertData, $form) {
    error_log("🔥 fluentform/before_insert_submission hook is called");
    // Log all submitted data
    error_log("Submitted Data: " . print_r($insertData, true));
    // Log form object
    error_log("Form Object: " . print_r($form, true));
    //  Optional: Only run for logged-in users (recommended)
    if (!is_user_logged_in()) {
        return;
    }
    // todo: Optional: check for a specific form ID
    // if ($form->id != 14) return;

    // Custom logic
    create_patient_if_not_exists_on_telegra_md();
    $telegra_patient_id = get_telegra_patient_id_for_current_user();
    if (empty($telegra_patient_id)) {
        error_log("TelegraMD patient ID not found for current user.");
        return;
    }
    error_log("telegra_patient_id " . $telegra_patient_id);
    // Create order in TelegraMD
    hld_create_order_on_telegramd($telegra_patient_id);
}, 10, 2);

include_once(plugin_dir_path(__FILE__) . 'includes/order-tracking-webhook.php');
include_once(plugin_dir_path(__FILE__) . 'includes/prescription-received-webhook.php');























add_action('wp_ajax_save_form_url', 'handle_save_form_url');
add_action('wp_ajax_nopriv_save_form_url', 'handle_save_form_url');

function handle_save_form_url()
{
    $form_id = isset($_POST['form_id']) ? intval($_POST['form_id']) : 0;
    $form_url = isset($_POST['form_url']) ? esc_url_raw($_POST['form_url']) : '';
    $active_step = isset($_POST['active_step']) ? intval($_POST['active_step']) : 1;

    if (!$form_id || empty($form_url)) {
        wp_send_json_error('Invalid data provided');
    }

    $meta_key = 'fluent_form_' . $form_id;
    $active_step_key = 'active_step_fluent_form_' . $form_id;
    // Save to user meta instead of options


    update_user_meta(get_current_user_id(), $meta_key, $form_url);
    update_user_meta(get_current_user_id(), $active_step_key, $active_step);


    wp_send_json_success('Form URL saved');
}

// patient Login Shortcode Script 
include_once(plugin_dir_path(__FILE__) . 'includes/patient-login.php');


require_once TELEGRA_PATIENT_PORTAL_PATH . 'ajax/patient-login.php';