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

            wp_localize_script(
                'hld-class-navigation',
                'hldActionItem',
                [
                    'glp1Prefunnel' => $this->is_action_item_active() ? true : false,
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





        public function activate_action_item()
        {
            $option_name = 'hld_action_item_form_' . $this->glp_prefunnel_form_id;
            update_option($option_name, true);
        }

        public function is_action_item_active()
        {
            return true;
            $option_name = 'hld_action_item_form_' . $this->glp_prefunnel_form_id;
            return (bool) get_option($option_name, false);
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




        public function prepare_questionare_for_telegra($form_data)
        {
            // Example — later pass these dynamically
            $order_id  = "order::a6807624-9c95-4819-ac61-a956784b02ed";
            $quinst_id = "quinst::54188482-41ac-4866-afc8-9e498c645d05";

            $answers = [];

            foreach ($form_data as $key => $value) {
                // Only process keys starting with Glp_intakeform_
                if (strpos($key, 'Glp_intakeform_') === 0) {
                    // Convert underscores to hyphens (Telegra expects this format)
                    $loc_id = str_replace('_', '-', $key);

                    $answers[] = [
                        'location' => "loc::{$loc_id}",
                        'value'    => $value
                    ];
                }
            }

            // Figure out the last location dynamically (no hardcoding)
            $last_location = null;
            if (!empty($answers)) {
                $last_location = end($answers)['location']; // last answered loc
            }

            // Debug log to confirm payload
            error_log("[TelegraMD] Prepared Questionnaire Answers → " . print_r($answers, true));
            error_log("[TelegraMD] Last Location → " . $last_location);

            // Call submit function
            $result = $this->telegra->submit_questionnaire_answers($order_id, $quinst_id, $answers, $last_location);

            return $result;
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


            if (true) {
                $this->prepare_questionare_for_telegra($form);
            }

            $form_id = $insertData['form_id'];
            if ($form_id == $this->glp_prefunnel_form_id) {
                $this->activate_action_item();
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
