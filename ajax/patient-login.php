<?php
add_action('wp_ajax_hld_patient_login', 'hld_patient_login_handler');
add_action('wp_ajax_nopriv_hld_patient_login', 'hld_patient_login_handler');

function hld_patient_login_handler() {
    check_ajax_referer('hld_patient_login_nonce', 'nonce');

    $username = sanitize_text_field($_POST['username'] ?? '');
    $password = sanitize_text_field($_POST['password'] ?? '');

    // Simple validation
    if (empty($username) || empty($password)) {
        wp_send_json_error('Missing username or password.');
    }

    // Try to login
    $creds = [
        'user_login'    => $username,
        'user_password' => $password,
        'remember'      => true,
    ];

    $user = wp_signon($creds, false); 

    if (is_wp_error($user)) {
        wp_send_json_error($user->get_error_message());
    }

    wp_send_json_success('Login successful');
}
