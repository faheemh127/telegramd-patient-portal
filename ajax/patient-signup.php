<?php
add_action('wp_ajax_hld_patient_signup', 'hld_patient_signup_handler');
add_action('wp_ajax_nopriv_hld_patient_signup', 'hld_patient_signup_handler');

function hld_patient_signup_handler()
{
    check_ajax_referer('class_patient_login_nonce', 'nonce'); // You may rename nonce later

    $email    = sanitize_email($_POST['username'] ?? '');
    $password = sanitize_text_field($_POST['password'] ?? '');

    error_log("classhld_patient_signup works");

    // Basic validation
    if (empty($email) || empty($password)) {
        wp_send_json_error('Email and password are required.');
    }

    if (!is_email($email)) {
        wp_send_json_error('Invalid email format.');
    }

    // Check if account already exists
    if (email_exists($email)) {
        wp_send_json_error('An account with this email already exists.');
    }

    // Create the user (username = email)
    $user_id = wp_create_user($email, $password, $email);

    if (is_wp_error($user_id)) {
        wp_send_json_error('Registration failed. Please try again.');
    }

    // Set user role to subscriber
    $user = new WP_User($user_id);
    $user->set_role('subscriber');

    // OPTIONAL — Auto login user after signup
    $creds = [
        'user_login'    => $email,
        'user_password' => $password,
        'remember'      => true,
    ];
    $logged_in = wp_signon($creds, false);

    if (is_wp_error($logged_in)) {
        wp_send_json_success('Account created. Please log in manually.');
    }

    wp_send_json_success('Signup successful');
}
