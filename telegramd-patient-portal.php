<?php
/**
 * Plugin Name: TelegraMD Patient Portal
 * Description: Patient portal with TelegraMD API integration for prescriptions, labs, and subscriptions.
 * Version: 1.0
 * Author: Your Company
 */

// Include API
require_once plugin_dir_path(__FILE__) . 'telegramd-api.php';

// Register shortcodes
require_once plugin_dir_path(__FILE__) . 'shortcodes.php';

// Admin settings page
require_once plugin_dir_path(__FILE__) . 'admin-settings.php';
