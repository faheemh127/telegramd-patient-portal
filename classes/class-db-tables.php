<?php
if (! defined('ABSPATH')) exit;

class HLD_DB_Tables
{

    private static $tables = [];

    public static function get_table($name)
    {
        return isset(self::$tables[$name]) ? self::$tables[$name] : null;
    }

    public static function create_tables()
    {
        global $wpdb;
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        HLD_UserSubscriptions::create_table_if_not_exists();

        $charset_collate = $wpdb->get_charset_collate();
        $prefix = $wpdb->prefix . "healsend_";

        self::$tables['patients'] = "{$prefix}patients";
        self::$tables['payments'] = "{$prefix}payments";
        self::$tables['patient_forms'] = "{$prefix}patient_forms";
        self::$tables['patient_form_answers'] = "{$prefix}patient_form_answers";
        self::$tables['action_items'] = "{$prefix}action_items";
        self::$tables['user_actions'] = "{$prefix}user_actions";

        $sql = [];

        // Patients Table
        $sql[] = "CREATE TABLE IF NOT EXISTS " . self::$tables['patients'] . " (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    patient_uuid CHAR(36) NOT NULL UNIQUE,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    dob DATE NULL,
    contact_email VARCHAR(255),
    patient_email VARCHAR(255) NOT NULL UNIQUE,
    phone VARCHAR(50),
    medical_history JSON,
    telegra_patient_id VARCHAR(255),
    notes TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_deleted TINYINT(1) DEFAULT 0
) $charset_collate;";

        // Payments Table
        $sql[] = "CREATE TABLE IF NOT EXISTS " . self::$tables['payments'] . " (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    patient_email VARCHAR(255) NOT NULL,
    payment_token VARCHAR(255) NOT NULL,
    card_last4 VARCHAR(4),
    card_brand VARCHAR(50),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_email) REFERENCES " . self::$tables['patients'] . "(patient_email)
) $charset_collate;";

        // Patient Forms Table
        $sql[] = "CREATE TABLE IF NOT EXISTS " . self::$tables['patient_forms'] . " (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    patient_email VARCHAR(255) NOT NULL,
    form_name VARCHAR(100) NOT NULL,
    form_data JSON NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_email) REFERENCES " . self::$tables['patients'] . "(patient_email)
) $charset_collate;";

        // Patient Form Answers Table
        $sql[] = "CREATE TABLE IF NOT EXISTS " . self::$tables['patient_form_answers'] . " (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    submission_id BIGINT UNSIGNED NOT NULL,
    patient_email VARCHAR(255) NOT NULL,
    question_key VARCHAR(100) NOT NULL,
    answer TEXT,
    FOREIGN KEY (submission_id) REFERENCES " . self::$tables['patient_forms'] . "(id),
    FOREIGN KEY (patient_email) REFERENCES " . self::$tables['patients'] . "(patient_email)
) $charset_collate;";





        // Action Items Table
        $sql[] = "CREATE TABLE IF NOT EXISTS " . self::$tables['action_items'] . " (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    plan_slug VARCHAR(100) NOT NULL,               -- e.g. 'glp_1_prefunnel', 'metabolic'
    action_key VARCHAR(100) NOT NULL,              -- e.g. 'id_upload', 'clinical_diff', 'agreement'
    label VARCHAR(255) NOT NULL,                   -- Human readable name
    description TEXT NULL,                         -- Optional detailed description
    item_slug VARCHAR(255) NOT NULL,               -- e.g. 'glp_1_weight_loss_intake'
    sort_order INT DEFAULT 0,
    required TINYINT(1) DEFAULT 1,
    UNIQUE KEY unique_plan_action (plan_slug, action_key)
) $charset_collate;";

        // User Actions Table
        $sql[] = "CREATE TABLE IF NOT EXISTS " . self::$tables['user_actions'] . " (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    patient_email VARCHAR(255) NOT NULL,
    plan_slug VARCHAR(100) NOT NULL,
    action_key VARCHAR(100) NOT NULL,
    status ENUM('pending', 'completed') DEFAULT 'pending',
    completed_at DATETIME NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_user_action (patient_email, plan_slug, action_key)
) $charset_collate;";




        foreach ($sql as $query) {
            dbDelta($query);
        }
    }







    /**
     * Add stripe_customer_id column to the patients table if it does not exist.
     * i think this function is not required anymore because the code for
     * --create column already written in class-stripe when we create and update stripe_id in patients tabel
     */
    public static function hld_add_stripe_customer_id_column()
    {
        global $wpdb;

        $table_name = HEALSEND_PATIENTS_TABLE;
        $column_name = 'stripe_customer_id';

        // Check if column already exists
        $column_exists = $wpdb->get_results(
            $wpdb->prepare(
                "SHOW COLUMNS FROM `$table_name` LIKE %s",
                $column_name
            )
        );

        if (empty($column_exists)) {
            // Column does not exist — add it
            $alter_query = "ALTER TABLE `$table_name` ADD `$column_name` VARCHAR(255) DEFAULT NULL AFTER `telegra_patient_id`;";
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
            $wpdb->query($alter_query);

            error_log("✅ Added column `$column_name` to `$table_name` successfully.");
        } else {
            error_log("ℹ️ Column `$column_name` already exists in `$table_name`.");
        }
    }
}



// HLD_DB_Tables::hld_add_stripe_customer_id_column();