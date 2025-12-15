<?php

class hldDashboardShortcode
{
    public function __construct()
    {
        add_shortcode('patient_dashboard', [$this, 'render_dashboard']);
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
                wp_redirect(home_url('/patient-login?patient_login_required=true'));
                exit;
            }
        } else {
            wp_redirect(home_url('/patient-login?patient_login_required=true'));
            exit;
        }
    }
}
$hld_dashboard_shortcode = new hldDashboardShortcode();
