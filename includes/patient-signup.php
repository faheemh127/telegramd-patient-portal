<?php
add_shortcode('hld_custom_signup_form', 'hld_render_custom_signup_form');

function hld_render_custom_signup_form()
{

    ob_start();

    $error_message = '';
    $success_message = '';


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