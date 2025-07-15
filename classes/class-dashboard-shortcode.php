<?php

class DashboardShortcode
{
    public function __construct()
    {
        add_shortcode('patient_dashboard', [$this, 'render_dashboard']);
        add_action('rest_api_init', [$this, 'register_rest_routes']);
    }

    public function render_dashboard()
    {
        $user = wp_get_current_user();
        // ...existing code...
        if (is_user_logged_in() && current_user_can('administrator')) {
            ob_start();
            hdl_get_template('dashboard/wrapper', ['icons' => hdl_icons(), 'user' => $user]);
            return ob_get_clean();
        }
        if (is_user_logged_in()) {
            if (in_array('subscriber', (array) $user->roles)) {
                ob_start();
                hdl_get_template('dashboard/wrapper', ['icons' => hdl_icons(), 'user' => $user]);
                return ob_get_clean();
            } else {
                wp_redirect(home_url('/log-in-to-your-account?patient_login_required=true'));
                exit;
            }
        } else {
            wp_redirect(home_url('/log-in-to-your-account?patient_login_required=true'));
            exit;
        }
    }

    public function register_rest_routes()
    {
        register_rest_route('hld/v1', '/update-account-details', [
            'methods' => 'POST',
            'callback' => [$this, 'update_account_details'],
            'permission_callback' => function () {
                return is_user_logged_in();
            },
        ]);
    }

    public function update_account_details($request)
    {
        // Nonce check (if sent)
        if (function_exists('wp_verify_nonce')) {
            $nonce = $request->get_header('x-wp-nonce');
            if (!$nonce || !wp_verify_nonce($nonce, 'wp_rest')) {
                return new WP_REST_Response(['success' => false, 'message' => 'Invalid nonce.'], 403);
            }
        }

        $user_id = get_current_user_id();
        if (!$user_id) {
            return new WP_REST_Response(['success' => false, 'message' => 'User not logged in.'], 401);
        }

        $params = $request->get_json_params();
        $full_name = sanitize_text_field($params['full_name'] ?? '');
        $email = sanitize_email($params['email'] ?? '');
        $phone = sanitize_text_field($params['phone'] ?? '');
        $dob = sanitize_text_field($params['dob'] ?? '');

        $user_data = [
            'ID' => $user_id,
        ];
        if ($full_name) {
            $user_data['display_name'] = $full_name;
        }
        if ($email) {
            $user_data['user_email'] = $email;
        }

        $user_update = wp_update_user($user_data);
        if (is_wp_error($user_update)) {
            return new WP_REST_Response(['success' => false, 'message' => $user_update->get_error_message()], 400);
        }

        update_user_meta($user_id, 'phone', $phone);
        update_user_meta($user_id, 'dob', $dob);

        return new WP_REST_Response(['success' => true], 200);
    }
}
