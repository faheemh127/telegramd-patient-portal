<?php

add_action('wp_ajax_hld_update_account_details', 'hld_update_account_details');
add_action('wp_ajax_nopriv_hld_update_account_details', 'hld_update_account_details');

function hld_update_account_details()
{
    //todo:implement verify nonce
    // ✅ Verify nonce only for logged-in users
    // if (is_user_logged_in()) {
    //     check_ajax_referer('hld_ajax_nonce', '_ajax_nonce');
    // }

    $user_id = get_current_user_id();
    if (!$user_id) {
        wp_send_json(['success' => false, 'message' => 'User not logged in.']);
    }

    // ✅ Sanitize and collect form data
    $full_name = sanitize_text_field($_POST['full_name'] ?? '');
    $email     = sanitize_email($_POST['email'] ?? '');
    $phone     = sanitize_text_field($_POST['phone'] ?? '');
    $dob       = sanitize_text_field($_POST['dob'] ?? '');

    // ✅ Split full name into first & last name
    $name_parts = explode(' ', $full_name, 2);
    $first_name = $name_parts[0] ?? '';
    $last_name  = $name_parts[1] ?? '';

    // ✅ Update WordPress user table
    $user_data = ['ID' => $user_id];
    if ($email) {
        $user_data['user_email'] = $email;
    }
    if ($full_name) {
        $user_data['display_name'] = $full_name;
    }

    $user_update = wp_update_user($user_data);
    if (is_wp_error($user_update)) {
        wp_send_json(['success' => false, 'message' => $user_update->get_error_message()]);
    }

    // ✅ Update user meta (optional for easy reference)
    update_user_meta($user_id, 'phone', $phone);
    update_user_meta($user_id, 'dob', $dob);

    // ✅ Load patient model
    if (!class_exists('HLD_Patient')) {
        wp_send_json(['success' => false, 'message' => 'Patient class not found.']);
    }

    // ✅ Ensure patient exists in table
    $current_user = wp_get_current_user();
    $patient_email = $current_user->user_email;
    HLD_Patient::ensure_patient_by_email($patient_email);

    // ✅ Update data in custom patient table
    if ($dob) {
        HLD_Patient::update_dob($dob);
    }
    if ($phone) {
        HLD_Patient::update_phone($phone);
    }
    if ($first_name || $last_name) {
        HLD_Patient::update_name($first_name, $last_name);
    }

    $hld_telegra = new HLD_Telegra();
    $patient = HLD_Patient::get_patient_info();
    $telegra_patient_id = HLD_Patient::get_telegra_patient_id($email);

    if (!empty($telegra_patient_id)) {
        $payload = [
            'firstName'        => $first_name,
            'lastName'         => $last_name,
            'email'            => $email,
            'phone'            => $phone,
            'dateOfBirth'      => $dob,
            'gender'           => 'other', // optional — use saved value if available
            'genderBiological' => $patient['gender'],
        ];

        $update_response = $hld_telegra->update_patient_on_telegra($telegra_patient_id, $payload);

        if (is_wp_error($update_response)) {
            error_log('[TelegraMD] Patient update failed: ' . $update_response->get_error_message());
        }
    }

    wp_send_json([
        'success' => true,
        'message' => 'Account details updated successfully.',
        'data' => [
            'full_name' => $full_name,
            'email'     => $email,
            'phone'     => $phone,
            'dob'       => $dob,
        ]
    ]);
}
