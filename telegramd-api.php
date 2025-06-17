<?php
// telegramd-api.php

function telegramd_get_api_key() {
    return get_option('telegramd_api_key'); // Stored via WP admin or wp_options
}

function telegramd_get_orders_by_email($email) {
    if (defined('TELEGRAMD_API_MOCK') && TELEGRAMD_API_MOCK) {
        return [
            ['name' => 'Semaglutide 2.5mg', 'status' => 'Shipped'],
            ['name' => 'Tirzepatide 5mg', 'status' => 'Processing'],
        ];
    }

    $response = wp_remote_get('https://api.telegramd.com/v1/orders?email=' . urlencode($email), [
        'headers' => [
            'Authorization' => 'Bearer ' . telegramd_get_api_key(),
        ]
    ]);

    if (is_wp_error($response)) return [];

    $body = wp_remote_retrieve_body($response);
    return json_decode($body, true)['orders'] ?? [];
}

function telegramd_get_labs_by_email($email) {
    if (defined('TELEGRAMD_API_MOCK') && TELEGRAMD_API_MOCK) {
        return [
            ['test' => 'CBC Panel', 'status' => 'Complete', 'result_url' => 'https://example.com/lab1.pdf'],
            ['test' => 'Lipid Panel', 'status' => 'Pending', 'result_url' => '']
        ];
    }

    $response = wp_remote_get('https://api.telegramd.com/v1/labs?email=' . urlencode($email), [
        'headers' => [
            'Authorization' => 'Bearer ' . telegramd_get_api_key(),
        ]
    ]);

    if (is_wp_error($response)) return [];

    $body = wp_remote_retrieve_body($response);
    return json_decode($body, true)['labs'] ?? [];
}

function telegramd_get_subscription_by_email($email) {
    if (defined('TELEGRAMD_API_MOCK') && TELEGRAMD_API_MOCK) {
        return [
            'plan' => 'Monthly Plus',
            'status' => 'Active',
            'renewal_date' => '2025-07-01'
        ];
    }

    $response = wp_remote_get('https://api.telegramd.com/v1/subscriptions?email=' . urlencode($email), [
        'headers' => [
            'Authorization' => 'Bearer ' . telegramd_get_api_key(),
        ]
    ]);

    if (is_wp_error($response)) return [];

    $body = wp_remote_retrieve_body($response);
    return json_decode($body, true)['subscription'] ?? [];
}
