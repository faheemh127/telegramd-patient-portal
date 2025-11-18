<?php
$pending_items = HLD_ActionItems_Manager::get_user_pending_action_items();


if ($pending_items) {
    foreach ($pending_items as $item) {
        // Append telegra_order_id to the URL safely
        $url_with_param = add_query_arg(
            'telegra_order_id',
            $item['telegra_order_id'],
            esc_url($item['url'])
        );

        hld_action_item(
            esc_html($item['label']),
            esc_html($item['description']),
            esc_url($url_with_param)
        );
    }
} else {
    hld_not_found("You have no action items");
}
