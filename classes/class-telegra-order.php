<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class HLD_TelegraOrder {

    /**
     * The full order data returned from Telegra API.
     *
     * @var array|object
     */
    private $order_data = [];

    /**
     * Constructor accepts the Telegra API response.
     *
     * @param array|object $data Full Telegra order response.
     */
    public function __construct( $data ) {
        // Convert to array if it's an object (JSON-decoded response)
        $this->order_data = is_object( $data ) ? json_decode( json_encode( $data ), true ) : $data;
    }

    /**
     * Example method — get patient email.
     *
     * @return string|null
     */
    public function get_patient_email() {
        return $this->order_data['patient']['email'] ?? null;
    }

    /**
     * Example method — get order ID.
     *
     * @return string|null
     */
    public function get_order_id() {
        return $this->order_data['id'] ?? null;
    }

    /**
     * Example method — get order number.
     *
     * @return string|null
     */
    public function get_order_number() {
        return $this->order_data['orderNumber'] ?? null;
    }

    /**
     * Example method — get patient full name.
     *
     * @return string|null
     */
    public function get_patient_name() {
        return $this->order_data['patient']['name'] ?? null;
    }

    /**
     * Example method — return raw order data.
     *
     * @return array
     */
    public function get_raw_data() {
        return $this->order_data;
    }
}


// $order = new HLD_TelegraOrder($response);

// // Now you can call instance methods
// echo $order->get_patient_email();  // → test33@gmail.com
// echo $order->get_order_number();   // → 378352
// echo $order->get_patient_name();   // → Test 33 Doe