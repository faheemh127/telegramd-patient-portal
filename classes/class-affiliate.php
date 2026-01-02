<?php

class HLD_Affiliate
{
    /**
     * Affiliate token from URL
     */
    private $affiliate_token = null;
    private $affiliate_url_key = "affiliate_id";
    private static $cookie_name = 'hld_affiliate_token';
    private static $cookie_duration_days = 30;

    /**
     * Constructor
     * Runs on every page load
     */

    public function __construct()
    {
        $this->maybe_set_affiliate_cookie();
    }

    public static function send_fluentaffiliate_referral($affiliate_id, $order_id, $provider_id, $amount = 0,)
    {
        // Make sure FluentAffiliate classes exist
        if (!class_exists('\FluentAffiliate\App\Models\Referral')) {
            return;
        }

        if (empty($affiliate_id) || empty($order_id)) {
            return;
        }

        $affiliate_id = intval($affiliate_id);
        // $order_id     = sanitize_text_field($order_id);
        $amount       = floatval($amount);


        if ($affiliate_id > 0) {
            $affiliate = \FluentAffiliate\App\Models\Affiliate::find($affiliate_id);

            if (!$affiliate || $affiliate->status !== 'active') {
                return;
            }

            if ($affiliate) {
                $existing = \FluentAffiliate\App\Models\Referral::where('provider', $order_id)->first();

                if (!$existing) {
                    $rate = floatval($affiliate->rate);
                    $type = $affiliate->rate_type;

                    $commission = 0;
                    //There is third type called group, no idea how is that used to calculated comission,
                    //perhaps provider_id between the group member equally,
                    if ($type === 'percentage') {
                        $commission = ($amount * ($rate / 100));
                    } elseif ($type === 'flat') {
                        $commission = $rate;
                    }

                    \FluentAffiliate\App\Models\Referral::create([
                        'affiliate_id'    => $affiliate_id,
                        'provider'        => $order_id,
                        'provider_sub_id' => "telegra_doctor_prescription_done",
                        'order_total'     => $amount,
                        'amount'          => $commission,
                        'currency'        => 'USD',
                        'status'          => 'unpaid',
                        'type'            => 'sale',
                        'customer_id'     => wp_get_current_user()->ID, //Should we use userId or telegra user Id or stripe user Id
                        'created_at'      => current_time('mysql'),
                        'provider_id'     => $provider_id, //This is the actual order id to show in the order id in referrals edit but it only takes number
                    ]);
                }
            }
        }
    }

    public static function get_affiliate_for_patient()
    {
        // Get logged-in WordPress user
        $user = wp_get_current_user();
        if (!$user || !isset($user->user_email) || empty($user->user_email)) {
            return null;
        }

        $email = sanitize_email($user->user_email);

        global $wpdb;
        $table = HLD_AFFILIATE_TABLE;

        // Calculate cutoff date based on static cookie duration
        $days = self::$cookie_duration_days;
        $cutoff = date('Y-m-d H:i:s', strtotime("-{$days} days"));

        $affiliate_id = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT affiliate_id FROM {$table} WHERE patient_email = %s AND created_at >= %s ORDER BY created_at DESC LIMIT 1",
                $email,
                $cutoff,
            ),
        );

        return $affiliate_id ? $affiliate_id : null;
    }

    /**
     * Create affiliate table if it does not exist
     * Should be called on plugin activation
     */
    public static function create_table()
    {
        global $wpdb;

        // Table name comes from constant (already prefixed)
        $table = HLD_AFFILIATE_TABLE;

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS {$table} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            patient_email VARCHAR(255) NOT NULL,
            affiliate_id VARCHAR(255) NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY patient_email_unique (patient_email)
        ) {$charset_collate};";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }

    /**
     * Save affiliate info when a user signs up
     *
     * @param string $email The user's email
     */
    public static function save_affiliate_on_signup($email)
    {
        if (empty($email)) {
            return;
        }

        // Check if the affiliate cookie is set
        if (empty($_COOKIE[self::$cookie_name])) {
            return;
        }

        global $wpdb;
        $table = HLD_AFFILIATE_TABLE;

        $affiliate_id = sanitize_text_field($_COOKIE[self::$cookie_name]);
        $email = sanitize_email($email);

        // Insert or ignore if email already exists
        $wpdb->insert(
            $table,
            [
                'patient_email' => $email,
                'affiliate_id' => $affiliate_id,
                'created_at' => current_time('mysql'),
            ],
            [
                '%s',
                '%s',
                '%s',
            ],
        );
    }

    /**
     * Check URL for affiliate token and set cookie
     */
    private function maybe_set_affiliate_cookie()
    {
        // Change param name if your affiliate plugin uses something else
        if (!isset($_GET[$this->affiliate_url_key])) {
            return;
        }

        $this->affiliate_token = sanitize_text_field($_GET[$this->affiliate_url_key]);

        if (empty($this->affiliate_token)) {
            return;
        }

        // Set cookie for 7 days
        setcookie(
            self::$cookie_name,
            $this->affiliate_token,
            time() + (7 * DAY_IN_SECONDS),
            COOKIEPATH ?: '/',
            COOKIE_DOMAIN,
            is_ssl(),
            true,
        );

        // Make it available immediately in this request
        $_COOKIE[self::$cookie_name] = $this->affiliate_token;
    }
}


// init the class on each page reload
add_action('init', function () {
    // new HLD_Affiliate();
    HLD_Affiliate::send_fluentaffiliate_referral(1, 'order::pov380',  10, 139);
});
