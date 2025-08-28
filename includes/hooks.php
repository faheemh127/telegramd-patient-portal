<?php




// Disalbe the admin bar for patients
add_action('after_setup_theme', function () {
    if (current_user_can('subscriber')) {
        show_admin_bar(false);
    }
});
add_action('init', function () {
    if (isset($_GET['test_charge'])) {
        $result = hld_charge_later(get_current_user_id(), 10); // $500
        var_dump($result);
        exit;
    }
});
