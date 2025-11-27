<?php

// constants
global $wpdb;
$prefix = $wpdb->prefix . "healsend_";



//  Prefunnels Forms

if (! defined('HLD_GLP_1_PREFUNNEL_FORM_ID')) {
    define('HLD_GLP_1_PREFUNNEL_FORM_ID', HLD_LIVE ? 56 : 60);
}

if (! defined('HLD_METABOLIC_PREFUNNEL_FORM_ID')) {
    define('HLD_METABOLIC_PREFUNNEL_FORM_ID', HLD_LIVE ? 70 : 57);
}

if (! defined('HLD_TRT_PREFUNNEL_FORM_ID')) {
    define('HLD_TRT_PREFUNNEL_FORM_ID', HLD_LIVE ? 63 : 55);
}




// Action Items -  Questionnaires

if (! defined('HLD_CLINICAL_DIFFERENCE_FORM_ID')) {
    define('HLD_CLINICAL_DIFFERENCE_FORM_ID', HLD_LIVE ? 66 : 59);
}

if (! defined('HLD_METABOLIC_ACTION_ITEM_FORM_ID')) {
    define('HLD_METABOLIC_ACTION_ITEM_FORM_ID', HLD_LIVE ? 71 : 58);
}



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




#***********************GHL API KEY *************************
if (! defined('GHL_API_KEY')) {
    define('GHL_API_KEY', 'pit-dcbcc991-8612-49ae-a5ff-31046d43da5b');
}

// Patient dashboard URL
if (! defined('HLD_PATIENT_DASHBOARD_URL')) {
    define('HLD_PATIENT_DASHBOARD_URL', home_url('/my-account/'));
}



define("HLD_GENERAL_ACTION_ITEM", "general");
// Define Slugs for plans

define("HLD_GLP_WEIGHT_LOSS_SLUG", "glp_1_prefunnel");
define("HLD_METABOLIC_SLUG", "metabolic");
