<?php
if (! defined('ABSPATH')) exit;

class HLD_ActionItems_Manager {

    /**
     * Seed default action items if not already existing
     */
    public static function seed_default_items() {
        global $wpdb;

        $table = HEALSEND_ACTION_ITEMS_TABLE;

        $default_items = [
            [
                'plan_slug'   => 'glp_1_prefunnel',
                'action_key'  => 'id_upload',
                'label'       => 'ID Upload',
                'description' => 'Upload your identification document to verify your profile.',
                'item_slug'   => 'glp_1_id_upload',
                'sort_order'  => 1,
                'required'    => 1
            ],
            [
                'plan_slug'   => 'glp_1_prefunnel',
                'action_key'  => 'clinical_diff',
                'label'       => 'Clinical Difference Questionnaire',
                'description' => 'Complete a short questionnaire to help us understand your clinical details.',
                'item_slug'   => 'glp_1_clinical_difference',
                'sort_order'  => 2,
                'required'    => 1
            ],
            [
                'plan_slug'   => 'glp_1_prefunnel',
                'action_key'  => 'agreement',
                'label'       => 'Agreement Form',
                'description' => 'Review and accept our treatment agreement to proceed.',
                'item_slug'   => 'glp_1_agreement_form',
                'sort_order'  => 3,
                'required'    => 1
            ],
            [
                'plan_slug'   => 'metabolic',
                'action_key'  => 'agreement',
                'label'       => 'Agreement Form',
                'description' => 'Please review and sign the agreement to continue your metabolic plan.',
                'item_slug'   => 'metabolic_agreement_form',
                'sort_order'  => 1,
                'required'    => 1
            ]
        ];

        foreach ($default_items as $item) {
            $exists = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT COUNT(*) FROM {$table} WHERE plan_slug = %s AND action_key = %s",
                    $item['plan_slug'],
                    $item['action_key']
                )
            );

            if (! $exists) {
                $wpdb->insert(
                    $table,
                    [
                        'plan_slug'   => $item['plan_slug'],
                        'action_key'  => $item['action_key'],
                        'label'       => $item['label'],
                        'description' => $item['description'],
                        'item_slug'   => $item['item_slug'],
                        'sort_order'  => $item['sort_order'],
                        'required'    => $item['required']
                    ],
                    ['%s', '%s', '%s', '%s', '%s', '%d', '%d']
                );
            }
        }
    }
}
