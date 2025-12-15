<?php
if (!defined('ABSPATH')) exit;

class HLD_Admin_Menu {

    public static function init() {
        add_action('admin_menu', [__CLASS__, 'register_menu']);
    }

    public static function register_menu() {
        // Main Menu
        add_menu_page(
            'Healsend',
            'Healsend',
            'manage_options',
            'healsend-admin',
            [__CLASS__, 'dashboard_page'],
            'dashicons-heart',
            25
        );

        // Sub Menus
        add_submenu_page('healsend-admin', 'Patients', 'Patients', 'manage_options', 'healsend-patients', [__CLASS__, 'render_patients']);
        add_submenu_page('healsend-admin', 'Payments', 'Payments', 'manage_options', 'healsend-payments', [__CLASS__, 'render_payments']);
        add_submenu_page('healsend-admin', 'Patient Forms', 'Patient Forms', 'manage_options', 'healsend-forms', [__CLASS__, 'render_forms']);
        add_submenu_page('healsend-admin', 'Form Answers', 'Form Answers', 'manage_options', 'healsend-form-answers', [__CLASS__, 'render_form_answers']);
        add_submenu_page('healsend-admin', 'Subscriptions', 'Subscriptions', 'manage_options', 'healsend-subscriptions', [__CLASS__, 'render_subscriptions']);
    }

    public static function dashboard_page() {
        echo '<div class="wrap"><h1>Healsend Admin Dashboard</h1><p>Select a submenu to view data.</p></div>';
    }

    /**
     * Render a generic table view for given table
     */
    private static function render_table($table_name) {
        global $wpdb;
        $results = $wpdb->get_results("SELECT * FROM $table_name LIMIT 50", ARRAY_A);

        echo '<div class="wrap">';
        echo '<h1>' . esc_html(ucfirst(str_replace('_', ' ', str_replace($wpdb->prefix . "healsend_", '', $table_name)))) . '</h1>';

        if (empty($results)) {
            echo '<p>No records found.</p>';
        } else {
            echo '<table class="widefat fixed striped">';
            echo '<thead><tr>';
            foreach (array_keys($results[0]) as $col) {
                echo '<th>' . esc_html($col) . '</th>';
            }
            echo '</tr></thead><tbody>';

            foreach ($results as $row) {
                echo '<tr>';
                foreach ($row as $col => $val) {
                    echo '<td>' . esc_html($val) . '</td>';
                }
                echo '</tr>';
            }

            echo '</tbody></table>';
        }

        echo '</div>';
    }

    // Specific table renderers
    public static function render_patients() {
        global $wpdb;
        self::render_table($wpdb->prefix . 'healsend_patients');
    }

    public static function render_payments() {
        global $wpdb;
        self::render_table($wpdb->prefix . 'healsend_payments');
    }

    public static function render_forms() {
        global $wpdb;
        self::render_table($wpdb->prefix . 'healsend_patient_forms');
    }

    public static function render_form_answers() {
        global $wpdb;
        self::render_table($wpdb->prefix . 'healsend_patient_form_answers');
    }

    public static function render_subscriptions() {
        global $wpdb;
        self::render_table($wpdb->prefix . 'healsend_subscriptions');
    }
}

HLD_Admin_Menu::init();
