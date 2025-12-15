<?php
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
