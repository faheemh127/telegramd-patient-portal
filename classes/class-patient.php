<?php
if (! class_exists('HLD_Patient')) {

    class HLD_Patient
    {
        /**
         * Return full table name for patients
         */
        public static function get_table_name()
        {
            global $wpdb;
            return $wpdb->prefix . 'patients';
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
    }
}
