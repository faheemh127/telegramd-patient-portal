<?php
if (! class_exists('HLD_Patient')) {

    class HLD_Patient
    {
        /**
         * Return full table name for patients
         */
        public static function get_table_name()
        {
            error_log("table name is 11" . HEALSEND_PATIENTS_TABLE);
            return HEALSEND_PATIENTS_TABLE;
        }


        public static function sync_user_to_patient($user)
        {
            if (!$user || empty($user->user_email)) {
                return false;
            }

            global $wpdb;
            $table = self::get_table_name();
            $email = sanitize_email($user->user_email);

            error_log("Syncing user: {$email} into patient table: {$table}");

            error_log("Patient is logged in 23");


            $email = sanitize_email($user->user_email);

            // Check if patient already exists
            $existing = $wpdb->get_var(
                $wpdb->prepare("SELECT id FROM {$table} WHERE patient_email = %s AND is_deleted = 0 LIMIT 1", $email)
            );

            $first_name = sanitize_text_field($user->first_name ?: get_user_meta($user->ID, 'first_name', true));
            $last_name  = sanitize_text_field($user->last_name  ?: get_user_meta($user->ID, 'last_name', true));

            if ($existing) {
                // Already exists → optionally update names if empty
                $wpdb->update(
                    $table,
                    [
                        'first_name' => $first_name,
                        'last_name'  => $last_name,
                        'contact_email' => $email,
                        'updated_at' => current_time('mysql'),
                    ],
                    ['id' => (int) $existing],
                    ['%s', '%s', '%s', '%s'],
                    ['%d']
                );

                return (int) $existing;
            }

            // Insert new row
            $uuid = function_exists('wp_generate_uuid4') ? wp_generate_uuid4() : uniqid('', true);

            $inserted = $wpdb->insert(
                $table,
                [
                    'patient_uuid'  => $uuid,
                    'first_name'    => sanitize_text_field($user->first_name),
                    'last_name'     => sanitize_text_field($user->last_name),
                    'contact_email' => $email,
                    'patient_email' => $email,
                    'created_at'    => current_time('mysql'),
                    'updated_at'    => current_time('mysql'),
                    'is_deleted'    => 0,
                ],
                ['%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d']
            );

            if ($inserted !== false) {
                return (int) $wpdb->insert_id;
            }

            return false;
        }




        /**
         * Get telegra_patient_id by patient email
         * Returns string ID or null if not found
         */
        public static function get_telegra_patient_id($email)
        {
            if (empty($email) || !is_email($email)) {
                return null;
            }

            global $wpdb;
            $table = self::get_table_name();

            $telegra_id = $wpdb->get_var(
                $wpdb->prepare("SELECT telegra_patient_id FROM {$table} WHERE patient_email = %s AND is_deleted = 0 LIMIT 1", $email)
            );

            return $telegra_id ? $telegra_id : null;
        }

        /**
         * Insert or update telegra_patient_id for given email.
         * If a row exists for the email, it updates telegra_patient_id and returns that patient ID.
         * If not, it inserts a new patient row (with minimal fields) and returns new insert ID.
         * Returns false on invalid input or DB failure.
         */
        public static function set_telegra_patient_id($email, $telegra_id, $first_name = '', $last_name = '')
        {
            if (empty($email) || !is_email($email) || empty($telegra_id)) {
                return false;
            }

            $email      = sanitize_email($email);
            $telegra_id = sanitize_text_field($telegra_id);
            $first_name = sanitize_text_field($first_name);
            $last_name  = sanitize_text_field($last_name);

            global $wpdb;
            $table = self::get_table_name();

            // Check existing row
            $existing = $wpdb->get_row(
                $wpdb->prepare("SELECT id FROM {$table} WHERE patient_email = %s LIMIT 1", $email)
            );

            if ($existing) {
                $updated = $wpdb->update(
                    $table,
                    ['telegra_patient_id' => $telegra_id],
                    ['id' => (int) $existing->id],
                    ['%s'],
                    ['%d']
                );

                // return patient id (existing)
                return (int) $existing->id;
            } else {
                // Insert new row. Ensure required NOT NULL columns are provided (first_name & last_name).
                $uuid = function_exists('wp_generate_uuid4') ? wp_generate_uuid4() : uniqid('', true);

                $inserted = $wpdb->insert(
                    $table,
                    [
                        'patient_uuid'       => $uuid,
                        'first_name'         => $first_name ?: '',
                        'last_name'          => $last_name ?: '',
                        'patient_email'      => $email,
                        'telegra_patient_id' => $telegra_id,
                        'created_at'         => current_time('mysql'),
                        'updated_at'         => current_time('mysql'),
                    ],
                    ['%s', '%s', '%s', '%s', '%s', '%s', '%s']
                );

                if ($inserted !== false) {
                    return (int) $wpdb->insert_id;
                }

                return false;
            }
        }

        /**
         * Helper: return full patient row by email (ARRAY_A) or null
         */
        public static function find_patient_by_email($email)
        {
            if (empty($email) || !is_email($email)) {
                return null;
            }

            global $wpdb;
            $table = self::get_table_name();

            $row = $wpdb->get_row(
                $wpdb->prepare("SELECT * FROM {$table} WHERE patient_email = %s AND is_deleted = 0 LIMIT 1", $email),
                ARRAY_A
            );

            return $row ?: null;
        }



        /**
         * Ensure a patient exists by email.
         * If not found, insert a new row with only the email set.
         * Does nothing if already exists.
         * Returns the patient ID on success, or false on failure.
         */
        public static function ensure_patient_by_email($email)
        {
            if (empty($email) || !is_email($email)) {
                return false;
            }

            global $wpdb;
            $table = self::get_table_name();

            $email = sanitize_email($email);

            // Check if patient already exists
            $existing = $wpdb->get_var(
                $wpdb->prepare("SELECT id FROM {$table} WHERE patient_email = %s LIMIT 1", $email)
            );

            if ($existing) {
                return (int) $existing; // Already exists, return ID
            }

            // Insert new minimal row
            $uuid = function_exists('wp_generate_uuid4') ? wp_generate_uuid4() : uniqid('', true);

            $inserted = $wpdb->insert(
                $table,
                [
                    'patient_uuid'  => $uuid,
                    'patient_email' => $email,
                    'created_at'    => current_time('mysql'),
                    'updated_at'    => current_time('mysql'),
                ],
                ['%s', '%s', '%s', '%s']
            );

            if ($inserted !== false) {
                return (int) $wpdb->insert_id;
            }

            return false;
        }
    }
}
