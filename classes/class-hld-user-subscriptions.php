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


    /**
     * Check if a user already has an active subscription for a given slug
     */
    public static function is_subscription_active($user_id, $subscription_slug)
    {
        if (empty($user_id) || empty($subscription_slug)) {
            return false;
        }

        global $wpdb;
        $table = $wpdb->prefix . self::$table_name;

        $current_time = time();

        // Check if user has an active subscription for this slug
        $result = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM $table
             WHERE user_id = %d
             AND subscription_slug = %s
             AND subscription_status = 'active'
             AND (subscription_end = 0 OR subscription_end > %d)",
                $user_id,
                $subscription_slug,
                $current_time
            )
        );

        return $result > 0;
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
        subscription_slug VARCHAR(100) NOT NULL,
        refund_status ENUM('created', 'requested', 'refunded') DEFAULT 'created',
        PRIMARY KEY (id),
        UNIQUE KEY user_order_unique (telegra_order_id)
    ) $charset_collate;";
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }


    /**
     * Insert Stripe subscription data into custom table
     */
    public static function add_subscription($user_id, $patient_email, $subscription_duration, $medication_telegra_id, $medication_name, $stripeData, $subscription_slug)
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
        // ✅ Check if user already has an active subscription for this plan
        if (self::is_subscription_active($user_id, $subscription_slug)) {
            error_log("Current user has already subscribed this plan " . $subscription_slug);
            return [
                'status'  => false,
                'message' => 'You already have an active Weight Loss plan. Please wait until your current subscription ends or contact support for adjustments.'
            ];
        }



        $invoice = $stripeData->latest_invoice ?? null;
        $result = $wpdb->insert(
            $table,
            [
                'user_id'                   => $user_id,
                'telegra_order_id'          => 'pending_' . wp_generate_uuid4(),
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
                'subscription_slug' => $subscription_slug,
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

        if ($result) {
            return [
                'status'  => true,
                'message' => 'Subscription has been purchased successfully'
            ];
        } else {
            // Log and return DB error
            error_log("Database insert failed: " . $wpdb->last_error);
            return [
                'status'  => false,
                'message' => 'Database error: ' . $wpdb->last_error
            ];
        }
    }




    /**
     * Add a new order to the custom table
     */
    public static function add_order($telegra_order_id)
    {
        if (empty($telegra_order_id)) {
            return false;
        }

        // ✅ Get logged-in user ID
        $user_id = get_current_user_id();

        if (empty($user_id)) {
            error_log("TelegraMD Error: No logged-in user found when adding order.");
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
                'user_id'          => $user_id,
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




        if (! hld_table_exists($table)) {
            error_log("Healsend Error: Table does not exist: {$table}");
            return false;
        }


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




    public static function update_order($telegra_order_id, $stripe_subscription_id)
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

        // Update telegra_order_id for this patient'
        
        $result = $wpdb->update(
            $table,
            [
                'telegra_order_id' => sanitize_text_field($telegra_order_id),
            ],
            [
                'stripe_subscription_id' => $stripe_subscription_id, // condition
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
    public static function get_orders()
    {
        // Ensure user is logged in
        if (!is_user_logged_in()) {
            error_log("TelegraMD Error: get_orders() called but no user is logged in.");
            return [];
        }

        // Get logged-in user's email
        $current_user = wp_get_current_user();

        if (empty($current_user->user_email)) {
            error_log("TelegraMD Error: Logged-in user has no email address.");
            return [];
        }

        $patient_email = $current_user->user_email;

        global $wpdb;
        $table = $wpdb->prefix . self::$table_name;

        // Check if table exists
        $table_exists = $wpdb->get_var(
            $wpdb->prepare("SHOW TABLES LIKE %s", $table)
        );

        if ($table_exists !== $table) {
            error_log("TelegraMD Error: Table '$table' does not exist.");
            return [];
        }

        // Fetch all telegra orders for this patient
        $results = $wpdb->get_col(
            $wpdb->prepare(
                "SELECT telegra_order_id 
             FROM $table 
             WHERE patient_email = %s",
                $patient_email
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
