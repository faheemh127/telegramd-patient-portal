<?php

add_action('wp_ajax_cancel_card_reminders', 'hld_cancel_card_reminders');
add_action('wp_ajax_nopriv_card_reminders', 'hld_cancel_card_reminders');

function hld_cancel_card_reminders()
{
    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'You must be logged in.']);
        wp_die();
    }

    $status = HLD_Patient::cancel_email_reminders_to_add_card();
    if ($status) {
        wp_send_json_success(['success' => true, 'message' => 'Reminders cancelled.']);
    } else {
        wp_send_json_success(['success' => false, 'message' => 'Internal Server Error.']);
    }

    wp_die();
}
