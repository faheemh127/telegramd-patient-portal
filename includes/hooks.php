<?php
if (! defined('ABSPATH')) exit; // Exit if accessed directly



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




// Add sidebar overlay to every page
add_action('wp_footer', 'hld_add_sidebar_overlay');

function hld_add_sidebar_overlay()
{
?>
    <div class="hld-sidebar-overlay" id="hldSidebarOverlay">
        <div class="hld-sidebar" id="hldSidebar">
            <button class="hld-sidebar-close" id="hldSidebarClose">&times;</button>
            <div class="hld-sidebar-content">
                <h2>Action Item</h2>
                <?php include HLD_PLUGIN_PATH . 'templates/dashboard/action-items.php';
                ?>
            </div>
        </div>
    </div>
<?php
}


add_action('wp_login', function ($user_login, $user) {
    error_log("patient logged in or signup");

    if (class_exists('HLD_Patient')) {
        error_log("class HLD_Patient exists 81");
        error_log(print_r($user, true));
        HLD_Patient::sync_user_to_patient($user); // Pass the user directly
    }
}, 10, 2);





// Shortcode: [hld_login_button]
function hld_login_button_shortcode()
{

    // If user is logged in
    if (is_user_logged_in()) {
        $url  = home_url('/my-account');
        $text = 'My Account';
        $class = 'hld-btn hld-login-btn-nav';
    } else {
        $url  = home_url('/patient-login');
        $text = 'Login';
        $class = 'hld-btn hld-login-btn-nav';
    }

    // Return HTML for button
    $html = '<a href="' . esc_url($url) . '" class="' . esc_attr($class) . '">' . esc_html($text) . '</a>';

    return $html;
}
add_shortcode('hld_login_button', 'hld_login_button_shortcode');
