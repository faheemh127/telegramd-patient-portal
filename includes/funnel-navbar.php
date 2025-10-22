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
                <div class="hld_wrap">
                    <div>
                        <svg viewBox="0 0 20 20" class="w-5 fill-ds-dark-250" style="fill: #333">
                            <path fill-rule="evenodd" d="M11.78 5.47a.75.75 0 0 1 0 1.06l-3.47 3.47 3.47 3.47a.75.75 0 1 1-1.06 1.06l-4-4a.75.75 0 0 1 0-1.06l4-4a.75.75 0 0 1 1.06 0Z"></path>
                        </svg>
                    </div>
                    <div style="margin-top: 1px;">
                        Back
                    </div>
                </div>
            </button>

            <!-- Center Logo -->
            <div class="hld-logo" id="hld-logo">
                <img src="<?php echo esc_url(get_site_icon_url(64)); ?>" alt="Logo">
            </div>

            <!-- Help Icon -->
            <span style="color: white;">.</span>
            <button style="display: none;" class="hld-help-btn" id="hld-help-btn" onclick="alert('Help section coming soon!');">
                &#x2753;
            </button>
        </div>
    </nav>

<?php
    return ob_get_clean();
}
add_shortcode('hld_navbar', 'hld_navbar_shortcode');
