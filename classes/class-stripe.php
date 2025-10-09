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



// // 1. Create product
// $product = HLD_Stripe::create_product('Premium Plan', 'Access to all premium features');
// if ($product) {
//     echo 'Product ID: ' . $product->id;
// }

// // 2. Create price
// $price = HLD_Stripe::create_price($product->id, 29.99, 'usd', 'month');
// if ($price) {
//     echo 'Price ID: ' . $price->id;
// }

// // 3. Get existing product
// $product_data = HLD_Stripe::get_product($product->id);

// // 4. Update product
// HLD_Stripe::update_product($product->id, ['description' => 'Updated premium plan details']);

// // 5. Delete product
// HLD_Stripe::delete_product($product->id);



add_action('init', function () {

    // return;
    // Prevent running this code on every page load — only for testing or setup
    // if (!is_admin() || !current_user_can('manage_options')) {
    //     return;
    // }

    error_log(("funtion init called 101"));



    // // Include Stripe setup (make sure your class and constants are defined)
    // require_once HLD_PLUGIN_PATH . 'vendor/autoload.php';
    // \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

    // // // 1. Create product
    // // $product = HLD_Stripe::create_product('Premium Plan', 'Access to all premium features');
    // $product = HLD_Stripe::create_product(
    //     'Tirzepatide',
    //     'Most effective, Higher % weight loss, Higher cost ,  Injection • Drops • Tablets',
    //     [
    //         'telegra_product_id' => "pvt::b04cabe5-2acc-4b8c-aacd-eea3a48b65bb",                // custom key
    //         'period' => 3 // another custom key
    //     ]
    // );
    // if ($product) {
    //     echo 'Product ID: ' . esc_html($product->id) . '<br>';
    // }

    // // 2. Create price
    // if ($product && isset($product->id)) {
    //     $price = HLD_Stripe::create_price($product->id, 29.99, 'usd', 'month');
    //     if ($price) {
    //         echo 'Price ID: ' . esc_html($price->id);
    //     }
    // }



    // $products = \Stripe\Product::search([
    //     'query' => 'metadata["telegra_product_id"]:"pvt::b04cabe5-2acc-4b8c-aacd-eea3a48b65bb" AND metadata["period"]:"1"',
    // ]);


    // error_log(print_r($products, true));

    // $products = HLD_Stripe::get_all_products();

    // foreach ($products as $p) {
    //     $data =  $p->id . ' - ' . $p->name . '<br>';
    //     error_log(print_r($p, true));
    // }
});
