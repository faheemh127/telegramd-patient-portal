<?php

// Subscribe Patient (auto-cancel after X months)
add_action('wp_ajax_32asdf3zsdf41fasef431sdf3412', 'hld_ghl_add_contact');
add_action('wp_ajax_nopriv_32asdf3zsdf41fasef431sdf341', 'hld_ghl_add_contact');

function hld_ghl_add_contact()
{
    global $wpdb;
    $table = $wpdb->prefix . 'healsend_subscriptions';

    if (!isset($_POST['data'])) {
        wp_send_json_error(['message' => 'Missing parameters']);
        wp_die();
    }
    $data = sanitize_text_field($_POST['data']);
    try {
            wp_send_json_success([
                'subscription_id' => $subscription->id,
                'status' => $subscription->status,
                'customer_id' => $customer_id,

            ]);
        }
    } catch (Exception $e) {
        wp_send_json_error(['message' => $e->getMessage()]);
    }

    wp_die();
}
