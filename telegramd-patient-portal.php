<?php

/**
 * Plugin Name: TelegraMD Patient Portal
 * Description: Provides a patient portal for Healsend.com with full TelegraMD REST API integration, including prescriptions, lab results, and subscription management.
 * Version: 1.1
 * Author: Faheem
 * Author URI: https://faheemhassan.dev
 */

define('HLD_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('HLD_PLUGIN_URL', plugin_dir_url(__FILE__));

require_once plugin_dir_path(__FILE__) . 'includes/constants.php';

require_once __DIR__ . '/vendor/autoload.php';
foreach (glob(plugin_dir_path(__FILE__) . 'helper/*.php') as $file) {
    require_once $file;
}


require_once plugin_dir_path(__FILE__) . 'classes/class-stripe.php';
require_once plugin_dir_path(__FILE__) . 'ajax/stripe-create-setup-intent.php';
require_once plugin_dir_path(__FILE__) . 'ajax/stripe-charge-now.php';
require_once plugin_dir_path(__FILE__) . 'ajax/upload-id.php';
require_once plugin_dir_path(__FILE__) . 'includes/api-keys.php';
require_once plugin_dir_path(__FILE__) . 'classes/class-hld-settings.php';
require_once plugin_dir_path(__FILE__) . 'includes/functions.php';
require_once plugin_dir_path(__FILE__) . 'classes/class-hld-user-subscriptions.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-hld-user-notifications.php';
require_once plugin_dir_path(__FILE__) . 'classes/class-payments.php';
require_once plugin_dir_path(__FILE__) . 'classes/class-hld-db-backup-manager.php';
require_once plugin_dir_path(__FILE__) . 'classes/class-hld-assets.php';
require_once plugin_dir_path(__FILE__) . 'classes/class-patient.php';
require_once plugin_dir_path(__FILE__) . 'classes/class-telegra.php';
require_once plugin_dir_path(__FILE__) . 'classes/class-admin-menu.php';
require_once plugin_dir_path(__FILE__) . 'classes/class-fluent-handler.php';
require_once plugin_dir_path(__FILE__) . 'classes/class-dashboard-shortcode.php';
require_once plugin_dir_path(__FILE__) . 'classes/class-db-tables.php';
require_once plugin_dir_path(__FILE__) . 'classes/class-action-item-manager.php';
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
require_once plugin_dir_path(__FILE__) . 'ajax/stripe-subscribe-patient.php';
require_once plugin_dir_path(__FILE__) . 'ajax/stripe-get-subscription-price.php';


register_activation_hook(__FILE__, function () {
    HLD_DB_Tables::create_tables();
    HLD_ActionItems_Manager::seed_default_items();
});
