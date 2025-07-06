<?php

/**
 * Plugin Name: ! TelegraMD Patient Portal
 * Description: Patient portal with TelegraMD API integration for prescriptions, labs, and subscriptions.
 * Version: 1.0
 * Author: Faheem
 * Author URI: https://faheemhassan.dev
 * Prefix: hld
 */

// Include API
// require_once plugin_dir_path(__FILE__) . 'telegramd-api.php';
// // Register shortcodes
// require_once plugin_dir_path(__FILE__) . 'shortcodes.php';
// // Admin settings page
// require_once plugin_dir_path(__FILE__) . 'admin-settings.php';



include_once('api-keys.php');
foreach (glob(plugin_dir_path(__FILE__) . 'helper/*.php') as $file) {
    require_once $file;
}


add_shortcode('hld_orders', function () {
    ob_start();
    include_once('templates/show-orders.php');
    return ob_get_clean();
});

add_action('wp_enqueue_scripts', function () {



    // Started adding stripe SDK
    // Load Stripe.js from Stripe's CDN
    wp_enqueue_script(
        'stripe-js',
        'https://js.stripe.com/v3/',
        [],
        null,
        true
    );

    // Enqueue your own JavaScript file AFTER stripe.js
    wp_enqueue_script(
        'my-stripe-handler',
        plugin_dir_url(__FILE__) . 'js/stripe-handler.js',
        ['stripe-js'],
        '1.0',
        true
    );

    // // Pass Stripe Publishable Key to JS
    // wp_localize_script('my-stripe-handler', 'MyStripeData', [
    //     'publishableKey' => 'pk_test_YOUR_PUBLISHABLE_KEY',
    // ]);
    $your_generated_client_secret = "..";
    wp_localize_script('my-stripe-handler', 'MyStripeData', [
        'publishableKey' => STRIPE_PUBLISHABLE_KEY,
        'clientSecret' => STRIPE_SECRET_KEY, // from Stripe API
    ]);
    // Ended stripe SDK 

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

    //Enqueue your custom JavaScript file
    wp_enqueue_script(
        'hld-custom-js',
        plugin_dir_url(__FILE__) . 'js/custom-script.js', // Your JS file path
        ['jquery'], // or [] if no dependency
        '1.0',
        true
    );


    // Get the current user
    $current_user = wp_get_current_user();

    // Determine if the user is logged in and get their email, or set null
    $user_email = is_user_logged_in() ? $current_user->user_email : null;

    // Pass the email to the JavaScript file
    wp_localize_script('hld-custom-js', 'hldData', [
        'hldPatientEmail' => $user_email,
    ]);
});

require_once plugin_dir_path(__FILE__) . 'classes/class-dashboard-shortcode.php';
new DashboardShortcode();







// function create_dummy_patient_on_telegra_md()
// {
//     $api_url = 'https://dev-core-ias-rest.telegramd.com/patients';

//     // Dummy patient payload
//     $payload = [
//         'name'      => 'John Doe',
//         'firstName' => 'Chewbacca',
//         'lastName'   => '',
//         'email'     => 'johndoe@example.com',
//         'phone'     => '',
//         'dateOfBirth' => '',
//         'gender'    => '',
//         'genderBiological' => '',
//         'affiliate' => TELEGRAMD_AFFLIATE_ID,
//     ];
//     $response = wp_remote_post($api_url, [
//         'method'  => 'POST',
//         'headers' => [
//             'accept'        => 'application/json',
//             'authorization' => 'Bearer ' . TELEGRAMD_BEARER_TOKEN,
//             'content-type'  => 'application/json',
//         ],
//         'body' => json_encode($payload),
//     ]);

//     if (is_wp_error($response)) {
//         error_log('TelegraMD API Error: ' . $response->get_error_message());
//     } else {
//         $body = wp_remote_retrieve_body($response);
//         error_log('TelegraMD API Response: ' . $body);
//     }
// }




// function debug_print_current_nsl_user_info()
// {
//     $user_id = get_current_user_id();
//     $provider = 'google'; // You can change this to 'facebook', 'twitter', etc. if needed
//     $current_user =  wp_get_current_user();
//     echo "<pre>";
//     print_r("user id is");
//     print_r($user_id);
//     print_r($current_user);
//     echo "</pre>";

//     if ($user_id) {
//         $user_info = get_user_meta($user_id, 'nsl_user_data_' . $provider, true);
//         echo "working32";
//         print_r($user_info);
//         var_dump($user_info);
//         echo "okfjdsl";
//         error_log("NSL User Info for logged-in user ID $user_id (provider: $provider):");
//         error_log(print_r($user_info, true));
//     } else {
//         error_log("No user is currently logged in.");
//     }
// }

// add_action('init', 'debug_print_current_nsl_user_info');




function get_telegra_patient_id_for_current_user()
{
    if (!is_user_logged_in()) {
        return null;
    }

    $user_id = get_current_user_id();
    $meta_key = 'hld_patient_' . $user_id . '_telegra_id';

    $patient_id = get_user_meta($user_id, $meta_key, true);

    return !empty($patient_id) ? $patient_id : null;
}


// $telegra_id =   get_telegra_patient_id_for_current_user();
// echo "telegraid";
// print_r($telegra_id);


add_action('init', 'create_patient_if_not_exists_on_telegra_md');

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

    // If already exists, skip API call
    if (get_user_meta($user_id, $meta_key, true)) {
        error_log("TelegraMD: Patient already exists for user $user_id");
        return;
    }

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
