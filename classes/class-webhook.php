<?php
if (! defined('ABSPATH')) {
    exit;
}

class HLD_Webhook
{

    /**
     * Constructor
     */
    public function __construct()
    {
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    /**
     * Register REST API routes
     */
    public function register_routes()
    {

        // Stripe Webhook Route
        register_rest_route(
            'hld/v1',
            '/stripe-webhook',
            [
                'methods'             => 'POST',
                'callback'            => [$this, 'handle_stripe_webhook'],
                'permission_callback' => '__return_true',
            ]
        );

        // Test Route
        register_rest_route(
            'hld/v1',
            '/test',
            [
                'methods'             => ['GET', 'POST'],
                'callback'            => [$this, 'test_endpoint'],
                'permission_callback' => '__return_true',
            ]
        );
    }

    /**
     * Test endpoint (no Stripe logic)
     *
     * @param WP_REST_Request $request
     * @return WP_REST_Response
     */
    public function test_endpoint(WP_REST_Request $request)
    {

        HLD_Affiliate::send_fluentaffiliate_referral(1, 'order::restorder', 139, 10);

        return new WP_REST_Response(
            [
                'status'  => 'success',
                'message' => 'HLD REST API is working correctly',
                'method'  => $request->get_method(),
                'params'  => $request->get_params(),
            ],
            200
        );
    }

    /**
     * Handle Stripe Webhook
     *
     * @param WP_REST_Request $request
     * @return WP_REST_Response
     */
    public function handle_stripe_webhook(WP_REST_Request $request)
    {

        $payload = $request->get_body();

        if (empty($payload)) {
            return new WP_REST_Response(
                ['error' => 'Empty payload'],
                400
            );
        }

        $event = json_decode($payload, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return new WP_REST_Response(
                ['error' => 'Invalid JSON'],
                400
            );
        }

        error_log('Stripe Webhook Event: ' . print_r($event, true));

        $this->process_event($event);

        return new WP_REST_Response(
            ['status' => 'Stripe webhook received'],
            200
        );
    }

    /**
     * Process Stripe Event
     */
    private function process_event($event)
    {

        if (empty($event['type'])) {
            return;
        }

        $event_type = $event['type'];
        $object     = $event['data']['object'] ?? [];

        switch ($event_type) {

            case 'customer.subscription.created':
                $this->subscription_created($object);
                break;

            case 'customer.subscription.updated':
                $this->subscription_updated($object);
                break;

            case 'customer.subscription.deleted':
                $this->subscription_deleted($object);
                break;

            case 'invoice.payment_succeeded':
                $this->payment_succeeded($object);
                break;

            case 'invoice.payment_failed':
                $this->payment_failed($object);
                break;

            default:
                error_log('Unhandled Stripe event: ' . $event_type);
        }
    }

    private function subscription_created($subscription)
    {
        error_log('Subscription Created: ' . ($subscription['id'] ?? ''));
    }

    private function subscription_updated($subscription)
    {
        error_log('Subscription Updated: ' . ($subscription['id'] ?? ''));
        error_log('Status: ' . ($subscription['status'] ?? ''));
    }

    private function subscription_deleted($subscription)
    {
        error_log('Subscription Deleted: ' . ($subscription['id'] ?? ''));
    }

    private function payment_succeeded($invoice)
    {
        error_log('Payment Succeeded: Invoice ' . ($invoice['id'] ?? ''));
    }

    private function payment_failed($invoice)
    {
        error_log('Payment Failed: Invoice ' . ($invoice['id'] ?? ''));
    }
}


new HLD_Webhook();
