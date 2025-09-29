<?php
if (! defined('ABSPATH')) exit;

class HLD_DB_Tables
{

    private static $tables = [];

    public static function create_tables()
    {
        global $wpdb;
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $charset_collate = $wpdb->get_charset_collate();
        $prefix = $wpdb->prefix . "healsend_";

        self::$tables['patients'] = "{$prefix}patients";
        self::$tables['payments'] = "{$prefix}payments";
        self::$tables['patient_forms'] = "{$prefix}patient_forms";
        self::$tables['patient_form_answers'] = "{$prefix}patient_form_answers";

        $sql = [];

        // Patients Table
        $sql[] = "CREATE TABLE IF NOT EXISTS " . self::$tables['patients'] . " (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            patient_uuid CHAR(36) NOT NULL UNIQUE,
            first_name VARCHAR(100) NOT NULL,
            last_name VARCHAR(100) NOT NULL,
            dob DATE NULL,
            contact_email VARCHAR(255),
            patient_email VARCHAR(255) NOT NULL,
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
            patient_id BIGINT UNSIGNED NOT NULL,
            patient_email VARCHAR(255) NOT NULL,
            payment_token VARCHAR(255) NOT NULL,
            card_last4 VARCHAR(4),
            card_brand VARCHAR(50),
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (patient_id) REFERENCES " . self::$tables['patients'] . "(id)
        ) $charset_collate;";

        // Patient Forms Table
        $sql[] = "CREATE TABLE IF NOT EXISTS " . self::$tables['patient_forms'] . " (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            patient_id BIGINT UNSIGNED NOT NULL,
            patient_email VARCHAR(255) NOT NULL,
            form_name VARCHAR(100) NOT NULL,
            form_data JSON NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (patient_id) REFERENCES " . self::$tables['patients'] . "(id)
        ) $charset_collate;";

        // Patient Form Answers Table
        $sql[] = "CREATE TABLE IF NOT EXISTS " . self::$tables['patient_form_answers'] . " (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            submission_id BIGINT UNSIGNED NOT NULL,
            patient_email VARCHAR(255) NOT NULL,
            question_key VARCHAR(100) NOT NULL,
            answer TEXT,
            FOREIGN KEY (submission_id) REFERENCES " . self::$tables['patient_forms'] . "(id)
        ) $charset_collate;";

        foreach ($sql as $query) {
            dbDelta($query);
        }
    }
}
