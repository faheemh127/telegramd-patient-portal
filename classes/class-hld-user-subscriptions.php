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
        add_action('init', [__CLASS__, 'create_table_if_not_exists']);
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

        PRIMARY KEY (id),
        UNIQUE KEY user_order_unique (user_id, telegra_order_id)
    ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }


    /**
     * Add a new order to the custom table
     */
    public static function add_order($user_id, $telegra_order_id)
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

    /**
     * Get all orders for a given user
     */
    public static function get_orders($user_id)
    {
        if (empty($user_id)) return [];

        global $wpdb;
        $table = $wpdb->prefix . self::$table_name;

        $results = $wpdb->get_col(
            $wpdb->prepare("SELECT telegra_order_id FROM $table WHERE user_id = %d", $user_id)
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
/**
 * ============================
 *   Usage Guide: HLD_UserSubscriptions
 * ============================
 *
 * Example 1: Add an order for a user
 * ----------------------------------
 * HLD_UserSubscriptions::add_order(13, 'ORD-12345');
 *
 *
 * Example 2: Retrieve all orders for a user
 * -----------------------------------------
 * $orders = HLD_UserSubscriptions::get_orders(13);
 * // Returns: ['ORD-12345', 'ORD-12346', ...]
 *
 *
 * Example 3: Check if a specific order exists for a user
 * ------------------------------------------------------
 * $hasOrder = HLD_UserSubscriptions::has_order(13, 'ORD-12345');
 *
 *
 * Example 4: Working with the logged-in user
 * ------------------------------------------
 * if ( is_user_logged_in() ) {
 *     $user_id = get_current_user_id();
 *     $orders  = HLD_UserSubscriptions::get_orders($user_id);
 *     print_r($orders);
 *     echo 'User ID: ' . $user_id;
 * } else {
 *     echo 'User is not logged in.';
 * }
 */
