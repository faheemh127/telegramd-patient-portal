<?php

/**
 * Plugin Name: TelegraMD Patient Portal
 * Description: Patient portal with TelegraMD API integration for prescriptions, labs, and subscriptions.
 * Version: 1.0
 * Author: Faheem
 * Author URI: https://faheemhassan.dev
 */


// constants

define("HLD_DEVELOPER_ENVIRONMENT", true);
define('HLD_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('HLD_PLUGIN_URL', plugin_dir_url(__FILE__));
define('HLD_PATIENT_DASHBOARD_URL', home_url('/my-account/'));



// auto load
require_once __DIR__ . '/vendor/autoload.php';
foreach (glob(plugin_dir_path(__FILE__) . 'helper/*.php') as $file) {
    require_once $file;
}

// include all necessary files
require_once plugin_dir_path(__FILE__) . 'ajax/stripe-create-setup-intent.php';
require_once plugin_dir_path(__FILE__) . 'ajax/stripe-charge-now.php';
require_once plugin_dir_path(__FILE__) . 'ajax/stripe-subscribe-patient.php';
require_once plugin_dir_path(__FILE__) . 'includes/api-keys.php';
require_once plugin_dir_path(__FILE__) . 'classes/class-hld-settings.php';
require_once plugin_dir_path(__FILE__) . 'includes/functions.php';
require_once plugin_dir_path(__FILE__) . 'classes/class-hld-user-orders.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-hld-user-notifications.php';
require_once plugin_dir_path(__FILE__) . 'classes/class-hld-assets.php';
require_once plugin_dir_path(__FILE__) . 'classes/class-telegra.php';
require_once plugin_dir_path(__FILE__) . 'classes/class-fluent-handler.php';
require_once plugin_dir_path(__FILE__) . 'classes/class-patient.php';
require_once plugin_dir_path(__FILE__) . 'classes/class-dashboard-shortcode.php';
require_once plugin_dir_path(__FILE__) . 'classes/class-db-tables.php';
require_once plugin_dir_path(__FILE__) . 'ajax/save-payment-method.php';
require_once plugin_dir_path(__FILE__) . 'ajax/log-payment-success.php';
require_once plugin_dir_path(__FILE__) . 'includes/shortcodes.php';
require_once plugin_dir_path(__FILE__) . 'includes/order-tracking-webhook.php';
require_once plugin_dir_path(__FILE__) . 'includes/prescription-received-webhook.php';
require_once plugin_dir_path(__FILE__) . 'includes/hooks.php';
require_once plugin_dir_path(__FILE__) . 'ajax/save-form-url.php';
require_once plugin_dir_path(__FILE__) . 'includes/patient-login.php';
require_once plugin_dir_path(__FILE__) . 'includes/patient-signup.php';
require_once plugin_dir_path(__FILE__) . 'ajax/patient-login.php';


register_activation_hook(__FILE__, ['HLD_DB_Tables', 'create_tables']);
