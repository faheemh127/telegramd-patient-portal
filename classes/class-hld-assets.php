<?php
if (! class_exists('hldAssets')) {

    class hldAssets
    {

        public function __construct()
        {
            // Hook into wp_enqueue_scripts
            add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
            add_action('wp_enqueue_scripts', [$this, 'enqueue_stripe']);
        }

        /**
         * Enqueue plugin CSS & JS assets
         */
        public function enqueue_assets()
        {


            // Replace YOUR_API_KEY with your actual Google Places API key
            wp_enqueue_script(
                'google-places-api',
                // 'https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places',
                'https://maps.googleapis.com/maps/api/js?key=AIzaSyCMrx4VCbwXkdIQqEo64zC8I_gTEJZXMpk&libraries=places',
                array(),
                null,
                true
            );


            // Styles
            wp_enqueue_style(
                'hld-plugin-custom-css',
                plugin_dir_url(__FILE__) . '../css/custom-style.css',
                [],
                '1.0'
            );
            wp_enqueue_style(
                'hld-plugin-scss',
                plugin_dir_url(__FILE__) . '../css/main.css',
                [],
                '1.0'
            );
            wp_enqueue_style(
                'hld-bootstrap',
                plugin_dir_url(__FILE__) . '../libs/bootstrap.min.css',
                [],
                '1.0'
            );

            // Scripts
            wp_enqueue_script(
                'hld-bootstrap',
                plugin_dir_url(__FILE__) . '../libs/bootstrap.min.js',
                ['jquery'],
                '1.0',
                true
            );

            wp_enqueue_script(
                'hld-custom-js',
                plugin_dir_url(__FILE__) . '../js/custom-script.js',
                ['jquery'],
                '1.0',
                true
            );

            wp_enqueue_script(
                'hld-class-patient-login',
                plugin_dir_url(__FILE__) . '../js/class-patient-login.js',
                ['jquery'],
                '1.0',
                true
            );

            wp_enqueue_script(
                'hld-class-navigation',
                plugin_dir_url(__FILE__) . '../js/class-navigation.js',
                ['jquery'],
                '1.0',
                true
            );

            wp_enqueue_script(
                'class-fluent-form-handler',
                plugin_dir_url(__FILE__) . '../js/class-fluent-form-handler.js',
                ['jquery'],
                '1.0',
                true
            );

            wp_enqueue_script(
                'hld-class-custom-checkbox',
                plugin_dir_url(__FILE__) . '../js/class-custom-checkbox.js',
                ['jquery'],
                '1.0',
                true
            );

            // Localize scripts
            wp_localize_script(
                'hld-class-patient-login',
                'hld_ajax_obj',
                [
                    'ajaxurl' => admin_url('admin-ajax.php'),
                    'nonce'   => wp_create_nonce('hld_patient_login_nonce')
                ]
            );

            $form_id       = 24; // Or dynamically get this
            $active_step_key = 'active_step_fluent_form_' . $form_id;
            $active_step   = get_user_meta(get_current_user_id(), $active_step_key, true);

            wp_localize_script('hld-custom-js', 'hldFormData', [
                'ajaxurl'    => admin_url('admin-ajax.php'),
                'formId'     => $form_id,
                'activeStep' => $active_step,
            ]);

            $current_user = wp_get_current_user();
            $user_email   = is_user_logged_in() ? $current_user->user_email : null;

            wp_localize_script('hld-custom-js', 'hldData', [
                'hldPatientEmail' => $user_email,
            ]);
        }

        /**
         * Enqueue Stripe specific assets
         */
        public function enqueue_stripe()
        {
            wp_enqueue_script('stripe-js', 'https://js.stripe.com/v3/');
            wp_enqueue_script(
                'my-stripe-handler',
                plugin_dir_url(__FILE__) . '../js/stripe-handler.js',
                ['stripe-js'],
                '1.0',
                true
            );

            wp_localize_script('my-stripe-handler', 'MyStripeData', [
                'ajax_url'       => admin_url('admin-ajax.php'),
                'publishableKey' => defined('STRIPE_PUBLISHABLE_KEY') ? STRIPE_PUBLISHABLE_KEY : '',
            ]);
        }
    }
}
new hldAssets();
