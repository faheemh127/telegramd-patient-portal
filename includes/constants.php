<?php
// constants
global $wpdb;
$prefix = $wpdb->prefix . "healsend_";



// Form IDs
if (! defined('HLD_GLP_1_PREFUNNEL_FORM_ID')) {
    define('HLD_GLP_1_PREFUNNEL_FORM_ID', HLD_LIVE ? 56 : 45);
}
// pt-141 57
// trt prefunnel 59
if (! defined('HLD_CLINICAL_DIFFERENCE_FORM_ID')) {
    define('HLD_CLINICAL_DIFFERENCE_FORM_ID', 52);
}

if (! defined('HLD_METABOLIC_PREFUNNEL_FORM_ID')) {
    define('HLD_METABOLIC_PREFUNNEL_FORM_ID', 53);
}



// Questionnaires
if (! defined('QUINST_GLP_1_WEIGHT_LOSS')) {
    define('QUINST_GLP_1_WEIGHT_LOSS', 'quinst::54188482-41ac-4866-afc8-9e498c645d05');
}

if (! defined('QUINST_CLINICAL_DIFFERENCE')) {
    define('QUINST_CLINICAL_DIFFERENCE', 'quinst::1feb5370-69ea-4455-a7a3-78fbe3257c3d');
}

// Patient dashboard URL
if (! defined('HLD_PATIENT_DASHBOARD_URL')) {
    define('HLD_PATIENT_DASHBOARD_URL', home_url('/my-account/'));
}



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
