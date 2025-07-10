<?php
class HLD_UserOrders
{

    /**
     * Meta key used to store orders  
     */
    private static $meta_key = 'hld_orders';

    /**
     * Add an order ID to the user's order list
     *
     * @param int $user_id
     * @param int|string $order_id
     * @return bool
     */
    public static function add_order($user_id, $order_id)
    {
        if (!is_user_logged_in() || empty($user_id) || empty($order_id)) {
            return false;
        }

        $orders = get_user_meta($user_id, self::$meta_key, true);

        if (!is_array($orders)) {
            $orders = [];
        }

        if (!in_array($order_id, $orders)) {
            $orders[] = $order_id;
            update_user_meta($user_id, self::$meta_key, $orders);
        }

        return true;
    }

    /**
     * Get all order IDs for a user
     *
     * @param int $user_id
     * @return array
     */
    public static function get_orders($user_id)
    {
        $orders = get_user_meta($user_id, self::$meta_key, true);
        return is_array($orders) ? $orders : [];
    }

    /**
     * Check if a specific order ID exists for a user
     *
     * @param int $user_id
     * @param int|string $order_id
     * @return bool
     */
    public static function has_order($user_id, $order_id)
    {
        $orders = self::get_orders($user_id);
        return in_array($order_id, $orders);
    }
}
