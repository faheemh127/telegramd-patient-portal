<?php
if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class HLD_Admin_Settings
{

    private $options;

    public function __construct()
    {
        // Load saved options
        $this->options = get_option('hld_settings', []);

        // Admin menu
        add_action('admin_menu', [$this, 'hld_add_admin_menu']);

        // Register settings
        add_action('admin_init', [$this, 'hld_register_settings']);

        // Define constants
        add_action('init', [$this, 'hld_define_constants']);
    }

    /**
     * Add admin menu
     */
    public function hld_add_admin_menu()
    {
        add_menu_page(
            'Telegra Settings',   // Page title (top of the settings page)
            'Telegra Settings',   // Menu item name (sidebar)
            'manage_options',
            'hld-settings',
            [$this, 'hld_settings_page'],
            'dashicons-admin-generic',
            80
        );
    }

    /**
     * Register settings
     */
    public function hld_register_settings()
    {
        register_setting('hld_settings_group', 'hld_settings');

        add_settings_section(
            'hld_settings_section',
            'API Configuration',
            '__return_false',
            'hld-settings'
        );

        $fields = [
            'bearer_token'       => 'Telegram Bearer Token',
            'stripe_publishable' => 'Stripe Publishable Key',
            'stripe_secret'      => 'Stripe Secret Key',
            'affiliate_id'       => 'Telegram Affiliate ID',
            'base_url'           => 'Telegram Base URL',
            'google_places'      => 'Google Places API Key',
        ];

        foreach ($fields as $id => $label) {
            add_settings_field(
                "hld_$id",
                $label,
                [$this, 'hld_render_input'],
                'hld-settings',
                'hld_settings_section',
                ['id' => $id]
            );
        }
    }

    /**
     * Render input field
     */
    public function hld_render_input($args)
    {
        $id    = $args['id'];
        $value = isset($this->options[$id]) ? esc_attr($this->options[$id]) : '';
        echo "<input type='text' name='hld_settings[$id]' value='$value' class='regular-text' />";
    }

    /**
     * Settings page
     */
    public function hld_settings_page()
    {
?>
        <div class="wrap">
            <h1>HLD Settings</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('hld_settings_group');
                do_settings_sections('hld-settings');
                submit_button();
                ?>
            </form>
        </div>
<?php
    }

    /**
     * Define constants using saved values
     */
    public function hld_define_constants()
    {
        $map = [
            'TELEGRAMD_BEARER_TOKEN' => 'bearer_token',
            'STRIPE_PUBLISHABLE_KEY' => 'stripe_publishable',
            'STRIPE_SECRET_KEY'      => 'stripe_secret',
            'TELEGRAMD_AFFLIATE_ID'  => 'affiliate_id',
            'TELEGRA_BASE_URL'       => 'base_url',
            'GOOGLE_PLACES_API_KEY'  => 'google_places',
        ];

        foreach ($map as $const => $key) {
            if (! defined($const)) {
                $val = isset($this->options[$key]) ? $this->options[$key] : '';
                define($const, $val);
            }
        }
    }
}

// Initialize
new HLD_Admin_Settings();
