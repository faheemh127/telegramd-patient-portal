<?php
add_filter('login_redirect', 'hld_force_login_redirect', 10, 3);
function hld_force_login_redirect($redirect_to, $requested_redirect_to, $user)
{
    if (isset($user->roles) && in_array('subscriber', $user->roles)) {
        return home_url('/my-account');
    }
    return $redirect_to;
}

add_shortcode('hld_custom_login_form', 'hld_render_custom_login_form');

function hld_render_custom_login_form()
{
    ob_start();

    $error_message = apply_filters('hld_login_error_message', '');

?>

    <div class="hld_login_wrapper">
        <?php if (!empty($error_message)) : ?>
            <div class="hld_error_message"><?php echo esc_html($error_message); ?></div>
        <?php endif; ?>

        <h2 class="hld_patient_login_title">Welcome Back</h2>
        <form method="post" class="hld_login_form">
            <input
                type="text"
                name="hld_username"
                id="hld_username"
                placeholder="Email"
                required
                <?php if (!empty($error_message)) : ?>
                style="margin-bottom: 3px;"
                <?php endif; ?> />
            <?php if (!empty($error_message)) : ?>
                <p class="hld_error"><?php echo esc_html($error_message); ?></p>
            <?php endif; ?>

            <input type="password" name="hld_password" id="hld_password" placeholder="Password" style="margin-bottom: 5px;" required />

            <!-- 🔗 Forgot Password Link -->
            <div class="hld_forgot_password">
                <a href="<?php echo wp_lostpassword_url(); ?>" style="font-size: 0.875rem">Forgot Password?</a>
            </div>

            <!-- ✅ Agreement Notice (no checkbox) -->
            <div class="hld_terms_consent" style="margin: 10px 0; font-size: 0.9rem;">
                <p style="margin: 0;">
                    By continuing, you agree to the
                    <a href="https://healsend.com/wp-content/uploads/2025/10/Privacy_Policy_US.pdf" target="_blank" rel="noopener noreferrer">Privacy Policy</a>,
                    <a href="https://healsend.com/wp-content/uploads/2025/10/Terms-of-Service.pdf" target="_blank" rel="noopener noreferrer">Terms</a>, and
                    <a href="https://healsend.com/wp-content/uploads/2025/10/Consent-to-Telehealth.pdf" target="_blank" rel="noopener noreferrer">Telehealth Consent</a>.
                </p>
            </div>

            <?php wp_nonce_field('hld_login_action', 'hld_login_nonce'); ?>

            <button type="submit" class="hld_login_button">Login</button>
        </form>

        <div class="hld_or_separator">or</div>

        <div class="hld_social_login">
            <?php echo do_shortcode('[nextend_social_login provider="google"]'); ?>
            <div class="hld_apple_signin">
                <?php echo do_shortcode('[nextend_social_login provider="apple"]'); ?>
            </div>

        </div>

        <div class="hld_create_wrap">
            <p>First time here?
                <a href="<?php echo esc_url(home_url('/patient-signup/')); ?>">Create an account</a>
            </p>
        </div>
    </div>


    <script>
        document.addEventListener("DOMContentLoaded", function() {

            // Redirect if already logged in
            if (document.body.classList.contains("logged-in")) {
                window.location.href = "<?php echo esc_url(home_url('/my-account')); ?>";
                return;
            }

            // Handle login button loading state
            const form = document.querySelector(".hld_login_form");
            const button = document.querySelector(".hld_login_button");

            if (form && button) {
                form.addEventListener("submit", function() {
                    button.disabled = true;
                    button.innerHTML = "Logging in…"; // 🔥 Update text instantly

                    // Optional small style upgrade
                    button.style.opacity = "0.7";
                    button.style.cursor = "not-allowed";

                    // Optional: add a spinner
                    // button.innerHTML = '<span class="spinner"></span> Logging in…';
                });
            }
        });
    </script>


<?php
    return ob_get_clean();
}
?>