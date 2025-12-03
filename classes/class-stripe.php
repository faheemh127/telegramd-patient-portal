<?php
if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

use Stripe\Stripe;
use Stripe\Product;
use Stripe\Price;
use Stripe\Exception\ApiErrorException;

class HLD_Stripe
{

    /**
     * Initialize Stripe
     */
    private static function init()
    {
        \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);
    }

    /**
     * Create a Stripe product
     *
     * @param string $name
     * @param string $description
     * @param array $metadata
     * @return \Stripe\Product|null
     */
    public static function create_product($name, $description = '', $metadata = [])
    {
        self::init();

        try {
            $product = Product::create([
                'name'        => $name,
                'description' => $description,
                'metadata'    => $metadata,
            ]);
            return $product;
        } catch (ApiErrorException $e) {
            error_log('Stripe Product Create Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Retrieve a Stripe product by ID
     *
     * @param string $product_id
     * @return \Stripe\Product|null
     */
    public static function get_product($product_id)
    {
        self::init();

        try {
            return Product::retrieve($product_id);
        } catch (ApiErrorException $e) {
            error_log('Stripe Get Product Error: ' . $e->getMessage());
            return null;
        }
    }




    /**
     * Verify Stripe Payment if AfterPay Redirect
     */
    public static function verify_payment_on_redirect()
    {
        // Check required parameters
        if (empty($_GET['payment_intent']) || empty($_GET['payment_intent_client_secret'])) {
            return; // nothing to verify
        }

        $payment_intent_id  = sanitize_text_field($_GET['payment_intent']);
        $client_secret      = sanitize_text_field($_GET['payment_intent_client_secret']);

        self::init();

        try {
            // Retrieve the Payment Intent from Stripe
            $pi = \Stripe\PaymentIntent::retrieve($payment_intent_id);

            // Check if succeeded
            if ($pi && isset($pi->status) && $pi->status === 'succeeded') {
                error_log("🔥 Stripe Payment SUCCESSFUL");
                error_log("PI ID: " . $payment_intent_id);
                error_log("Amount Received: " . $pi->amount_received);
                error_log("Currency: " . $pi->currency);
                error_log("Payment Method Type: " . json_encode($pi->payment_method_types));
                error_log("Full PaymentIntent: " . print_r($pi, true));




                $user_id = get_current_user_id();
                $patient_email = '';

                if ($user_id) {
                    $user = wp_get_current_user();
                    $patient_email = $user->user_email;

                    $response = HLD_UserSubscriptions::add_subscription(
                        $user_id,
                        $patient_email,
                        0,
                        "", // Example: pov::.....
                        "", // Example: Tirzepatide
                        $subscription,
                        $slug, // metabolic, glp_1_prefunnel
                        $pi->payment_method_types,
                    );
                }
            } else {
                error_log("⚠ Stripe Payment FAILED or NOT completed");
                error_log("PI Status: " . ($pi->status ?? 'unknown'));
                error_log("Full PI Response: " . print_r($pi, true));
            }
        } catch (\Exception $e) {
            error_log("❌ Stripe Payment Verification ERROR: " . $e->getMessage());
        }
    }






    /**
     * Update a Stripe product
     *
     * @param string $product_id
     * @param array $updates
     * @return \Stripe\Product|null
     */
    public static function update_product($product_id, $updates = [])
    {
        self::init();

        try {
            return Product::update($product_id, $updates);
        } catch (ApiErrorException $e) {
            error_log('Stripe Update Product Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Delete a Stripe product
     *
     * @param string $product_id
     * @return bool
     */
    public static function delete_product($product_id)
    {
        self::init();

        try {
            $product = Product::retrieve($product_id);
            $product->delete();
            return true;
        } catch (ApiErrorException $e) {
            error_log('Stripe Delete Product Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Create a price for a product
     *
     * @param string $product_id
     * @param float $amount
     * @param string $currency
     * @param string $interval (e.g., 'month', 'year')
     * @return \Stripe\Price|null
     */
    public static function create_price($product_id, $amount, $currency = 'usd', $interval = 'month')
    {
        self::init();

        try {
            $price = Price::create([
                'unit_amount' => intval($amount * 100), // convert to cents
                'currency'    => $currency,
                'recurring'   => ['interval' => $interval],
                'product'     => $product_id,
            ]);
            return $price;
        } catch (ApiErrorException $e) {
            error_log('Stripe Price Create Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get all prices for a product
     *
     * @param string $product_id
     * @return array|null
     */
    public static function get_prices($product_id)
    {
        self::init();

        try {
            $prices = Price::all(['product' => $product_id]);
            return $prices->data ?? [];
        } catch (ApiErrorException $e) {
            error_log('Stripe Get Prices Error: ' . $e->getMessage());
            return null;
        }
    }

    public static function get_all_products($limit = 100)
    {
        self::init();


        try {
            $products = \Stripe\Product::all(['limit' => $limit]);
            return $products->data ?? [];
        } catch (\Exception $e) {
            error_log('Stripe Product Fetch Error: ' . $e->getMessage());
            return [];
        }
    }






    /**
     * Get or create Stripe Customer ID for a patient
     *
     * @param string $email
     * @param string $first_name
     * @param string $last_name
     * @return string|null Stripe Customer ID or null on failure
     */
    // public static function get_or_create_stripe_customer($email, $first_name = '', $last_name = '')
    // {
    //     global $wpdb;

    //     self::init();

    //     $table = HEALSEND_PATIENTS_TABLE;

    //     // 1. Ensure stripe_customer_id column exists
    //     $column_exists = $wpdb->get_results(
    //         $wpdb->prepare(
    //             "SHOW COLUMNS FROM $table LIKE %s",
    //             'stripe_customer_id'
    //         )
    //     );

    //     if (empty($column_exists)) {
    //         $wpdb->query("ALTER TABLE $table ADD COLUMN stripe_customer_id VARCHAR(255) NULL AFTER telegra_patient_id");
    //     }

    //     // 2. Check if patient already exists
    //     $patient = $wpdb->get_row(
    //         $wpdb->prepare("SELECT id, stripe_customer_id FROM $table WHERE patient_email = %s", $email)
    //     );

    //     // 3. If patient exists and has Stripe ID → return it
    //     if ($patient && !empty($patient->stripe_customer_id)) {
    //         return $patient->stripe_customer_id;
    //     }

    //     // 4. Create Stripe Customer
    //     try {
    //         $customer = \Stripe\Customer::create([
    //             'email' => $email,
    //             'name'  => trim("$first_name $last_name"),
    //             'metadata' => [
    //                 'source' => 'Healsend',
    //             ],
    //         ]);
    //     } catch (\Stripe\Exception\ApiErrorException $e) {
    //         error_log('Stripe Create Customer Error: ' . $e->getMessage());
    //         return null;
    //     }

    //     if (empty($customer->id)) {
    //         return null;
    //     }

    //     // 5. If patient exists, update record — otherwise insert new
    //     if ($patient) {
    //         $wpdb->update(
    //             $table,
    //             ['stripe_customer_id' => $customer->id],
    //             ['id' => $patient->id],
    //             ['%s'],
    //             ['%d']
    //         );
    //     } else {
    //         $wpdb->insert(
    //             $table,
    //             [
    //                 'patient_uuid'       => wp_generate_uuid4(),
    //                 'first_name'         => $first_name,
    //                 'last_name'          => $last_name,
    //                 'patient_email'      => $email,
    //                 'stripe_customer_id' => $customer->id,
    //                 'created_at'         => current_time('mysql'),
    //                 'updated_at'         => current_time('mysql'),
    //             ],
    //             ['%s', '%s', '%s', '%s', '%s', '%s', '%s']
    //         );
    //     }

    //     return $customer->id;
    // }



    public static function get_or_create_stripe_customer($email, $first_name = '', $last_name = '')
    {
        global $wpdb;

        self::init();

        $table = HEALSEND_PATIENTS_TABLE;

        // 1. Ensure stripe_customer_id column exists
        $column_exists = $wpdb->get_results(
            $wpdb->prepare("SHOW COLUMNS FROM $table LIKE %s", 'stripe_customer_id')
        );

        if (empty($column_exists)) {
            $wpdb->query("ALTER TABLE $table ADD COLUMN stripe_customer_id VARCHAR(255) NULL AFTER telegra_patient_id");
        }

        // 2. Try to find patient in DB
        $patient = $wpdb->get_row(
            $wpdb->prepare("SELECT id, stripe_customer_id FROM $table WHERE patient_email = %s", $email)
        );

        // 3. If Stripe ID already exists → return it
        if ($patient && !empty($patient->stripe_customer_id)) {
            return $patient->stripe_customer_id;
        }

        try {
            // ✅ 4. Try to find existing customer in Stripe by email
            $stripe = new \Stripe\StripeClient(STRIPE_SECRET_KEY);

            $existing = $stripe->customers->search([
                'query' => 'email:"' . $email . '"',
            ]);

            if (!empty($existing->data)) {
                $customer = $existing->data[0]; // found existing customer
            } else {
                // ❌ Not found — create a new customer
                $customer = $stripe->customers->create([
                    'email' => $email,
                    'name'  => trim("$first_name $last_name"),
                    'metadata' => [
                        'source' => 'Healsend',
                    ],
                ]);
            }

            // Safety check
            if (empty($customer->id)) {
                return null;
            }

            // ✅ 5. Store (or update) in database
            if ($patient) {
                $wpdb->update(
                    $table,
                    ['stripe_customer_id' => $customer->id],
                    ['id' => $patient->id],
                    ['%s'],
                    ['%d']
                );
            } else {
                $wpdb->insert(
                    $table,
                    [
                        'patient_uuid'       => wp_generate_uuid4(),
                        'first_name'         => $first_name,
                        'last_name'          => $last_name,
                        'patient_email'      => $email,
                        'stripe_customer_id' => $customer->id,
                        'created_at'         => current_time('mysql'),
                        'updated_at'         => current_time('mysql'),
                    ],
                    ['%s', '%s', '%s', '%s', '%s', '%s', '%s']
                );
            }

            return $customer->id;
        } catch (\Stripe\Exception\ApiErrorException $e) {
            error_log('Stripe get_or_create_stripe_customer Error: ' . $e->getMessage());
            return null;
        }
    }











    /**
     * Delete a price (Note: Stripe doesn’t fully delete prices; it deactivates them)
     *
     * @param string $price_id
     * @return \Stripe\Price|null
     */
    public static function deactivate_price($price_id)
    {
        self::init();

        try {
            return Price::update($price_id, ['active' => false]);
        } catch (ApiErrorException $e) {
            error_log('Stripe Deactivate Price Error: ' . $e->getMessage());
            return null;
        }
    }
}


add_action('init', ['HLD_Stripe', 'verify_payment_on_redirect']);
