<?php

// constants
global $wpdb;
$prefix = $wpdb->prefix . "healsend_";



//  Prefunnels Forms

// 1)
if (! defined('HLD_GLP_1_PREFUNNEL_FORM_ID')) {
    define('HLD_GLP_1_PREFUNNEL_FORM_ID', HLD_LIVE ? 74 : 60);
}

// 2)
if (! defined('HLD_METABOLIC_PREFUNNEL_FORM_ID')) {
    define('HLD_METABOLIC_PREFUNNEL_FORM_ID', HLD_LIVE ? 72 : 57);
}

// 3)
if (! defined('HLD_PT_141_PREFUNNEL_FORM_ID')) {
    define('HLD_PT_141_PREFUNNEL_FORM_ID', HLD_LIVE ? 0 : 61);
}

// 5)
if (! defined('HLD_TRT_PREFUNNEL_FORM_ID')) {
    define('HLD_TRT_PREFUNNEL_FORM_ID', HLD_LIVE ? 0 : 55);
}




// Action Items -  Questionnaires

if (! defined('HLD_CLINICAL_DIFFERENCE_FORM_ID')) {
    define('HLD_CLINICAL_DIFFERENCE_FORM_ID', HLD_LIVE ? 75 : 59);
}

if (! defined('HLD_METABOLIC_ACTION_ITEM_FORM_ID')) {
    define('HLD_METABOLIC_ACTION_ITEM_FORM_ID', HLD_LIVE ? 73 : 58);
}

if (! defined('HLD_PT_OXYTOCIN_ACTION_ITEM_FORM_ID')) {
    define('HLD_PT_OXYTOCIN_ACTION_ITEM_FORM_ID', HLD_LIVE ? 0 : 64);
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
