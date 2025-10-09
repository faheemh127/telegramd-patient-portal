<?php
$pending_items = HLD_ActionItems_Manager::get_user_pending_action_items();
if ($pending_items) {
    foreach ($pending_items as $item) {
        hld_action_item(
            esc_html($item['label']),
            esc_html($item['description']),
            esc_url($item['url'])
        );
    }
} else {
    hld_not_found("You have no action items");
}
