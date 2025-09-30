<?php
class HLD_Payments
{
    /**
     * Table name
     */
    private static $table_name = 'healsend_payments';

    /**
     * Add a payment method for a patient
     */
    public static function add_payment_method($patient_email, $payment_token, $card_last4 = null, $card_brand = null)
    {
        if (empty($patient_email) || empty($payment_token)) {
            return false;
        }

        global $wpdb;
        $table = $wpdb->prefix . self::$table_name;

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

        error_log("payment method saved in database 15" . $result);
        return $result !== false;
    }

    /**
     * Get all payments for a patient
     */
    public static function get_payments_by_patient($patient_id)
    {
        global $wpdb;
        $table = $wpdb->prefix . self::$table_name;

        return $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM $table WHERE patient_id = %d ORDER BY created_at DESC", $patient_id),
            ARRAY_A
        );
    }

    /**
     * Check if a payment method exists for a token
     */
    public static function has_payment_method($payment_token)
    {
        global $wpdb;
        $table = $wpdb->prefix . self::$table_name;

        $exists = $wpdb->get_var(
            $wpdb->prepare("SELECT COUNT(*) FROM $table WHERE payment_token = %s", $payment_token)
        );

        return $exists > 0;
    }

    /**
     * Delete a payment method by ID
     */
    public static function delete_payment_method($id)
    {
        global $wpdb;
        $table = $wpdb->prefix . self::$table_name;

        return $wpdb->delete(
            $table,
            ['id' => (int) $id],
            ['%d']
        ) !== false;
    }

    /**
     * Get all payments
     */
    public static function get_all_payments()
    {
        global $wpdb;
        $table = $wpdb->prefix . self::$table_name;

        return $wpdb->get_results("SELECT * FROM $table ORDER BY created_at DESC", ARRAY_A);
    }
}


// Usage
// 1. Add a payment method
// HLD_Payments::add_payment_method(
//     15,
//     'john@example.com',
//     'tok_123abc456def',
//     '4242',
//     'Visa'
// );

// // 2. Get all payments for a patient
// $payments = HLD_Payments::get_payments_by_patient(15);
// print_r($payments);

// // 3. Check if a payment method exists
// if (HLD_Payments::has_payment_method('tok_123abc456def')) {
//     echo "Payment method exists.";
// }

// // 4. Delete a payment method
// HLD_Payments::delete_payment_method(3);

// // 5. Get all payments
// $allPayments = HLD_Payments::get_all_payments();
// print_r($allPayments);