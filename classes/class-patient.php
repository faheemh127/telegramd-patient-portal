<?php
if (! class_exists('HLD_Patient')) {

    class HLD_Patient
    {
        /**
         * Return full table name for patients
         */
        public static function get_table_name()
        {

            return HEALSEND_PATIENTS_TABLE;
        }




        /**
         * Update patient DOB for logged-in user
         */
        public static function update_dob($dob)
        {
            if (!is_user_logged_in()) {
                error_log('HLD_Patient Error: update_dob() called but patient not logged in.');
                return false;
            }

            $current_user = wp_get_current_user();
            $patient_email = $current_user->user_email;

            global $wpdb;
            $table = self::get_table_name();

            $updated = $wpdb->update(
                $table,
                ['dob' => sanitize_text_field($dob), 'updated_at' => current_time('mysql')],
                ['patient_email' => $patient_email],
                ['%s', '%s'],
                ['%s']
            );

            return $updated !== false;
        }


        /**
         * Update patient first and/or last name
         */
        public static function update_name($first_name = '', $last_name = '')
        {
            if (!is_user_logged_in()) {
                error_log('HLD_Patient Error: update_name() called but patient not logged in.');
                return false;
            }

            $current_user = wp_get_current_user();
            $patient_email = $current_user->user_email;

            $data = [];
            $format = [];

            if (!empty($first_name)) {
                $data['first_name'] = sanitize_text_field($first_name);
                $format[] = '%s';
            }
            if (!empty($last_name)) {
                $data['last_name'] = sanitize_text_field($last_name);
                $format[] = '%s';
            }

            if (empty($data)) {
                return false;
            }

            $data['updated_at'] = current_time('mysql');
            $format[] = '%s';

            global $wpdb;
            $table = self::get_table_name();

            $updated = $wpdb->update(
                $table,
                $data,
                ['patient_email' => $patient_email],
                $format,
                ['%s']
            );

            return $updated !== false;
        }


        /**
         * Update patient gender
         */
        public static function update_gender($gender)
        {
            if (!is_user_logged_in()) {
                error_log('HLD_Patient Error: update_gender() called but patient not logged in.');
                return false;
            }

            $allowed = ['male', 'female', 'other'];
            if (!in_array(strtolower($gender), $allowed)) {
                error_log('HLD_Patient Error: Invalid gender value provided.');
                return false;
            }

            $current_user = wp_get_current_user();
            $patient_email = $current_user->user_email;

            global $wpdb;
            $table = self::get_table_name();

            $updated = $wpdb->update(
                $table,
                ['gender' => strtolower($gender), 'updated_at' => current_time('mysql')],
                ['patient_email' => $patient_email],
                ['%s', '%s'],
                ['%s']
            );

            return $updated !== false;
        }


        /**
         * Update height (feet, inches) and weight
         */
        public static function update_physical_metrics($feet = null, $inches = null, $weight = null)
        {
            if (!is_user_logged_in()) {
                error_log('HLD_Patient Error: update_physical_metrics() called but patient not logged in.');
                return false;
            }

            $current_user = wp_get_current_user();
            $patient_email = $current_user->user_email;

            $data = [];
            $format = [];

            if (!is_null($feet)) {
                $data['feet'] = (int) $feet;
                $format[] = '%d';
            }
            if (!is_null($inches)) {
                $data['inches'] = (int) $inches;
                $format[] = '%d';
            }
            if (!is_null($weight)) {
                $data['weight'] = (float) $weight;
                $format[] = '%f';
            }

            if (empty($data)) {
                return false;
            }

            $data['updated_at'] = current_time('mysql');
            $format[] = '%s';

            global $wpdb;
            $table = self::get_table_name();

            $updated = $wpdb->update(
                $table,
                $data,
                ['patient_email' => $patient_email],
                $format,
                ['%s']
            );

            return $updated !== false;
        }


        /**
         * Update patient state
         */
        public static function update_state($state)
        {
            if (!is_user_logged_in()) {
                error_log('HLD_Patient Error: update_state() called but patient not logged in.');
                return false;
            }

            $current_user = wp_get_current_user();
            $patient_email = $current_user->user_email;

            global $wpdb;
            $table = self::get_table_name();

            $updated = $wpdb->update(
                $table,
                ['state' => sanitize_text_field($state), 'updated_at' => current_time('mysql')],
                ['patient_email' => $patient_email],
                ['%s', '%s'],
                ['%s']
            );

            return $updated !== false;
        }


        /**
         * Update patient phone
         */
        public static function update_phone($phone)
        {
            if (!is_user_logged_in()) {
                error_log('HLD_Patient Error: update_phone() called but patient not logged in.');
                return false;
            }

            $current_user = wp_get_current_user();
            $patient_email = $current_user->user_email;

            global $wpdb;
            $table = self::get_table_name();

            $updated = $wpdb->update(
                $table,
                ['phone' => sanitize_text_field($phone), 'updated_at' => current_time('mysql')],
                ['patient_email' => $patient_email],
                ['%s', '%s'],
                ['%s']
            );

            return $updated !== false;
        }


        /**
         * Update patient notes
         */
        public static function update_notes($notes)
        {
            if (!is_user_logged_in()) {
                error_log('HLD_Patient Error: update_notes() called but patient not logged in.');
                return false;
            }

            $current_user = wp_get_current_user();
            $patient_email = $current_user->user_email;

            global $wpdb;
            $table = self::get_table_name();

            $updated = $wpdb->update(
                $table,
                ['notes' => wp_kses_post($notes), 'updated_at' => current_time('mysql')],
                ['patient_email' => $patient_email],
                ['%s', '%s'],
                ['%s']
            );

            return $updated !== false;
        }




        public static function get_patient_info()
        {
            // Check if user is logged in
            if (! is_user_logged_in()) {
                error_log('HLD_Patient Error: Patient should be logged in to fetch info.');
                return [];
            }

            $current_user = wp_get_current_user();
            $patient_email = $current_user->user_email;

            global $wpdb;
            $table = HEALSEND_PATIENTS_TABLE;

            // Fetch patient record by email
            $patient_row = $wpdb->get_row(
                $wpdb->prepare("SELECT * FROM {$table} WHERE patient_email = %s AND is_deleted = 0", $patient_email)
            );

            // If no patient found, log it
            if (! $patient_row) {
                error_log("HLD_Patient Warning: No patient found in patients table for email {$patient_email}");
                return [];
            }

            // Build return array
            $patient = [
                'first_name' => $patient_row->first_name,
                'last_name'  => $patient_row->last_name,
                'full_name'  => trim($patient_row->first_name . ' ' . $patient_row->last_name),
                'gender'     => $patient_row->gender ?: 'unknown',
                'dob'        => $patient_row->dob ?: null,
                'email'      => $patient_row->patient_email,
                'phone'      => $patient_row->phone ?: '',
                'contact_email' => $patient_row->contact_email,
                'telegra_patient_id' => $patient_row->telegra_patient_id,
                'notes'      => $patient_row->notes,
                'medical_history' => $patient_row->medical_history ? json_decode($patient_row->medical_history, true) : [],
                'created_at' => $patient_row->created_at,
                'updated_at' => $patient_row->updated_at,
            ];

            return $patient;
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
        public static function get_telegra_patient_id($email = null)
        {
            // If email is not passed, try to get current logged-in user email
            if (empty($email)) {
                $current_user = wp_get_current_user();
                if ($current_user && !empty($current_user->user_email)) {
                    $email = $current_user->user_email;
                }
            }

            // Validate email
            if (empty($email) || !is_email($email)) {
                return null;
            }

            global $wpdb;
            $table = self::get_table_name();

            $telegra_id = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT telegra_patient_id 
             FROM {$table} 
             WHERE patient_email = %s 
             AND is_deleted = 0 
             LIMIT 1",
                    $email
                )
            );

            return $telegra_id ?: null;
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
