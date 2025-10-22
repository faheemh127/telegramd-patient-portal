<?php
add_shortcode('hld_custom_signup_form', 'hld_render_custom_signup_form');

function hld_render_custom_signup_form()
{
    if (is_user_logged_in()) {
        wp_safe_redirect(home_url('/my-account'));
        exit;
    }

    ob_start();

    $error_message = '';
    $success_message = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hld_signup_nonce']) && wp_verify_nonce($_POST['hld_signup_nonce'], 'hld_signup_action')) {

        $username = sanitize_user($_POST['hld_username']);
        $email    = sanitize_email($_POST['hld_email']);
        $password = $_POST['hld_password'];

        if (username_exists($username) || email_exists($email)) {
            $error_message = 'Username or email already exists.';
        } elseif (empty($username) || empty($email) || empty($password)) {
            $error_message = 'All fields are required.';
        } else {
            // Create user as subscriber
            $user_id = wp_create_user($username, $password, $email);

            if (is_wp_error($user_id)) {
                $error_message = 'Registration failed. Please try again.';
            } else {
                // Set role to subscriber
                $user = new WP_User($user_id);
                $user->set_role('subscriber');

                // Log the user in immediately
                $creds = array(
                    'user_login'    => $username,
                    'user_password' => $password,
                    'remember'      => true,
                );

                $logged_in_user = wp_signon($creds, false);

                if (!is_wp_error($logged_in_user)) {
                    wp_safe_redirect(home_url('/my-account'));
                    exit;
                } else {
                    $error_message = 'Registration successful, but automatic login failed. Please log in manually.';
                }
            }
        }
    }
?>
    <div class="hld_login_wrapper">
        <?php if (!empty($error_message)) : ?>
            <p class="hld_error"><?php echo esc_html($error_message); ?></p>
        <?php endif; ?>

        <h2 class="hld_patient_login_title">Create an Account</h2>

        <form method="post" class="hld_login_form">

            <input
                type="text"
                name="hld_username"
                id="hld_username"
                placeholder="Username"
                required />

            <input
                type="email"
                name="hld_email"
                id="hld_email"
                placeholder="Email"
                required />

            <input
                type="password"
                name="hld_password"
                id="hld_password"
                placeholder="Password"
                required />

            <!-- ✅ Agreement Notice (no checkbox) -->
            <div class="hld_terms_consent" style="margin: 10px 0; font-size: 0.9rem;">
                <p style="margin: 0;">
                    By continuing, you agree to the
                    <a href="https://healsend.com/wp-content/uploads/2025/10/Privacy_Policy_US.pdf" target="_blank" rel="noopener noreferrer">Privacy Policy</a>,
                    <a href="https://healsend.com/wp-content/uploads/2025/10/Terms-of-Service.pdf" target="_blank" rel="noopener noreferrer">Terms</a>, and
                    <a href="https://healsend.com/wp-content/uploads/2025/10/Consent-to-Telehealth.pdf" target="_blank" rel="noopener noreferrer">Telehealth Consent</a>.
                </p>
            </div>

            <?php wp_nonce_field('hld_signup_action', 'hld_signup_nonce'); ?>

            <button type="submit" class="hld_login_button">Sign Up</button>
        </form>

        <div class="hld_or_separator">or</div>

        <div class="hld_social_login">
            <?php echo do_shortcode('[nextend_social_login provider="google"]'); ?>
        </div>

        <div class="hld_create_wrap">
            <p>Already have an account?
                <a href="<?php echo esc_url(home_url('/patient-login/')); ?>">Login</a>
            </p>
        </div>
    </div>
<?php
    return ob_get_clean();
}
?>
