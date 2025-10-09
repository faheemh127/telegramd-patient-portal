<?php
// Subscribe Patient (auto-cancel after X months)
add_action('wp_ajax_subscribe_patient', 'hld_subscribe_patient_handler');
add_action('wp_ajax_nopriv_subscribe_patient', 'hld_subscribe_patient_handler');

function hld_subscribe_patient_handler()
{
    if (!isset($_POST['payment_method']) || !isset($_POST['price_id']) || !isset($_POST['duration'])) {
        wp_send_json_error(['message' => 'Missing parameters']);
        wp_die();
    }

    require_once HLD_PLUGIN_PATH . 'vendor/autoload.php';
    \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

    $payment_method  = sanitize_text_field($_POST['payment_method']);
    $price_id        = sanitize_text_field($_POST['price_id']);
    $duration        = (int) sanitize_text_field($_POST['duration']);
    $months          = max(1, $duration); // ensure positive integer

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
        $subscription = \Stripe\Subscription::create([
            'customer' => $customer_id,
            'items' => [['price' => $price_id]],
            'cancel_at' => strtotime("+{$months} months"),
            'expand' => ['latest_invoice.payment_intent'],
        ]);

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
            HLD_Telegra::create_patient();

            HLD_UserSubscriptions::add_subscription(
                $user_id,
                $patient_email,
                $months,
                'med_123',            // Example: Telegra med ID
                'Tirzepatide',        // Example: Medication name
                $subscription
            );

            HLD_ActionItems_Manager::assign_pending_actions_for_plan('glp_1_prefunnel');
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
