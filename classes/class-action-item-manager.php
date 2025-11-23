<?php

if (! defined('ABSPATH')) {
    exit;
}

class HLD_ActionItems_Manager
{
    /**
     * Seed default action items if not already existing
     */
    public static function seed_default_items()
    {
        global $wpdb;

        $table = HEALSEND_ACTION_ITEMS_TABLE;

        $default_items = [
            [
                'plan_slug'          =>  HLD_GENERAL_ACTION_ITEM,
                'action_key'         => 'id_upload',
                'label'              => 'ID Verification Pending',
                'description'        => 'As required by law, you must upload a form of personal identification. This can be a driver\'s license, a state-issued ID, or a passport.',
                'item_slug'          => 'my-account?upload-id',
                'quinst_array_index' => '3',
                'sort_order'         => 1,
                'required'           => 1,
            ],
            [
                'plan_slug'          => HLD_GENERAL_ACTION_ITEM,
                'action_key'         => 'agreement',
                'label'              => 'Agreement Form',
                'description'        => 'Review and accept our treatment agreement to proceed.',
                'item_slug'          => 'my-account?informed-consent-for-treatment',
                'quinst_array_index' => '4',
                'sort_order'         => 3,
                'required'           => 1,
            ],
            [
                'plan_slug'          => HLD_GLP_WEIGHT_LOSS_SLUG,
                'action_key'         => 'clinical_diff',
                'label'              => 'Complete Your GLP-1 Weight Loss Visit',
                'description'        => 'You recently started a GLP-1 weight loss visit and still need to answer a few remaining questions. Pick up where you left off and complete your visit today.',
                'item_slug'          => 'glp-1-weight-loss-intake',
                'quinst_array_index' => '1,2',
                'sort_order'         => 2,
                'required'           => 1,
            ],
            [
                'plan_slug'          => HLD_METABOLIC_SLUG,
                'action_key'         => 'agreement',
                'label'              => 'Metabolic Enhancement Questionnaire',
                'description'        => 'A quick assessment to help us understand your metabolic health and create a personalized enhancement plan.',
                'item_slug'          => 'metabolic-enhancers-initial-intake-form',
                'quinst_array_index' => '0',
                'sort_order'         => 1,
                'required'           => 1,
            ],
        ];

        foreach ($default_items as $item) {
            $exists = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT COUNT(*) FROM {$table} WHERE plan_slug = %s AND action_key = %s",
                    $item['plan_slug'],
                    $item['action_key']
                )
            );

            if (!$exists) {
                $wpdb->insert(
                    $table,
                    [
                        'plan_slug'          => $item['plan_slug'],
                        'action_key'         => $item['action_key'],
                        'label'              => $item['label'],
                        'description'        => $item['description'],
                        'item_slug'          => $item['item_slug'],
                        'quinst_array_index' => $item['quinst_array_index'],
                        'sort_order'         => $item['sort_order'],
                        'required'           => $item['required'],
                    ],
                    ['%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d']
                );
            }
        }
    }


    public static function mark_action_item_completed($telegra_order_id, $plan_slug)
    {
        global $wpdb;

        // Table name
        $table = HEALSEND_USER_ACTIONS_TABLE;


        if (empty($telegra_order_id) || empty($plan_slug)) {
            return false; // invalid input
        }

        // Check if row exists
        $row_exists = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$table} WHERE telegra_order_id = %s AND action_key = %s",
                $telegra_order_id,
                $plan_slug
            )
        );

        if (!$row_exists) {
            return false; // no matching row found
        }

        // Update the row: set status to 'completed' only
        $updated = $wpdb->update(
            $table,
            [
                'status' => 'completed',
            ],
            [
                'telegra_order_id' => $telegra_order_id,
                'action_key'        => $plan_slug,
            ],
            [
                '%s', // status
            ],
            [
                '%s', // telegra_order_id
                '%s', // plan_slug
            ]
        );

        return $updated !== false;
    }




    public static function get_refund_requested_action_item()
    {
        if (!is_user_logged_in()) {
            return false;
        }

        global $wpdb;

        $user = wp_get_current_user();
        $email = sanitize_email($user->user_email);

        $table = HEALSEND_SUBSCRIPTIONS_TABLE;


        // Check table existence
        $table_exists = $wpdb->get_var(
            $wpdb->prepare("SHOW TABLES LIKE %s", $table)
        );

        if ($table_exists !== $table) {
            error_log("Healsend Error: Missing required table: {$table}");
            return false;
        }



        // Check if any subscription has refund_status = requested
        $row = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$table} 
             WHERE patient_email = %s 
             AND refund_status = 'requested' 
             LIMIT 1",
                $email
            ),
            ARRAY_A
        );

        if (!$row) {
            return false; // No refund request found
        }

        // Build action item
        return [
            'label'       => 'Refund Requested',
            'description' => 'Your refund request for the subscription has been successfully received. You may review the request anytime in your Subscription section and proceed to complete the refund process when ready.',
            'url'         => home_url('/my-account?subscription'),
            'telegra_order_id' => $row['telegra_order_id'],
        ];
    }





    /**
     * Assign all pending action items for a given plan to the currently logged-in user's email.
     *
     * @param string $plan_slug e.g. 'glp_1_prefunnel'
     */
    public static function assign_pending_actions_for_plan($plan_slug, $order_id = null)
    {
        if (!is_user_logged_in()) {
            return; // no user logged in
        }

        $current_user = wp_get_current_user();
        $user_email = $current_user->user_email;

        if (empty($user_email)) {
            return;
        }

        global $wpdb;
        $action_items_table = HEALSEND_ACTION_ITEMS_TABLE;
        $user_actions_table = HEALSEND_USER_ACTIONS_TABLE;

        // Get all action keys for this plan
        $actions = $wpdb->get_results(
            $wpdb->prepare("SELECT action_key FROM {$action_items_table} WHERE plan_slug = %s", $plan_slug)
        );

        if (empty($actions)) {
            return;
        }

        // Insert all as pending if not already existing
        foreach ($actions as $action) {
            $exists = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT COUNT(*) FROM {$user_actions_table} WHERE patient_email = %s AND plan_slug = %s AND action_key = %s",
                    $user_email,
                    $plan_slug,
                    $action->action_key
                )
            );

            if (!$exists) {
                $wpdb->insert(
                    $user_actions_table,
                    [
                        'patient_email'     => $user_email,
                        'plan_slug'         => $plan_slug,
                        'action_key'        => $action->action_key,
                        'status'            => 'pending',
                        'telegra_order_id'  => $order_id, // ✅ new field added here
                    ],
                    ['%s', '%s', '%s', '%s', '%s']
                );
            }
        }
    }


    /**
     * Get all pending action items for the currently logged-in user.
     *
     * @return array|false  Array of pending actions with label, description, and item_url, or false if none.
     */
    public static function get_user_pending_action_items()
    {
        if (!is_user_logged_in()) {
            return false;
        }

        $current_user = wp_get_current_user();
        $user_email   = $current_user->user_email;

        if (empty($user_email)) {
            return false;
        }

        global $wpdb;
        $user_actions_table = HEALSEND_USER_ACTIONS_TABLE;
        $action_items_table = HEALSEND_ACTION_ITEMS_TABLE;




        // Check if required tables exist before running queries
        $missing_tables = [];

        $check_tables = [
            $user_actions_table,
            $action_items_table
        ];

        foreach ($check_tables as $table_name) {
            $table_exists = $wpdb->get_var(
                $wpdb->prepare("SHOW TABLES LIKE %s", $table_name)
            );

            if ($table_exists !== $table_name) {
                $missing_tables[] = $table_name;
            }
        }

        if (!empty($missing_tables)) {
            error_log("Healsend Error: Missing required tables: " . implode(', ', $missing_tables));
            return false;
        }



        // Get all pending user actions joined with action item info + telegra_order_id
        $results = $wpdb->get_results(
            $wpdb->prepare("
            SELECT 
                ai.label, 
                ai.description, 
                ai.item_slug, 
                ua.plan_slug, 
                ua.action_key,
                ua.telegra_order_id
            FROM {$user_actions_table} AS ua
            INNER JOIN {$action_items_table} AS ai
                ON ua.plan_slug = ai.plan_slug
                AND ua.action_key = ai.action_key
            WHERE ua.patient_email = %s
                AND ua.status = 'pending'
            ORDER BY ai.sort_order ASC
        ", $user_email)
        );

        if (empty($results)) {
            return false;
        }

        // Format for display
        $pending_items = [];
        foreach ($results as $row) {
            $pending_items[] = [
                'label'            => $row->label,
                'description'      => $row->description,
                'url'              => home_url('/' . ltrim($row->item_slug, '/')),
                'plan_slug'        => $row->plan_slug,
                'action_key'       => $row->action_key,
                'telegra_order_id' => $row->telegra_order_id, // ✅ added field
            ];
        }

        return $pending_items;
    }
} //class ends
