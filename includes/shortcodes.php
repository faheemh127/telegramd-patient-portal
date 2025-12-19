<?php
if (! class_exists('hldShortcode')) {
    class hldShortcode
    {
        public function __construct()
        {
            add_action('init', [$this, 'register_shortcodes']);
        }

        public function register_shortcodes()
        {
            add_shortcode('healsend_form', [$this, 'healsend_form_shortcode']);
        }


        /**
         * Shortcode handler
         * Usage: [healsend_form id="21" payment="later" medications='[...]']
         */
        public function healsend_form_shortcode($atts)
        {
            $atts = shortcode_atts([
                'id'     => '',
                'medications' => '[]', // Default to an empty JSON array
                'slug'        => '',
                'type'        => 'prefunnel',
            ], $atts, 'healsend_form');

            if ($atts['type'] != "prefunnel") {
                $eligible = HLD_Patient::is_postfunnel_eligible($atts["slug"]);
                if (!$eligible) {
                    ob_start();
                    require_once HLD_PLUGIN_PATH . 'templates/post-funnel-eligibility.php';
                    return ob_get_clean();
                }
            }

            $decoded_json = base64_decode($atts['medications']);
            $medications_data = json_decode($decoded_json, true); // object

            wp_localize_script(
                'class-fluent-form-handler',
                'fluentFormData', // JS object name
                [
                    'medications' => $medications_data,
                    'form_id'     => $atts['id'] ?? '',
                    'showLoadingBar' => true,
                    'slug' => $atts['slug'] ?? '',
                ],
            );



            /**
             * If klarna and afterpay payment successfull
             * it will return payment-success on klarna and afterpay payment success
             */

            $class_payment_success = HLD_Stripe::get_loading_class();




            ob_start();
            echo do_shortcode('[hld_navbar]'); ?>
            <div class="hld_form_container">
                <?php
                if (!empty($class_payment_success) && $class_payment_success == "payment-success") {
                    require_once HLD_PLUGIN_PATH . "templates/klarna-afterpay-success-processing.php";
                }
                ?>
                <div class="hld_form_wrap <?php echo $class_payment_success; ?>">
                    <div class="hld_form_wrap_hidden">
                        <?php
                        echo do_shortcode('[fluentform id="' . intval($atts['id']) . '"]');
                        echo do_shortcode('[hld_footer]');
                        ?>
                    </div>
                </div>
            </div>


<?php
            return ob_get_clean();
        }
    }

    // instantiate on plugins loaded (or just instantiate now)
    new hldShortcode();
}
