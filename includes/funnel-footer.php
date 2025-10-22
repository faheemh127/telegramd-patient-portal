<?php
// === Shortcode: [hld_footer] ===
function hld_footer_shortcode()
{
    ob_start(); ?>

    <!-- HLD Footer -->
    <div class="hld-footer" id="hld-footer">
        <div class="hld-footer-inner" style="display: flex; justify-content: space-between; align-items: center; padding: 10px 20px; background-color: #f5f5f5; border-top: 1px solid #ddd;">

            <!-- Left Text -->
            <div class="hld-footer-left" style="font-size: 14px; color: #333;">
                © <?php echo date('Y'); ?> <?php echo esc_html(wp_parse_url(home_url(), PHP_URL_HOST)); ?>. All rights reserved.
            </div>

            <!-- Right Google Reviews -->
            <div class="hld-footer-right">
                <a href="https://admin.trustindex.io/api/googleReview?place-id=ChIJyyGCkUtZwokRNF3OWcs6WFg" target="_blank" rel="noopener noreferrer" style="display: inline-flex; align-items: center; gap: 6px; background-color: #4285F4; color: #fff; padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 14px;">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 488 512" width="16" height="16" fill="white">
                        <path d="M488 261.8c0-17.8-1.6-35-4.6-51.8H249v98.2h134.3c-5.8 31-23.1 57.2-49.4 74.8v61h79.8c46.7-43 74.3-106.4 74.3-182.2z" />
                        <path d="M249 492c66.6 0 122.4-22.2 163.2-60.2l-79.8-61c-22.2 15-50.5 24-83.4 24-64 0-118.1-43.1-137.4-101.2H30.1v63.4C70.4 447 153.8 492 249 492z" />
                        <path d="M111.6 293.6c-4.8-15-7.5-31.1-7.5-47.6s2.7-32.6 7.5-47.6v-63.4H30.1C10.9 173.2 0 208.5 0 246s10.9 72.8 30.1 104.6l81.5-57z" />
                        <path d="M249 97.9c36.2 0 68.6 12.5 94.1 37l70.6-70.6C371.4 24.3 315.6 0 249 0 153.8 0 70.4 45 30.1 118.4l81.5 57C130.9 141 185 97.9 249 97.9z" />
                    </svg>
                    Google Reviews
                </a>
            </div>
        </div>
    </div>

<?php
    return ob_get_clean();
}
add_shortcode('hld_footer', 'hld_footer_shortcode');
