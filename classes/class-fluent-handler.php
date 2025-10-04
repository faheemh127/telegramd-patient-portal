<?php
if (! class_exists('hldFluentHandler')) {

    class hldFluentHandler
    {
        protected $telegra;
        protected $glp_prefunnel_form_id = 45;

        /**
         * Only forms listed here will trigger Telegra order creation.
         * Add form IDs to this array if they should create an order in Telegra.
         * If a form ID is not in this array, no Telegra order will be created.
         */
        protected $telegra_forms = [45];
        public function __construct($telegra)
        {
            // Register hook when class is instantiated
            $this->telegra = $telegra;
            add_action(
                'fluentform/before_insert_submission',
                [$this, 'handle_before_insert_submission'],
                10,
                2
            );

            add_action('wp_enqueue_scripts', [$this, 'pass_action_item_to_js']);
        }

        /**
         * Get Fluent Form entries for the currently logged-in user.
         *
         * This function checks if a user is logged in, then retrieves their Fluent Form
         * submissions from the `wp_fluentform_submissions` table using their WordPress User ID.
         *
         * - If the user is not logged in, it returns null.
         * - If entries exist, it returns an array of entry objects.
         * - If no entries exist, it returns null.
         *
         * @global wpdb $wpdb WordPress database access object.
         *
         * @return array|null Array of submission objects on success, or null if no entries or not logged in.
         */

        public function pass_action_item_to_js()
        {
            $user_info = [
                'logged_in' => is_user_logged_in(),
                'name'      => '',
                'email'     => '',
                'role'      => '',
            ];

            if (is_user_logged_in()) {
                $current_user = wp_get_current_user();
                $user_info['name']  = $current_user->display_name;
                $user_info['email'] = $current_user->user_email;
                $user_info['role']  = !empty($current_user->roles) ? $current_user->roles[0] : '';
            }

            wp_localize_script(
                'hld-class-navigation',
                'hldActionItem',
                [
                    'glp1Prefunnel' => $this->is_action_item_active() ? true : false,
                    'userInfo'      => $user_info,
                ]
            );
        }



        public function get_patient_entries()
        {
            // Check if the user is logged in
            if (! is_user_logged_in()) {
                return null;
            }

            global $wpdb;
            $current_user_id = get_current_user_id();

            // Fetch entries from Fluent Forms submissions table
            $entries = $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}fluentform_submissions WHERE user_id = %d",
                $current_user_id
            ));

            // Return entries if found, otherwise return null
            if (! empty($entries)) {
                return $entries;
            }

            return null;
        }








        public function get_order_id()
        {
            // Get current user email
            $current_user = wp_get_current_user();
            if (!$current_user || empty($current_user->user_email)) {
                return false;
            }

            $email = $current_user->user_email;

            global $wpdb;
            $table_name = $wpdb->prefix . 'healsend_subscriptions'; // adjust if your table name is different

            // Query to check if row exists with non-empty telegra_order_id
            $order_id = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT telegra_order_id 
             FROM $table_name 
             WHERE patient_email = %s 
               AND telegra_order_id IS NOT NULL 
               AND telegra_order_id != '' 
               AND telegra_order_id LIKE 'order::%%'
             LIMIT 1",
                    $email
                )
            );

            return $order_id;
        }


        public function is_action_item_active()
        {
            // ✅ Get current user
            $current_user = wp_get_current_user();
            if (!$current_user || empty($current_user->user_email)) {
                return false;
            }

            $email = $current_user->user_email;

            global $wpdb;
            $table_name = $wpdb->prefix . 'healsend_subscriptions'; // adjust if needed

            // ✅ Check if table exists
            $table_exists = $wpdb->get_var(
                $wpdb->prepare("SHOW TABLES LIKE %s", $table_name)
            );

            if ($table_exists !== $table_name) {
                error_log("TelegraMD Error: Table '$table_name' does not exist.");
                return false;
            }

            // ✅ Query to check if row exists with non-empty telegra_order_id
            $order_id = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT telegra_order_id 
             FROM $table_name 
             WHERE patient_email = %s 
               AND telegra_order_id IS NOT NULL 
               AND telegra_order_id != '' 
               AND telegra_order_id LIKE %s
             LIMIT 1",
                    $email,
                    $wpdb->esc_like('order::') . '%'
                )
            );

            return !empty($order_id);
        }



        public function update_patient_name($insertData)
        {
            // Check if response exists
            if (empty($insertData['response'])) {
                error_log("update_patient_name: No response found in insertData");
                return;
            }

            // Decode response JSON
            $response = json_decode($insertData['response'], true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                error_log("update_patient_name: Failed to decode response JSON");
                return;
            }

            // Check if 'names' exists
            if (empty($response['names']['first_name']) && empty($response['names']['last_name'])) {
                error_log("update_patient_name: No names found in response");
                return;
            }

            // Check if user is logged in
            if (!is_user_logged_in()) {
                error_log("update_patient_name: User is not logged in");
                return;
            }

            $current_user_id = get_current_user_id();

            // Prepare data to update
            $userdata = array(
                'ID'         => $current_user_id,
                'first_name' => sanitize_text_field($response['names']['first_name']),
                'last_name'  => sanitize_text_field($response['names']['last_name'])
            );

            // Update the user
            $user_id = wp_update_user($userdata);

            if (is_wp_error($user_id)) {
                error_log("update_patient_name: Failed to update user. Error: " . $user_id->get_error_message());
            } else {
                error_log("update_patient_name: Successfully updated user {$current_user_id} with name {$userdata['first_name']} {$userdata['last_name']}");
            }
        }


        public function get_patient_package()
        {
            // Get all patient entries
            $entries = $this->get_patient_entries();

            if (empty($entries)) {
                return null; // No entries found
            }

            // Filter only entries for form_id = 24
            $form_entries = array_filter($entries, function ($entry) {
                return $entry->form_id == 24;
            });

            if (empty($form_entries)) {
                return null; // No form 24 entries
            }

            // Re-index and get the last entry of form_id = 24
            $form_entries = array_values($form_entries);
            $last_entry = end($form_entries);

            if (empty($last_entry->response)) {
                return null; // No response data
            }

            // Decode the JSON response
            $response = json_decode($last_entry->response, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return null; // Invalid JSON
            }

            // Return dropdown_3 (the package type), if it exists
            return $response['dropdown_3'] ?? null;
        }





        /**
         * Get the patient's medication from Fluent Forms entries.
         *
         * - Checks if the user is logged in.
         * - Fetches all entries for the logged-in user.
         * - Filters only entries for form_id = 24 (medication form).
         * - Returns the latest entry's `dropdown_2` value (medication type).
         * - Returns null if no valid data found.
         *
         * @return string|null
         */
        public function get_patient_medication()
        {
            // Get all patient entries
            $entries = $this->get_patient_entries();

            if (empty($entries)) {
                return null; // No entries found
            }

            // Filter only entries for form_id = 24
            $form_entries = array_filter($entries, function ($entry) {
                return $entry->form_id == 24;
            });

            if (empty($form_entries)) {
                return null; // No form 24 entries
            }

            // Re-index and get the last entry of form_id = 24
            $form_entries = array_values($form_entries);
            $last_entry = end($form_entries);

            if (empty($last_entry->response)) {
                return null; // No response data
            }

            // Decode the JSON response
            $response = json_decode($last_entry->response, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return null; // Invalid JSON
            }

            // Return dropdown_2 (the medication field), if it exists
            return $response['dropdown_2'] ?? null;
        }


        public function get_medication_id()
        {
            $medication_name = $this->get_patient_medication();
            $medication_id =  $this->telegra->get_medicine_code($medication_name);
            return $medication_id;
        }



        public function telegra()
        {
            $telegra_patient_id = HLD_Telegra::create_patient();

            if (empty($telegra_patient_id)) {
                error_log("TelegraMD patient ID not found for current user.");
                return;
            }
            error_log("telegra_patient_id is:245" . $telegra_patient_id);
            $medication_id = $this->get_medication_id();
            $this->telegra->create_order(
                $telegra_patient_id,
                $medication_id,
                ["symp::9d65e74b-caed-4b38-b343-d7f84946da60"]
            );
        }

        // public function prepare_questionare_for_telegra($form_data)
        // {
        //     // Hardcoded for testing — later you’ll pass real values
        //     $order_id  = "order::a6807624-9c95-4819-ac61-a956784b02ed";
        //     $quinst_id = "quinst::54188482-41ac-4866-afc8-9e498c645d05";

        //     $answers = [];

        //     foreach ($form_data as $key => $value) {
        //         // Skip non-questionnaire fields
        //         if (strpos($key, 'Glp_intakeform_') !== false) {
        //             $answers[] = [
        //                 'location' => 'loc::' . $key, // prepend loc::
        //                 'value'    => $value
        //             ];
        //         }
        //     }

        //     // Debug log for safety
        //     error_log("[TelegraMD] Prepared Questionnaire Answers → " . print_r($answers, true));

        //     // Now call your submit function (if you want to auto-send)
        //     $result = $this->telegra->submit_questionnaire_answers($order_id, $quinst_id, $answers);

        //     return $result;
        // }


        // public function prepare_questionare_for_telegra($form_data, $quinst_id, $order_id)
        // {
        //     $answers = [];

        //     foreach ($form_data as $key => $value) {
        //         if (strpos($key, 'Glp_intakeform_') === 0) {
        //             // Convert Fluent form keys to Telegra location IDs
        //             $loc_id = str_replace('_', '-', $key); // <-- critical fix
        //             $answers[] = [
        //                 'location' => "loc::{$loc_id}",
        //                 'value'    => $value,
        //             ];
        //         }
        //     }

        //     error_log("[TelegraMD] Prepared Questionnaire Answers → " . print_r($answers, true));

        //     return [
        //         'quinst_id' => $quinst_id,
        //         'order_id'  => $order_id,
        //         'answers'   => $answers,
        //     ];
        // }




        // public function prepare_questionare_for_telegra($form_data)
        // {
        //     // Example — later pass these dynamically
        //     $order_id  = "order::a6807624-9c95-4819-ac61-a956784b02ed";
        //     $quinst_id = "quinst::54188482-41ac-4866-afc8-9e498c645d05";

        //     $answers = [];

        //     foreach ($form_data as $key => $value) {
        //         // Only process keys starting with Glp_intakeform_
        //         if (strpos($key, 'Glp_intakeform_') === 0) {

        //             // First replace triple underscore with colon
        //             $loc_id = str_replace('___', ':', $key);

        //             // Then replace double underscore with dot
        //             $loc_id = str_replace('__', '.', $loc_id);

        //             // Finally replace remaining single underscores with hyphens
        //             $loc_id = str_replace('_', '-', $loc_id);

        //             // Prepend loc::
        //             $answers[] = [
        //                 'location' => "loc::{$loc_id}",
        //                 'value'    => $value
        //             ];
        //         }
        //     }

        //     // Figure out the last location dynamically (no hardcoding)
        //     $last_location = null;
        //     // if (!empty($answers)) {
        //     //     $last_location = end($answers)['location']; // last answered loc
        //     // }

        //     // $last_location = "loc::Glp-intakeform-2";

        //     // Debug log to confirm payload
        //     error_log("[TelegraMD] Prepared Questionnaire Answers → " . print_r($answers, true));
        //     error_log("[TelegraMD] Last Location → " . $last_location);

        //     // Call submit function
        //     $result = $this->telegra->submit_questionnaire_answers($order_id, $quinst_id, $answers, $last_location);




        //     return $result;
        // }





        public function prepare_questionare_for_telegra($form_data)
        {
            // Example — later pass these dynamically
            $order_id  = $this->get_order_id();
            $answers = [];

            /**
             * 1️⃣ Process Glp_intakeform_ questionnaire
             */
            foreach ($form_data as $key => $value) {
                // Only process keys starting with Glp_intakeform_
                if (strpos($key, 'Glp_intakeform_') === 0) {

                    // Replace special patterns
                    $loc_id = str_replace('___', ':', $key);
                    $loc_id = str_replace('__', '.', $loc_id);
                    $loc_id = str_replace('_', '-', $loc_id);

                    $answers[] = [
                        'location' => "loc::{$loc_id}",
                        'value'    => $value
                    ];
                }
            }

            // For full submission we keep last_location null
            $last_location = null;

            // Debug log
            error_log("[TelegraMD] Intakeform Answers → " . print_r($answers, true));
            error_log("[TelegraMD] Intakeform Last Location → " . $last_location);

            // Submit intakeform questionnaire
            $result = $this->telegra->submit_questionnaire_answers(
                $order_id,
                QUINST_GLP_1_WEIGHT_LOSS,
                $answers,
                $last_location
            );

            /**
             * 2️⃣ Process glp-clinical-difference-intake questionnaire
             */
            $clinical_answers = [];
            foreach ($form_data as $key => $value) {
                // Only process keys starting with glp-clinical-difference-intake
                if (strpos($key, 'glp-clinical-difference-intake') === 0) {

                    // Replace special patterns
                    $loc_id = str_replace('___', ':', $key);
                    $loc_id = str_replace('__', '.', $loc_id);
                    $loc_id = str_replace('_', '-', $loc_id);

                    $clinical_answers[] = [
                        'location' => "loc::{$loc_id}",
                        'value'    => $value
                    ];
                }
            }

            $last_location = null; // full submission

            // Debug log
            error_log("[TelegraMD] Clinical Difference Answers → " . print_r($clinical_answers, true));
            error_log("[TelegraMD] Clinical Difference Last Location → " . $last_location);

            // Submit clinical difference questionnaire
            if (!empty($clinical_answers)) {
                $result = $this->telegra->submit_questionnaire_answers(
                    $order_id,
                    QUINST_CLINICAL_DIFFERENCE, // constant for clinical difference questionnaire
                    $clinical_answers,
                    $last_location
                );
            }

            return $result;
        }




        public function save_patient_form_submission($insertData)
        {
            global $wpdb;
            $table = HEALSEND_PATIENT_FORMS_TABLE;

            // Get current logged-in user
            $current_user = wp_get_current_user();

            if (!$current_user || empty($current_user->user_email)) {
                error_log("No logged-in user found while saving patient form submission.");
                return false;
            }

            // Decode form response
            $responseData = json_decode($insertData['response'], true);

            // Build form name as dummy + form_id
            $formName = 'FluentForm_' . ($insertData['form_id'] ?? 'unknown');

            // Prepare insert data
            $formData = [
                'patient_email' => sanitize_email($current_user->user_email),
                'form_name'     => $formName, // dummy name with form_id
                'form_data'     => wp_json_encode($responseData),
                'created_at'    => current_time('mysql'),
            ];

            $formats = ['%s', '%s', '%s', '%s'];

            $result = $wpdb->insert($table, $formData, $formats);

            if ($result === false) {
                error_log("Failed to save patient form submission: " . $wpdb->last_error);
                return false;
            }

            error_log("Patient form submission saved successfully for {$current_user->user_email}");
            return true;
        }



        // later we will use
        public function save_patient_form_answers($submission_id, $form)
        {
            global $wpdb;
            $table = HEALSEND_FORM_ANSWERS_TABLE;

            // Get logged-in user
            $current_user = wp_get_current_user();
            if (!$current_user || empty($current_user->user_email)) {
                error_log("No logged-in user found while saving patient form answers.");
                return false;
            }

            $patient_email = sanitize_email($current_user->user_email);

            // Loop through form data
            foreach ($form as $key => $value) {
                // Skip internal or hidden fields (like nonce, referer, etc.)
                if (strpos($key, '_fluentform') === 0 || strpos($key, '__') === 0 || $key === '_wp_http_referer') {
                    continue;
                }

                // If value is an array (like names or address), convert to JSON
                if (is_array($value)) {
                    $value = wp_json_encode($value);
                }

                // Insert each answer
                $wpdb->insert(
                    $table,
                    [
                        'submission_id' => (int) $submission_id,
                        'patient_email' => $patient_email,
                        'question_key'  => sanitize_text_field($key),
                        'answer'        => sanitize_textarea_field($value),
                    ],
                    ['%d', '%s', '%s', '%s']
                );

                if ($wpdb->last_error) {
                    error_log("Error saving form answer for {$key}: " . $wpdb->last_error);
                }
            }

            error_log("Patient form answers saved successfully for {$patient_email}, submission_id: {$submission_id}");
            return true;
        }







        /**
         * Callback for FluentForm before insert submission
         *
         * @param array $insertData
         * @param object $form
         */
        public function handle_before_insert_submission($insertData, $form)
        {

            error_log("handle_before_insert_submission called");
            error_log("insertData: " . print_r($insertData, true));
            error_log("form: " . print_r($form, true));
            // No need to do processing if user is not a patient


            if (! is_user_logged_in()) {
                return;
            }




            // First save main submission
            // $this->save_patient_form_submission($insertData);
            // $submission_id = $wpdb->insert_id; // get last inserted ID

            // // Then save answers
            // $form = json_decode($insertData['response'], true);
            // $this->save_patient_form_answers($submission_id, $form);




            $form_id = $insertData['form_id'];
            $this->save_patient_form_submission($insertData);

            if ($form_id == HLD_CLINICAL_DIFFERENCE_FORM_ID) {
                $this->prepare_questionare_for_telegra($form);
            }


            if ($form_id == HLD_GLP_1_PREFUNNEL_FORM_ID) {
                $this->update_patient_name($insertData);
            }


            // if not form can create patient create telegra order return
            if (in_array($form_id, $this->telegra_forms)) {
                error_log("form_id is allowed");
                $this->telegra();
            }
        }
    }
}

// Create an object so the hook runs
$hld_fluent_handler = new hldFluentHandler($hld_telegra);
