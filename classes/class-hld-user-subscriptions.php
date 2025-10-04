<?php
class HLD_UserSubscriptions
{
    /**
     * Table name
     */
    private static $table_name = 'healsend_subscriptions';

    /**
     * Hook to create table if not exists
     */
    public static function init()
    {
        // add_action('init', [__CLASS__, 'create_table_if_not_exists']);
    }


    /**
     * Runs only once on plugin activation
     */
    public static function on_plugin_activate()
    {
        self::create_table_if_not_exists();
    }


    /**
     * Create table if it doesn't exist
     */
    public static function create_table_if_not_exists()
    {
        global $wpdb;
        $table = $wpdb->prefix . self::$table_name;

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS $table (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        user_id BIGINT(20) NOT NULL,
        telegra_order_id VARCHAR(255) NOT NULL,
        patient_email VARCHAR(255) NOT NULL,
        subscription_duration VARCHAR(100) NOT NULL,
        medication_telegra_id VARCHAR(255) NOT NULL,
        medication_name VARCHAR(255) NOT NULL,
        stripe_product_id VARCHAR(255) NOT NULL,
        subscription_monthly_amount DECIMAL(10,2) NOT NULL,

        stripe_subscription_id VARCHAR(255) NOT NULL,
        stripe_customer_id VARCHAR(255) NOT NULL,
        stripe_invoice_id VARCHAR(255) NULL,
        subscription_status VARCHAR(50) NOT NULL,
        subscription_start BIGINT(20) NOT NULL,
        subscription_end BIGINT(20) NULL,
        cancel_at_period_end TINYINT(1) DEFAULT 0,
        invoice_pdf_url TEXT NULL,
        hosted_invoice_url TEXT NULL,

        PRIMARY KEY (id),
        UNIQUE KEY user_order_unique (user_id, telegra_order_id)
    ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }



    /**
     * Insert Stripe subscription data into custom table
     */
    public static function add_subscription($user_id, $patient_email, $subscription_duration, $medication_telegra_id, $medication_name, $stripeData)
    {
        error_log("function add_subscription is called");
        error_log("user_id" . $user_id);


        if (empty($user_id) || empty($stripeData)) {
            return false;
        }
        error_log("Nothing is empty and function called");

        global $wpdb;
        $table = $wpdb->prefix . self::$table_name;

        // Prevent duplicates
        // if (self::has_order($user_id, $telegra_order_id)) {
        //     return true;
        // }

        $invoice = $stripeData->latest_invoice ?? null;
        $result = $wpdb->insert(
            $table,
            [
                'user_id'                   => $user_id,
                'telegra_order_id'          => "pending",
                'patient_email'             => sanitize_email($patient_email),
                'subscription_duration'     => sanitize_text_field($subscription_duration),
                'medication_telegra_id'     => sanitize_text_field($medication_telegra_id),
                'medication_name'           => sanitize_text_field($medication_name),
                'stripe_product_id'         => sanitize_text_field($stripeData->plan->product ?? ''),
                'subscription_monthly_amount' => ($stripeData->plan->amount ?? 0) / 100, // convert cents to dollars
                'stripe_subscription_id'    => sanitize_text_field($stripeData->id),
                'stripe_customer_id'        => sanitize_text_field($stripeData->customer),
                'stripe_invoice_id'         => $invoice->id ?? null,
                'subscription_status'       => sanitize_text_field($stripeData->status),
                'subscription_start'        => intval($stripeData->start_date),
                'subscription_end'          => intval($stripeData->cancel_at ?? 0),
                'cancel_at_period_end'      => $stripeData->cancel_at_period_end ? 1 : 0,
                'invoice_pdf_url'           => $invoice->invoice_pdf ?? null,
                'hosted_invoice_url'        => $invoice->hosted_invoice_url ?? null,
            ],
            [
                '%d',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%f',
                '%s',
                '%s',
                '%s',
                '%s',
                '%d',
                '%d',
                '%d',
                '%s',
                '%s'
            ]
        );

        return $result !== false;
    }




    /**
     * Add a new order to the custom table
     */
    public static function add_order($telegra_order_id)
    {
        if (empty($user_id) || empty($telegra_order_id)) {
            return false;
        }

        global $wpdb;
        $table = $wpdb->prefix . self::$table_name;

        // Prevent duplicates
        if (self::has_order($user_id, $telegra_order_id)) {
            return true;
        }

        $result = $wpdb->insert(
            $table,
            [
                'user_id'  => $user_id,
                'telegra_order_id' => sanitize_text_field($telegra_order_id),
            ],
            ['%d', '%s']
        );

        return $result !== false;
    }


    public static function get_user_subscription($user_id)
    {
        if (empty($user_id)) return null;

        global $wpdb;
        $table = $wpdb->prefix . self::$table_name;

        // Fetch latest active subscription for user
        $result = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * 
             FROM $table 
             WHERE user_id = %d 
             ORDER BY subscription_start DESC 
             LIMIT 1",
                $user_id
            ),
            ARRAY_A
        );

        return $result ?: null;
    }




    public static function update_order($telegra_order_id)
    {
        // Validate input
        if (empty($telegra_order_id)) {
            return false;
        }

        // Get current user
        $current_user = wp_get_current_user();
        if (!$current_user || empty($current_user->user_email)) {
            return false;
        }

        $patient_email = $current_user->user_email;

        global $wpdb;
        $table = $wpdb->prefix . self::$table_name;

        // Update telegra_order_id for this patient
        $result = $wpdb->update(
            $table,
            [
                'telegra_order_id' => sanitize_text_field($telegra_order_id),
            ],
            [
                'patient_email' => $patient_email, // condition
            ],
            ['%s'],
            ['%s']
        );

        // If no row was updated (patient not found), return false
        return $result !== false && $result > 0;
    }







    /**
     * Get all orders for a given user
     */
    public static function get_orders($user_id)
    {
        if (empty($user_id)) return [];

        global $wpdb;
        $table = $wpdb->prefix . self::$table_name;

        $results = $wpdb->get_col(
            $wpdb->prepare(
                "SELECT telegra_order_id 
             FROM $table 
             WHERE user_id = %d 
               AND telegra_order_id LIKE %s",
                $user_id,
                $wpdb->esc_like('order::') . '%'
            )
        );

        return $results ?: [];
    }


    /**
     * Check if a user already has a specific order
     */
    public static function has_order($user_id, $telegra_order_id)
    {
        global $wpdb;
        $table = $wpdb->prefix . self::$table_name;

        $exists = $wpdb->get_var(
            $wpdb->prepare("SELECT COUNT(*) FROM $table WHERE user_id = %d AND telegra_order_id = %s", $user_id, $telegra_order_id)
        );

        return $exists > 0;
    }
}
HLD_UserSubscriptions::init();
register_activation_hook(__FILE__, ['HLD_UserSubscriptions', 'on_plugin_activate']);
