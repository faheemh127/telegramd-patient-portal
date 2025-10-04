<?php
class HLD_Payments
{
    /**
     * Table name constant
     */
    private static function get_table_name()
    {
        return HEALSEND_PAYMENTS_TABLE;
    }

    /**
     * Add a payment method for a patient
     */
    public static function add_payment_method($patient_email, $payment_token, $card_last4 = null, $card_brand = null)
    {
        if (empty($patient_email) || empty($payment_token)) {
            return false;
        }

        global $wpdb;
        $table = self::get_table_name();

        $result = $wpdb->insert(
            $table,
            [
                'patient_email' => sanitize_email($patient_email),
                'payment_token' => sanitize_text_field($payment_token),
                'card_last4'    => sanitize_text_field($card_last4),
                'card_brand'    => sanitize_text_field($card_brand),
                'created_at'    => current_time('mysql'),
            ],
            ['%s', '%s', '%s', '%s', '%s']
        );

        error_log("payment method saved in database 15 -> " . ($result ? 'success' : 'failed'));
        return $result !== false;
    }





    /* -------------------------
     * New Methods for logged-in user
     * ------------------------- */

    /**
     * Get the logged-in patient's email
     */
    private static function get_logged_in_email()
    {
        $user = wp_get_current_user();
        return ($user && ! empty($user->user_email)) ? sanitize_email($user->user_email) : null;
    }

    /**
     * Get last payment row for logged-in user
     */
    public static function get_last_payment()
    {
        global $wpdb;
        $table = self::get_table_name();
        $email = self::get_logged_in_email();

        if (! $email) {
            return null;
        }

        return $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$table} WHERE patient_email = %s ORDER BY created_at DESC LIMIT 1",
                $email
            ),
            ARRAY_A
        );
    }

    /**
     * Get last 4 digits for logged-in user
     */
    public static function get_last4()
    {
        $payment = self::get_last_payment();
        return $payment && ! empty($payment['card_last4']) ? $payment['card_last4'] : null;
    }

    /**
     * Get card brand for logged-in user
     */
    public static function get_card_brand()
    {
        $payment = self::get_last_payment();
        return $payment && ! empty($payment['card_brand']) ? $payment['card_brand'] : null;
    }

    /**
     * Check if logged-in user has a saved card (last4 exists)
     */
    public static function has_card()
    {
        return ! empty(self::get_last4());
    }
}
