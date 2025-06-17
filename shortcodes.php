<?php
// shortcodes.php

add_shortcode('my_rx_list', function() {
    if (!is_user_logged_in()) return 'Please log in.';
    $user = wp_get_current_user();
    $orders = telegramd_get_orders_by_email($user->user_email);

    ob_start();
    echo '<ul>';
    foreach ($orders as $rx) {
        echo '<li>' . esc_html($rx['name']) . ' – ' . esc_html($rx['status']) . '</li>';
    }
    echo '</ul>';
    return ob_get_clean();
});

add_shortcode('my_lab_results', function() {
    if (!is_user_logged_in()) return 'Please log in.';
    $user = wp_get_current_user();
    $labs = telegramd_get_labs_by_email($user->user_email);

    ob_start();
    echo '<ul>';
    foreach ($labs as $lab) {
        echo '<li>' . esc_html($lab['test']) . ' – ' . esc_html($lab['status']);
        if (!empty($lab['result_url'])) {
            echo ' (<a href="' . esc_url($lab['result_url']) . '" target="_blank">Result</a>)';
        }
        echo '</li>';
    }
    echo '</ul>';
    return ob_get_clean();
});
