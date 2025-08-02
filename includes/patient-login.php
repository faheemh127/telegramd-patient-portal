<?php
add_shortcode('hld_custom_login_form', 'hld_render_custom_login_form');

function hld_render_custom_login_form() {
    if (is_user_logged_in()) {
        return '<p class="hld_logged_in_notice">You are already logged in.</p>';
    }

    ob_start();

    $error_message = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hld_login_nonce']) && wp_verify_nonce($_POST['hld_login_nonce'], 'hld_login_action')) {
        $creds = array(
            'user_login'    => sanitize_user($_POST['hld_username']),
            'user_password' => $_POST['hld_password'],
            'remember'      => true,
        );
        $user = wp_signon($creds, false);

        if (is_wp_error($user)) {
            $error_message = 'Invalid username or password.';
        } else {
            // Ensure user has subscriber role
            if (in_array('subscriber', (array)$user->roles)) {
                wp_redirect(home_url('/my-account'));
                exit;
            } else {
                wp_logout();
                $error_message = 'Access denied. Only subscribers can log in here.';
            }
        }
    }
    ?>

    <div class="hld_login_wrapper">
        <?php if (!empty($error_message)) : ?>
            <div class="hld_error_message"><?php echo esc_html($error_message); ?></div>
        <?php endif; ?>

        <h2 class="hld_patient_login_title">Healsend Patient Login</h2>
        <form method="post" class="hld_login_form">
            <label for="hld_username">Username or Email</label>
            <input type="text" name="hld_username" id="hld_username" required />

            <label for="hld_password">Password</label>
            <input type="password" name="hld_password" id="hld_password" required />

            <?php wp_nonce_field('hld_login_action', 'hld_login_nonce'); ?>

            <button type="submit" class="hld_login_button">Login</button>
        </form>

        <div class="hld_or_separator">or</div>

        <div class="hld_social_login">
            <?php echo do_shortcode('[nextend_social_login provider="google"]'); ?>
        </div>
    </div>

    <?php
    return ob_get_clean();
}
