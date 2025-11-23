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
            add_shortcode('hld_glp_prefunnel', [$this, 'hld_glp_prefunnel_shortcode']);
        }


        /**
         * Shortcode handler
         * Usage: [hld_glp_prefunnel form_id="21" payment="later" medications='[...]']
         */
        public function hld_glp_prefunnel_shortcode($atts)
        {
            $atts = shortcode_atts([
                'form_id'     => '',
                'pay'         => '',
                'medications' => '[]' // Default to an empty JSON array
            ], $atts, 'hld_glp_prefunnel');


            $decoded_json = base64_decode($atts['medications']);
            $medications_data = json_decode($decoded_json, true); // object

            wp_localize_script(
                'class-fluent-form-handler',
                'fluentFormData', // JS object name
                [
                    'medications' => $medications_data,
                    'form_id'     => $atts['form_id'] ?? '',
                    'pay'     => $atts['pay'] ?? '',
                ]
            );

            ob_start();
            echo do_shortcode('[hld_navbar]');
?>
            <div class="hld_form_wrap_hidden">
                <?php echo do_shortcode('[fluentform id="' . intval($atts['form_id']) . '"]'); ?>
            </div> <?php echo do_shortcode('[hld_footer]');
                    return ob_get_clean();
                }
            }

            // instantiate on plugins loaded (or just instantiate now)
            new hldShortcode();
        }
