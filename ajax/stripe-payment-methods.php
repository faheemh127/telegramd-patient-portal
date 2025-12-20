
<?php
add_action('wp_ajax_get_payment_methods', 'handle_get_payment_methods');
add_action('wp_ajax_add_payment_method', 'handle_add_payment_method');
add_action('wp_ajax_set_default_payment_method', 'handle_set_default_payment_method');
add_action('wp_ajax_delete_payment_method', 'handle_delete_payment_method');


function handle_get_payment_methods()
{
    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'Customer not logged in.']);
        wp_die();
    }

    require_once HLD_PLUGIN_PATH . 'vendor/autoload.php';
    \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

    check_ajax_referer('payment_nonce', 'nonce');
    $cus_id = get_user_meta(get_current_user_id(), 'stripe_customer_id', true);

    $user_id   = get_current_user_id();
    $user_info = get_userdata($user_id);
    $patient_email = $user_info->user_email;
    $first_name    = $user_info->first_name ?? '';
    $last_name     = $user_info->last_name ?? '';

    $cus_id = HLD_Stripe::get_or_create_stripe_customer($patient_email, $first_name, $last_name);
    $customer = \Stripe\Customer::retrieve($cus_id);
    $default_id = $customer->invoice_settings->default_payment_method;

    $methods = \Stripe\PaymentMethod::all([
        'customer' => $cus_id,
        'type' => 'card',
    ]);

    $formatted = [];
    foreach ($methods->data as $pm) {
        $formatted[] = [
            'id' => $pm->id,
            'last4' => $pm->card->last4,
            'label' => $pm->card->wallet ? ucfirst(str_replace('_', ' ', $pm->card->wallet->type)) : 'Card',
            'is_default' => ($pm->id === $default_id),
        ];
    }

    usort($formatted, fn($a, $b) => $b['is_default'] <=> $a['is_default']);
    wp_send_json_success($formatted);
}

function handle_add_payment_method()
{
    require_once HLD_PLUGIN_PATH . 'vendor/autoload.php';
    \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

    check_ajax_referer('payment_nonce', 'nonce');

    $cus_id = get_user_meta(get_current_user_id(), 'stripe_customer_id', true);
    $user_id   = get_current_user_id();
    $user_info = get_userdata($user_id);
    $patient_email = $user_info->user_email;
    $first_name    = $user_info->first_name ?? '';
    $last_name     = $user_info->last_name ?? '';

    $cus_id = HLD_Stripe::get_or_create_stripe_customer($patient_email, $first_name, $last_name);
    $pm_id = $_POST['pm_id'];

    if (empty($cus_id) || empty($pm_id)) {
        wp_send_json_error(['message' => 'Missing Customer or Payment Method ID.']);
    }

    try {
        $payment_method = \Stripe\PaymentMethod::retrieve($pm_id);
        $payment_method->attach(['customer' => $cus_id]);

        $methods = \Stripe\PaymentMethod::all([
            'customer' => $cus_id,
            'type'     => 'card',
        ]);

        if (count($methods->data) === 1) {
            \Stripe\Customer::update($cus_id, [
                'invoice_settings' => [
                    'default_payment_method' => $pm_id,
                ],
            ]);
            $message = 'Payment method added and set as default.';
        } else {
            $message = 'Backup payment method added successfully.';
        }

        wp_send_json_success(['message' => $message]);
    } catch (\Stripe\Exception\ApiErrorException $e) {
        wp_send_json_error(['message' => $e->getMessage()]);
    } catch (\Exception $e) {
        wp_send_json_error(['message' => 'An unexpected error occurred.']);
    }
}

// 3. Set Default
function handle_set_default_payment_method()
{

    require_once HLD_PLUGIN_PATH . 'vendor/autoload.php';
    \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

    check_ajax_referer('payment_nonce', 'nonce');

    $cus_id = get_user_meta(get_current_user_id(), 'stripe_customer_id', true);
    $user_id   = get_current_user_id();
    $user_info = get_userdata($user_id);
    $patient_email = $user_info->user_email;
    $first_name    = $user_info->first_name ?? '';
    $last_name     = $user_info->last_name ?? '';

    $cus_id = HLD_Stripe::get_or_create_stripe_customer($patient_email, $first_name, $last_name);
    $pm_id = $_POST['pm_id'];

    try {
        \Stripe\Customer::update($cus_id, [
            'invoice_settings' => ['default_payment_method' => $pm_id],
        ]);
        wp_send_json_success();
    } catch (\Exception $e) {
        wp_send_json_error(['message' => $e->getMessage()]);
    }
}

function handle_delete_payment_method()
{

    require_once HLD_PLUGIN_PATH . 'vendor/autoload.php';
    \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

    $cus_id = get_user_meta(get_current_user_id(), 'stripe_customer_id', true);
    $user_id   = get_current_user_id();
    $user_info = get_userdata($user_id);
    $patient_email = $user_info->user_email;
    $first_name    = $user_info->first_name ?? '';
    $last_name     = $user_info->last_name ?? '';

    $cus_id = HLD_Stripe::get_or_create_stripe_customer($patient_email, $first_name, $last_name);
    $target_pm_id = $_POST['pm_id'];

    try {
        $methods = \Stripe\PaymentMethod::all(['customer' => $cus_id, 'type' => 'card']);
        if (count($methods->data) <= 1) {
            wp_send_json_error(['message' => 'You must have at least one backup payment method.']);
        }

        $customer = \Stripe\Customer::retrieve($cus_id);
        $was_default = ($customer->invoice_settings->default_payment_method === $target_pm_id);

        $pm = \Stripe\PaymentMethod::retrieve($target_pm_id);
        $pm->detach();

        if ($was_default) {
            $remaining = \Stripe\PaymentMethod::all(['customer' => $cus_id, 'type' => 'card']);
            if (!empty($remaining->data)) {
                \Stripe\Customer::update($cus_id, [
                    'invoice_settings' => ['default_payment_method' => $remaining->data[0]->id],
                ]);
            }
        }

        wp_send_json_success();
    } catch (\Exception $e) {
        wp_send_json_error(['message' => $e->getMessage()]);
    }
    // add_action('wp_ajax_delete_payment_method', function() {
    //     check_ajax_referer('payment_nonce', 'nonce');
    //     $stripe = new \Stripe\StripeClient('sk_test_...');
    //     $pm_id = $_POST['pm_id'];
    //     $user_id = get_current_user_id();
    //     $cus_id = get_user_meta($user_id, 'stripe_customer_id', true);
    //
    //     // Get current default
    //     $customer = $stripe->customers->retrieve($cus_id);
    //     $is_deleting_default = ($customer->invoice_settings->default_payment_method === $pm_id);
    //
    //     // 1. Detach the card
    //     $stripe->paymentMethods->detach($pm_id);
    //
    //     // 2. Promotion Logic: If deleted default, find the next card and promote it
    //     if ($is_deleting_default) {
    //         $remaining = $stripe->paymentMethods->all(['customer' => $cus_id, 'type' => 'card']);
    //         if (!empty($remaining->data)) {
    //             $new_default = $remaining->data[0]->id;
    //             $stripe->customers->update($cus_id, [
    //                 'invoice_settings' => ['default_payment_method' => $new_default]
    //             ]);
    //         }
    //     }
    //     wp_send_json_success();
    // });
}
