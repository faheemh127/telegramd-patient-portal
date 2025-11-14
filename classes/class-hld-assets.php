<?php

if (!class_exists('hldAssets')) {

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
            // Google Places API
            wp_enqueue_script(
                'google-places-api',
                'https://maps.googleapis.com/maps/api/js?key=AIzaSyCMrx4VCbwXkdIQqEo64zC8I_gTEJZXMpk&libraries=places',
                [],
                null,
                true
            );


            // Datepicker for desktop
            // wp_enqueue_script(
            //     'slim-date-picker',
            //     'https://code.jquery.com/jquery-3.6.3.slim.min.js',
            //     [],
            //     null,
            //     true
            // );


            wp_enqueue_script(
                'jquery-dOb-js',
                plugin_dir_url(__FILE__) . '../js/jquery.dOb.js',
                [],
                HLD_PLUGIN_VERSION,
                true
            );



            // Styles
            wp_enqueue_style(
                'hld-plugin-custom-css',
                plugin_dir_url(__FILE__) . '../css/custom-style.css',
                [],
                HLD_PLUGIN_VERSION
            );
            wp_enqueue_style(
                'hld-plugin-scss',
                plugin_dir_url(__FILE__) . '../css/main.css',
                [],
                HLD_PLUGIN_VERSION
            );
            wp_enqueue_style(
                'hld-bootstrap',
                plugin_dir_url(__FILE__) . '../libs/bootstrap.min.css',
                [],
                HLD_PLUGIN_VERSION
            );

            // Scripts
            wp_enqueue_script(
                'hld-bootstrap',
                plugin_dir_url(__FILE__) . '../libs/bootstrap.min.js',
                ['jquery'],
                HLD_PLUGIN_VERSION,
                true
            );

            wp_enqueue_script(
                'hld-custom-js',
                plugin_dir_url(__FILE__) . '../js/custom-script.js',
                ['jquery'],
                HLD_PLUGIN_VERSION,
                true
            );

            wp_enqueue_script(
                'hld-class-patient-login',
                plugin_dir_url(__FILE__) . '../js/class-patient-login.js',
                ['jquery'],
                HLD_PLUGIN_VERSION,
                true
            );

            wp_localize_script('hld-class-patient-login', 'hld_ajax_obj', [
                'ajaxurl'    => admin_url('admin-ajax.php'),
                'nonce'      => wp_create_nonce('hld_ajax_nonce'),
                'logged_in'  => is_user_logged_in(),
            ]);

            wp_enqueue_script(
                'hld-class-telegra',
                plugin_dir_url(__FILE__) . '../js/class-telegra.js',
                ['jquery'],
                HLD_PLUGIN_VERSION,
                true
            );

            wp_localize_script('hld-class-telegra', 'hld_ajax_obj', [
                'ajaxurl'    => admin_url('admin-ajax.php'),
                'nonce'      => wp_create_nonce('hld_ajax_nonce'),
            ]);



            wp_enqueue_script(
                'hld-class-navigation',
                plugin_dir_url(__FILE__) . '../js/class-navigation.js',
                ['jquery'],
                HLD_PLUGIN_VERSION,
                true
            );

            // Pass home URL to the script
            wp_localize_script(
                'hld-class-navigation',
                'hldClassNavData',
                [
                    'homeUrl' => home_url(),
                ]
            );


            wp_enqueue_script(
                'class-fluent-form-handler',
                plugin_dir_url(__FILE__) . '../js/class-fluent-form-handler.js',
                ['jquery'],
                HLD_PLUGIN_VERSION,
                true
            );

            wp_enqueue_script(
                'hld-class-custom-checkbox',
                plugin_dir_url(__FILE__) . '../js/class-custom-checkbox.js',
                ['jquery'],
                HLD_PLUGIN_VERSION,
                true
            );

            wp_enqueue_script(
                'rolldate-min-js',
                plugin_dir_url(__FILE__) . '../js/rolldate.min.js',
                [],
                HLD_PLUGIN_VERSION,
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

            $form_id         = 24; // Or dynamically get this
            $active_step_key = 'active_step_fluent_form_' . $form_id;
            $active_step     = get_user_meta(get_current_user_id(), $active_step_key, true);

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
            wp_enqueue_script('stripe-js', 'https://js.stripe.com/v3/', [], HLD_PLUGIN_VERSION, true);

            wp_enqueue_script(
                'my-stripe-handler',
                plugin_dir_url(__FILE__) . '../js/stripe-handler.js',
                ['stripe-js'],
                HLD_PLUGIN_VERSION,
                true
            );

            wp_localize_script('my-stripe-handler', 'MyStripeData', [
                'ajax_url'       => admin_url('admin-ajax.php'),
                'publishableKey' => defined('STRIPE_PUBLISHABLE_KEY') ? STRIPE_PUBLISHABLE_KEY : '',
                'prefunnelFormId' => HLD_GLP_1_PREFUNNEL_FORM_ID, // pass the constant
            ]);
        }
    }
}

new hldAssets();
