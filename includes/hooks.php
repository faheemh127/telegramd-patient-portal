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


// let user not to access the limited access wp-admin even.
function hld_restrict_subscriber_admin_access()
{
    if (is_admin() && ! defined('DOING_AJAX') && current_user_can('subscriber')) {
        wp_redirect(home_url()); // redirect to homepage (or dashboard page)
        exit;
    }
}
add_action('admin_init', 'hld_restrict_subscriber_admin_access');


// hook into nextend social so always it should redirect to the redirect attribute link in shortcode
add_filter('nsl_redirect_url', function ($url, $provider) {
    // If shortcode has redirect attribute, Nextend passes it in $_REQUEST['redirect']
    if (isset($_REQUEST['redirect']) && !empty($_REQUEST['redirect'])) {
        return esc_url_raw($_REQUEST['redirect']);
    }
    return $url; // fallback
}, 20, 2);
