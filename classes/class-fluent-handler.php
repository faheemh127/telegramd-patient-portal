<?php

if (! class_exists('hldFluentHandler')) {

    class hldFluentHandler
    {
        protected $telegra;

        /**
         * Only forms listed here will trigger Telegra order creation.
         * Add form IDs to this array if they should create an order in Telegra.
         * If a form ID is not in this array, no Telegra order will be created.
         */

        protected $prefunnel_forms_ids = [
            HLD_GLP_1_PREFUNNEL_FORM_ID,
            HLD_METABOLIC_PREFUNNEL_FORM_ID,
            HLD_PT_141_PREFUNNEL_FORM_ID,
            HLD_TRT_PREFUNNEL_FORM_ID,

        ];
        protected $action_items = [
            HLD_CLINICAL_DIFFERENCE_FORM_ID,
            HLD_METABOLIC_ACTION_ITEM_FORM_ID
        ];
        protected $telegra_product_id = null;
        protected $stripe_subscription_id = null;
        protected $medication_name = null;


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

            add_action(
                'fluentform/submission_inserted',
                [$this, 'handle_after_insert_submission'],
                10,
                3
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



        public static function get_patient_entries()
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
            global $wpdb;

            // ✅ Ensure constant is defined
            if (!defined('HEALSEND_USER_ACTIONS_TABLE')) {
                error_log('HEALSEND_USER_ACTIONS_TABLE constant is not defined.');
                return false;
            }

            $table_name = HEALSEND_USER_ACTIONS_TABLE;

            // ✅ Get current user
            $current_user = wp_get_current_user();
            if (!$current_user || empty($current_user->user_email)) {
                return false;
            }

            $email = $current_user->user_email;

            // ✅ Check if table exists
            $table_exists = $wpdb->get_var(
                $wpdb->prepare("SHOW TABLES LIKE %s", $table_name)
            );

            if ($table_exists !== $table_name) {
                error_log("Healsend Error: Table '$table_name' does not exist.");
                return false;
            }

            // ✅ Check if any pending action exists for this user
            $has_pending_action = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT COUNT(*) FROM $table_name 
             WHERE patient_email = %s 
             AND status = 'pending'
             LIMIT 1",
                    $email
                )
            );

            return (bool) $has_pending_action;
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


        public static function hld_convert_image_to_base64($image_url)
        {
            // Remove any escaped slashes
            $image_url = stripslashes($image_url);

            // Get the absolute path from the URL
            $image_path = str_replace(site_url(), ABSPATH, $image_url);

            // Check if the file exists
            if (! file_exists($image_path)) {
                return false; // File not found
            }

            // Get file contents
            $image_data = file_get_contents($image_path);
            if (! $image_data) {
                return false;
            }

            // Get the file type
            $file_type = wp_check_filetype($image_path);

            // Return base64 string
            return 'data:' . $file_type['type'] . ';base64,' . base64_encode($image_data);
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

            return $this->telegra_product_id;
            // $medication_name = $this->get_patient_medication();
            // $medication_id =  $this->telegra->get_medicine_code($medication_name);
            // return $medication_id;
        }



        public function telegra($form_id)
        {
            $telegra_patient_id = HLD_Telegra::create_patient();

            if (empty($telegra_patient_id)) {
                error_log("TelegraMD patient ID not found for current user.");
                return;
            }
            error_log("telegra_patient_id is:245" . $telegra_patient_id);
            $medication_id = $this->get_medication_id();




            // the third parameter we can pass is symptoms at the moment we are ignoring it means empty array default parameter will be used
            // ["symp::9d65e74b-caed-4b38-b343-d7f84946da60"]
            $order_id = $this->telegra->create_order(
                $telegra_patient_id,
                $medication_id
            );

            if (is_user_logged_in()) {
                HLD_UserSubscriptions::update_order_telegra_id($order_id, $this->stripe_subscription_id);
                HLD_UserSubscriptions::update_telegra_product_id($order_id, $this->telegra_product_id, $this->stripe_subscription_id);
            } else {
                error_log("⚠️ User not logged in, cannot save order to user meta.");
            }


            // Assign General Action item that will be for all plans
            HLD_ActionItems_Manager::assign_pending_actions_for_plan(HLD_GENERAL_ACTION_ITEM, $order_id);

            if ($form_id == HLD_GLP_1_PREFUNNEL_FORM_ID) {
                HLD_ActionItems_Manager::assign_pending_actions_for_plan(HLD_GLP_WEIGHT_LOSS_SLUG, $order_id);
            } elseif ($form_id == HLD_METABOLIC_PREFUNNEL_FORM_ID) {
                HLD_ActionItems_Manager::assign_pending_actions_for_plan(HLD_METABOLIC_SLUG, $order_id);
            } else {
                error_log("[Healsend Error]: we cannot fill any action item as form id" . $form_id . " do not match any prefunnel");
            }
        }


        public function prepare_questionare_for_telegra($form_data, $quest_inst, $search_string, $order_id, $last_location = "")
        {




            error_log($last_location);


            // later pass these dynamically
            // $order_id  = $this->get_order_id();
            // $order_id  = "order::a55a22f5-8bdb-4299-87f7-b18eb2a3a405";
            // $last_location = null;
            // error_log("[ORDER ID]" . $order_id);
            // // quinst for clinical difference
            // $quest_inst_id_clinical_difference = $order_detail["questionnaireInstances"][1]["id"];
            // // $quest_inst = "quinst::0cefcecd-d2a7-4763-8989-a78af06bad80";
            // error_log("quinst id for GLP weight loss" . $quest_inst);
            // error_log("quinst id for GLP weight loss" . $quest_inst_id_clinical_difference);




            $answers = [];
            foreach ($form_data as $key => $value) {
                if (strpos($key, $search_string) === 0) {
                    $val = $value;
                    if (is_array($value) && isset($value[0])) {
                        $potential_url = $value[0];
                        if (filter_var($potential_url, FILTER_VALIDATE_URL)) {
                            $file_contents = @file_get_contents($potential_url);
                            if ($file_contents !== false) {
                                $base64_file = base64_encode($file_contents);
                                $val = $base64_file; // Replace the value with the Base64 string
                            } else {
                                error_log("[TelegraMD] Unable to fetch file from URL: {$potential_url}");
                                continue;
                            }
                        } else {
                            $val = $value;
                        }
                    }

                    // Replace special patterns in the key
                    $loc_id = str_replace('___', ':', $key);
                    $loc_id = str_replace('__', '.', $loc_id);
                    $loc_id = str_replace('_', '-', $loc_id);

                    $answers[] = [
                        'location' => "loc::{$loc_id}",
                        'value'    => $val,
                    ];
                }
            }


            error_log("Search string is " . $search_string);
            switch ($search_string) {
                case 'metabolic_enhancement':
                    $data = get_user_meta(get_current_user_id(), 'metabolic-action-data', true);
                    error_log("user questionnare meta data for metabolic");
                    error_log(print_r($data, true));
                    $answers = array_merge($answers, $data);
                    break;
            }
            //   error_log("[TelegraMD] Answers for {$form_type} → " . print_r($answers, true));

            error_log("Answers");
            error_log(print_r($answers, true));
            // Submit the questionnaire answers
            // $last_location = "loc::metabolic-enhancement-8";
            if (!empty($answers)) {
                $result = $this->telegra->submit_questionnaire_answers(
                    $order_id,
                    $quest_inst,
                    $answers,
                    $last_location
                );

                // Debug log for submission result
                // error_log("[TelegraMD] Submission result for {$form_type} → " . print_r($result, true));

                return $result;
            }

            // error_log("[TelegraMD] No answers found for {$form_type}. Nothing to submit.");
            return false;
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


        public function update_patient_info($prefunnel_form_data)
        {
            if (!is_user_logged_in()) {
                error_log('❌ update_patient_info: patient must be logged in.');
                return;
            }

            // Extract data safely from the form
            $first_name = isset($prefunnel_form_data['names']['first_name']) ? sanitize_text_field($prefunnel_form_data['names']['first_name']) : '';
            $last_name  = isset($prefunnel_form_data['names']['last_name']) ? sanitize_text_field($prefunnel_form_data['names']['last_name']) : '';
            $gender     = isset($prefunnel_form_data['dropdown_1']) ? strtolower(sanitize_text_field($prefunnel_form_data['dropdown_1'])) : '';
            $dob        = isset($prefunnel_form_data['datetime']) ? sanitize_text_field($prefunnel_form_data['datetime']) : '';
            $state      = isset($prefunnel_form_data['dropdown']) ? sanitize_text_field($prefunnel_form_data['dropdown']) : '';
            $phone      = isset($prefunnel_form_data['phone']) ? sanitize_text_field($prefunnel_form_data['phone']) : '';
            $address    = isset($prefunnel_form_data['address_1']) ? sanitize_text_field($prefunnel_form_data['address_1']["address_line_1"]) : '';
            $city       = isset($prefunnel_form_data['address_1']) ? sanitize_text_field($prefunnel_form_data['address_1']["city"]) : '';
            $zip_code   = isset($prefunnel_form_data['address_1']) ? sanitize_text_field($prefunnel_form_data['address_1']["zip"]) : '';



            error_log("DOB is" . $dob);
            // Physical metrics
            $height_feet  = isset($prefunnel_form_data['input_text_2']) ? floatval($prefunnel_form_data['input_text_2']) : 0;
            $height_inches = isset($prefunnel_form_data['input_text_4']) ? floatval($prefunnel_form_data['input_text_4']) : 0;
            $weight_lbs   = isset($prefunnel_form_data['input_text_3']) ? floatval($prefunnel_form_data['input_text_3']) : 0;


            $date = DateTime::createFromFormat('m-d-Y', $dob);

            if ($date) {
                $formatted_dob = $date->format('Y-m-d');
                error_log("Formatted DOB: " . $formatted_dob);
            } else {
                error_log("Failed to format DOB: " . $dob);
            }



            // Log extracted data for debugging
            error_log("🧾 update_patient_info extracted data: " . print_r([
                'first_name' => $first_name,
                'last_name' => $last_name,
                'gender' => $gender,
                'dob' => $formatted_dob,
                'state' => $state,
                'phone' => $phone,
                'height_feet' => $height_feet,
                'height_inches' => $height_inches,
                'weight_lbs' => $weight_lbs,
            ], true));

            // Update patient data using your existing methods
            if ($first_name || $last_name) {
                HLD_Patient::update_name($first_name, $last_name);
            }
            if ($gender) {
                HLD_Patient::update_gender($gender);
            }
            if ($formatted_dob) {
                HLD_Patient::update_dob($formatted_dob);
            }
            if ($height_feet || $height_inches || $weight_lbs) {
                HLD_Patient::update_physical_metrics($height_feet, $height_inches, $weight_lbs);
            }
            if ($state) {
                HLD_Patient::update_state($state);
            }
            if ($phone) {
                HLD_Patient::update_phone($phone);
            }
            if ($city) {
                HLD_Patient::update_city($city);
            }

            if ($address) {
                HLD_Patient::update_address($address);
            }

            if ($zip_code) {
                HLD_Patient::update_zip_code($zip_code);
            }


            error_log("✅ Patient info updated successfully for logged-in user.");
        }

        private function is_prefunnel($form_id)
        {
            return in_array($form_id, $this->prefunnel_forms_ids, true);
        }

        private function is_action_item($form_id)
        {
            return in_array($form_id, $this->action_items, true);
        }




        /**
         * Callback for FluentForm before insert submission
         *
         * @param array $insertData
         * @param object $form
         */
        public function handle_before_insert_submission($insertData, $form)
        {


            $gender     = isset($form['dropdown_1']) ? strtolower(sanitize_text_field($form['dropdown_1'])) : '';
            error_log("handle_before_insert_submission called");
            error_log("insertData: " . print_r($insertData, true));
            error_log("form: " . print_r($form, true));
            // No need to do processing if user is not a patient and have not signed up
            if (! is_user_logged_in()) {
                error_log("[Healsend Error] user is not logged in ");
                return;
            }

            $form_id = $insertData['form_id'];

            // receiving telegra_product_id from fluent form prefunnel
            if (!isset($form['telegra_product_id']) || empty($form['telegra_product_id'])) {
                error_log("[Healsend Error] Telegra product id has not been set");
                return;
            } else {
                $this->telegra_product_id = $form['telegra_product_id'];
                $this->medication_name = $form['dropdown_4']; //dropdown_4 is actually the name attribute in prefunnel for product
            }



            if ($this->is_prefunnel($form_id) && (!isset($form['my_stripe_subscription_id']) && empty($form['my_stripe_subscription_id']))) {
                error_log("[Healsend Error] Subscription_id has not been set for form submission of " . $form_id);
                return;
            } else {
                $this->stripe_subscription_id = $form['my_stripe_subscription_id'];
            }




            // For security reasons also save the duplicate copy of fluent form submission
            if ($this->is_prefunnel($form_id)) {
                $this->save_patient_form_submission($insertData);
            } else {
                error_log($form_id . " is not a prefunnel [line 876]");
            }

            // $this->save_patient_form_answers($submission_id, $form);

            // if not form can create patient create telegra order return
            if ($this->is_prefunnel($form_id)) {
                error_log("form is prefunnel so proceed");
                $this->update_patient_info($form);
                //
                switch ($form_id) {
                    case HLD_METABOLIC_PREFUNNEL_FORM_ID:
                        $data = [];
                        $data[] = ['location' => 'loc::metabolic-enhancement-1', 'value' => $form['checkbox']];

                        if ($gender == 'female') {
                            $data[] = ['location' => 'loc::metabolic-enhancement-9', 'value' => $form['input_radio']];
                        } else {
                            $data[] = ['location' => 'loc::metabolic-enhancement-9', 'value' => "4ad305b7"];
                        }

                        $data[] = ['location' => 'loc::metabolic-enhancement-7', 'value' => $form['input_radio_2']];

                        $uid = wp_get_current_user()->ID;
                        update_user_meta($uid, 'metabolic-action-data', $data);
                        break;
                    default:
                        break;
                }
                HLD_Telegra::create_patient();
                // HLD_Patient::prepareGHLContact();
                $this->telegra($form_id);
            } else {
                error_log("The Form id" . $form_id . " not exists in telegra_forms. that's why we cannot create any order on telegra with this form");
            }
        }



        public function handle_after_insert_submission($insert_id, $form)
        {
            error_log("aftersubmission form 853");
            error_log(print_r($insert_id, true));
            error_log(print_r($form, true));


            // 1️⃣ Retrieve and sanitize order ID
            $telegra_order_id = isset($form['telegra_order_id']) ? sanitize_text_field($form['telegra_order_id']) : '';
            $telegra_order_id = isset($form['telegra_order_id']) ? sanitize_text_field($form['telegra_order_id']) : '';
            if (empty($telegra_order_id)) {
                error_log("[TelegraMD] Order ID not found. Cannot submit questionnaire.");
                return;
            }

            // 2️⃣ Decode base64 JSON string safely
            $telegra_quinst_data_raw = isset($form['telegra_quinst_data']) ? $form['telegra_quinst_data'] : '';
            $decoded_json = base64_decode($telegra_quinst_data_raw, true);
            if ($decoded_json === false) {
                error_log("[TelegraMD] Failed to base64 decode telegra_quinst_data");
                return;
            }

            $telegra_quinst_data = json_decode($decoded_json, true);
            if (json_last_error() !== JSON_ERROR_NONE || !is_array($telegra_quinst_data)) {
                error_log("[TelegraMD] Invalid JSON in telegra_quinst_data: " . json_last_error_msg());
                return;
            }

            // 3️⃣ Get order details once
            $order_detail = $this->telegra->get_order($telegra_order_id);

            // 4️⃣ Loop through each object and process
            foreach ($telegra_quinst_data as $item) {
                $quinst_index = isset($item['quinst_array_index']) ? intval($item['quinst_array_index']) : null;
                $telegra_location_key = isset($item['telegra_location_key']) ? sanitize_text_field($item['telegra_location_key']) : '';
                $last_location = isset($item['last_location']) ? sanitize_text_field($item['last_location']) : '';
                $action_key = isset($item['action_key']) ? sanitize_text_field($item['action_key']) : '';

                if ($quinst_index === null || empty($telegra_location_key) || empty($last_location)) {
                    error_log("[TelegraMD] Skipped invalid entry in telegra_quinst_data");
                    continue;
                }

                // Safety: ensure that index exists
                if (!isset($order_detail["questionnaireInstances"][$quinst_index]["id"])) {
                    error_log("[TelegraMD] Questionnaire index {$quinst_index} not found in order detail");
                    continue;
                }

                $quest_inst = $order_detail["questionnaireInstances"][$quinst_index]["id"];

                // 5️⃣ Call prepare_questionare_for_telegra for each object




                $this->prepare_questionare_for_telegra(
                    $form,
                    $quest_inst,
                    $telegra_location_key,
                    $telegra_order_id,
                    $last_location
                );


                HLD_ActionItems_Manager::mark_action_item_completed($telegra_order_id, $action_key);

                error_log("[TelegraMD] Submitted questionnaire {$quest_inst} for location {$telegra_location_key}");
            }
        }
    }
}

// Create an object so the hook runs
$hld_fluent_handler = new hldFluentHandler($hld_telegra);


// add_action("init", function () {
//     // $es = hldFluentHandler::get_patient_entries();
//     // error_log(print_r($es, true));
//     $image_url = 'http:\/\/localhost\/server\/healsend-new\/wp-content\/uploads\/fluentform\/ff-2c3effc6a02e44b02ac8f30909388b25-ff-4e35eaed-f8fa-482b-8609-2a6f168df0e9.png';
//     $base64_image = hldFluentHandler::hld_convert_image_to_base64( $image_url );
//     if ( $base64_image ) {
//         echo '<img src="' . esc_attr( $base64_image ) . '" alt="Converted Image">';
//         error_log($base64_image);
//     } else {
//         echo 'Image not found or failed to convert.';
//     }
// });
