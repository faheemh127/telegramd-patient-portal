<?php

class DashboardShortcode
{

    public function __construct()
    {
        add_shortcode('patient_dashboard', [$this, 'render_dashboard']);
    }

    public function render_dashboard()
    {
        $user = wp_get_current_user();

        // Check if user is logged in
        if (is_user_logged_in() && current_user_can('administrator')) {
            ob_start();
            hdl_get_template('dashboard/wrapper', ['icons' => hdl_icons(), 'user' => $user]);
            return ob_get_clean();
        }



        if (is_user_logged_in()) {


            // Check if user has only 'subscriber' role
            if (in_array('subscriber', (array) $user->roles)) {
                ob_start();
              
                hdl_get_template('dashboard/wrapper', ['icons' => hdl_icons(), 'user' => $user]);
                return ob_get_clean();
            } else {
                // Redirect non-subscribers
                wp_redirect(home_url('/log-in-to-your-account?patient_login_required=true'));
                exit;
            }
        } else {
            // Redirect non-logged-in users
            wp_redirect(home_url('/log-in-to-your-account?patient_login_required=true'));
            exit;
        }
    }
}
