<?php
// === Shortcode: [hld_navbar] ===
function hld_navbar_shortcode()
{
    ob_start(); ?>

    <!-- HLD Navbar -->
    <nav class="hld-navbar" id="hld-navbar">
        <div class="hld-navbar-inner">
            <!-- Back Button -->
            <button class="hld-back-btn" id="hld-back-btn" type="button">
                &#8592; Back
            </button>

            <!-- Center Logo -->
            <div class="hld-logo" id="hld-logo">
                <img src="<?php echo esc_url(get_site_icon_url(64)); ?>" alt="Logo">
            </div>

            <!-- Help Icon -->
            <button class="hld-help-btn" id="hld-help-btn" onclick="alert('Help section coming soon!');">
                &#x2753;
            </button>
        </div>
    </nav>

<?php
    return ob_get_clean();
}
add_shortcode('hld_navbar', 'hld_navbar_shortcode');
