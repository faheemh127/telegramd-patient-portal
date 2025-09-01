<?php
if (! class_exists('hldFluentHandler')) {

    class hldFluentHandler
    {
        protected $telegra;
        protected $glp_prefunnel_form_id = 24;

        /**
         * Only forms listed here will trigger Telegra order creation.
         * Add form IDs to this array if they should create an order in Telegra.
         * If a form ID is not in this array, no Telegra order will be created.
         */
        protected $telegra_forms = [17, 16];
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
        }

        public function activate_action_item()
        {
            $option_name = 'hld_action_item_form_' . $this->glp_prefunnel_form_id;
            update_option($option_name, true);
        }

        public function is_action_item_active()
        {
            $option_name = 'hld_action_item_form_' . $this->glp_prefunnel_form_id;
            return (bool) get_option($option_name, false);
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

            if (is_user_logged_in()) {
                $user_id = get_current_user_id();
                error_log("✅ Logged in User ID: " . $user_id);
            } else {
                error_log("❌ No user logged in.");
                return;
            }




            $form_id = $insertData['form_id'];
            if ($form_id == $this->glp_prefunnel_form_id) {
                $this->activate_action_item();
            }
            if (! in_array($form_id, $this->telegra_forms)) {
                return;
            }

            error_log("form_id is allowed");



            // create_patient_if_not_exists_on_telegra_md();

            $this->telegra->create_patient();
            $telegra_patient_id = $this->telegra->get_patient_id();
            if (empty($telegra_patient_id)) {
                error_log("TelegraMD patient ID not found for current user.");
                return;
            }
            error_log("telegra_patient_id " . $telegra_patient_id);
            $this->telegra->create_order($telegra_patient_id);
        }
    }
}

// Create an object so the hook runs
$hld_fluent_handler = new hldFluentHandler($telegra);
