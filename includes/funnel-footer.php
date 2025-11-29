<?php
// === Shortcode: [hld_footer] ===
function hld_footer_shortcode()
{
    ob_start(); ?>

    <!-- HLD Footer -->
    <div class="hld-footer" id="hld-footer">
        <div class="hld-footer-inner" style="display: flex; justify-content: space-between; align-items: center; padding: 10px 20px; background-color: transparent; border-top: 1px solid #ddd;">

            <!-- Left Text -->
            <div class="hld-footer-left" style="font-size: 14px; color: #333; max-width: 100px;">
                <img src="<?php echo HLD_PLUGIN_URL . "/images/hippa_complaint.png"; ?>" />
            </div>


            <!-- Right Google Reviews -->
            <div class="hld-footer-right">

                <?php echo do_shortcode('[trustindex no-registration="google"]'); ?>
            </div>
        </div>
    </div>
    <!-- This input we are using for date picker Due to ID not available in input field that's why we are using this technique -->
<!-- <input
  readonly
  type="text"
  id="hldPrefunnelDOBInput"
  placeholder=""

> -->
<!-- style="position:absolute; left:-9999px; opacity:0; pointer-events:none;"   -->

<?php
    return ob_get_clean();
}
add_shortcode('hld_footer', 'hld_footer_shortcode');
