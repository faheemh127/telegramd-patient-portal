<?php

// constants
global $wpdb;
$prefix = $wpdb->prefix . "healsend_";


define("HLD_CURRENCY", "usd");
define("HLD_BUISNESS_OPERATIONAL_COUNTRY", "US");

// ******************** Action Items Ends


// Define constants for table names
if (! defined('HEALSEND_PATIENTS_TABLE')) {
    define('HEALSEND_PATIENTS_TABLE', $prefix . "patients");
}
if (! defined('HEALSEND_PAYMENTS_TABLE')) {
    define('HEALSEND_PAYMENTS_TABLE', $prefix . "payments");
}
if (! defined('HEALSEND_PATIENT_FORMS_TABLE')) {
    define('HEALSEND_PATIENT_FORMS_TABLE', $prefix . "patient_forms");
}
if (! defined('HEALSEND_FORM_ANSWERS_TABLE')) {
    define('HEALSEND_FORM_ANSWERS_TABLE', $prefix . "patient_form_answers");
}

if (! defined('HEALSEND_ACTION_ITEMS_TABLE')) {
    define('HEALSEND_ACTION_ITEMS_TABLE', $prefix . "action_items");
}

if (! defined('HEALSEND_USER_ACTIONS_TABLE')) {
    define('HEALSEND_USER_ACTIONS_TABLE', $prefix . "user_actions");
}
if (! defined('HEALSEND_SUBSCRIPTIONS_TABLE')) {
    define('HEALSEND_SUBSCRIPTIONS_TABLE', $wpdb->prefix . "healsend_subscriptions");
}

if (! defined('HLD_AFFILIATE_TABLE')) {
    define('HLD_AFFILIATE_TABLE', $wpdb->prefix . "healsend_affiliate");
}




#***********************GHL API KEY *************************


// Patient dashboard URL
if (! defined('HLD_PATIENT_DASHBOARD_URL')) {
    define('HLD_PATIENT_DASHBOARD_URL', home_url('/my-account/'));
}



define("HLD_GENERAL_ACTION_ITEM", "general");
// Define Slugs for plans

define("HLD_GLP_WEIGHT_LOSS_SLUG", "glp_1_prefunnel");
define("HLD_METABOLIC_SLUG", "metabolic");
define("HLD_PT_141_SLUG", "pt_141");
define("HLD_OXYTOCIN_SLUG", "oxytocin");
