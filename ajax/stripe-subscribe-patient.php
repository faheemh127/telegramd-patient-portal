<?php
// Subscribe Patient (auto-cancel after X months)
add_action('wp_ajax_subscribe_patient', 'hld_subscribe_patient_handler');
add_action('wp_ajax_nopriv_subscribe_patient', 'hld_subscribe_patient_handler');

function hld_subscribe_patient_handler()
{


    // @todo check nonce here

    if (!isset($_POST['payment_method']) || !isset($_POST['price_id']) || !isset($_POST['duration']) || !isset($_POST['promo'])) {
        wp_send_json_error(['message' => 'Missing parameters']);
        wp_die();
    }


    /**
     * check that if patient has already purchased the plan or not
     * one patient should not be able to purchase the same plan again
     */

    $slug  = sanitize_text_field($_POST['slug']);
    if (empty($_POST['slug'])) { // covers both not set and empty
        wp_send_json_error([
            'message' => 'Plan slug is required.'
        ]);
        wp_die();
    }
    $plan_exists = HLD_UserSubscriptions::is_subscription_active($slug);


    if ($plan_exists) {
        wp_send_json_error([
            'message' => 'It looks like you are already subscribed to this plan. Please check your active subscriptions.'
        ]);
        wp_die();
    }




    require_once HLD_PLUGIN_PATH . 'vendor/autoload.php';
    \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

    $payment_method     = sanitize_text_field($_POST['payment_method']);
    $price_id           = sanitize_text_field($_POST['price_id']);
    $duration           = (int) sanitize_text_field($_POST['duration']);
    $months             = max(1, $duration);
    $medication         = sanitize_text_field($_POST['medication']);
    $telegra_product_id = sanitize_text_field($_POST['telegra_product_id']);
    $promo              = sanitize_text_field($_POST['promo']);


    try {
        /**
         * STEP 1: Get or Create Stripe Customer
         */
        if (is_user_logged_in()) {
            $user_id   = get_current_user_id();
            $user_info = get_userdata($user_id);
            $patient_email = $user_info->user_email;
            $first_name    = $user_info->first_name ?? '';
            $last_name     = $user_info->last_name ?? '';

            // Automatically fetch or create Stripe customer
            $customer_id = HLD_Stripe::get_or_create_stripe_customer($patient_email, $first_name, $last_name);
        } else {
            // Fallback if not logged in (or from frontend)
            if (!empty($_POST['customer_id'])) {
                $customer_id = sanitize_text_field($_POST['customer_id']);
            } else {
                wp_send_json_error(['message' => 'Customer not logged in or customer_id missing.']);
                wp_die();
            }
        }

        /**
         * STEP 2: Attach payment method to customer
         */
        $pm = \Stripe\PaymentMethod::retrieve($payment_method);

        // Check if already attached
        if (empty($pm->customer)) {
            // Not attached — safe to attach
            $pm->attach(['customer' => $customer_id]);
        } else {
            // Already attached to some customer
            // Optional: check if it's attached to the same one
            if ($pm->customer !== $customer_id) {
                wp_send_json_error([
                    'message' => 'This payment method is already attached to another customer. Please use a new card.'
                ]);
                wp_die();
            }
        }

        // Always set as default
        \Stripe\Customer::update($customer_id, [
            'invoice_settings' => ['default_payment_method' => $payment_method]
        ]);






        /**
         * STEP 3: Create subscription that cancels automatically after N months
         */
        // $subscription = \Stripe\Subscription::create([
        //     'customer' => $customer_id,
        //     'items' => [['price' => $price_id]],
        //     'discounts' => [[
        //         'promotion_code' => 'promo_1ScNtmAcgi1hKyLWaHc5MWbi' // e.g. "25OFF_FIRST_MONTH"
        //     ]],
        //     'cancel_at' => strtotime("+{$months} months"),
        //     'expand' => ['latest_invoice.payment_intent'],
        // ]);



        // Base subscription data
        $subscription_data = [
            'customer' => $customer_id,
            'items' => [
                ['price' => $price_id]
            ],
            'cancel_at' => strtotime("+{$months} months"),
            'expand' => ['latest_invoice.payment_intent'],
        ];

        // If patient is new → add discount
        if (HLD_Patient::is_patient_new($patient_email) && !empty($promo)) {
            $subscription_data['discounts'] = [[
                'promotion_code' => $promo
            ]];
        }

        // Create subscription
        $subscription = \Stripe\Subscription::create($subscription_data);

        error_log("a subscription was trying to be created with card");
        error_log(print_r($subscription, true));



        /**
         * STEP 4: Store locally in custom tables
         */
        if (is_user_logged_in()) {
            // Make sure the patient exists
            HLD_Patient::ensure_patient_by_email($patient_email);

            // Extract card details
            $card_last4 = $pm->card->last4 ?? null;
            $card_brand = $pm->card->brand ?? null;

            // Save into payments table
            HLD_Payments::add_payment_method(
                $patient_email,
                $payment_method,
                $card_last4,
                $card_brand
            );

            // Optional custom actions
            // HLD_Telegra::create_patient();

            $response = HLD_UserSubscriptions::add_subscription(
                $user_id,
                $patient_email,
                $months,
                $telegra_product_id, // Example: pov::.....
                $medication, // Example: Tirzepatide
                $subscription,
                $slug
            );


            // If the current patient already have the subscription with the same plan don't allow him to purchase that subscription again
            if (!$response['status']) {
                wp_send_json_error([
                    'message' => $response['message'],
                ]);
            }
        }

        /**
         * STEP 5: Return success response
         */
        wp_send_json_success([
            'subscription_id' => $subscription->id,
            'status' => $subscription->status,
            'customer_id' => $customer_id,
        ]);
    } catch (Exception $e) {
        wp_send_json_error(['message' => $e->getMessage()]);
    }

    wp_die();
}
