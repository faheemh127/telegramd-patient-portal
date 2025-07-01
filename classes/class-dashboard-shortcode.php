<?php

class DashboardShortcode
{

    public function __construct()
    {
        add_shortcode('dashboard', [$this, 'render_dashboard']);
    }

    public function render_dashboard()
    {
        ob_start();

        // Check if user is logged in
        if (is_user_logged_in()) {
            $user = wp_get_current_user();

            // Check if user has only 'subscriber' role
            if (in_array('subscriber', (array) $user->roles)) {
                hdl_get_template('dashboard/wrapper', ['icons' => hdl_icons(), 'user' => $user]);
            } else {
                // You can add logic here for other roles if needed
                hdl_get_template('no-access', []);
            }
        } else {
            // Load login prompt template
            hdl_get_template('login-first');
        }

        return ob_get_clean();
    }
}
