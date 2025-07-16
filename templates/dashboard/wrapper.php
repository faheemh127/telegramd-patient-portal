<?php
// File: templates/dashboard/wrapper.php

defined('ABSPATH') || exit;







function hld_display_fluent_saved_forms_cards()
{
    // Get current user ID
    $user_id = get_current_user_id();
    if (!$user_id) {
        echo '<p>You must be logged in to view your saved forms.</p>';
        return;
    }

    global $wpdb;

    // Search for all user meta keys that start with 'fluent_form_'
    $meta_keys = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT meta_key, meta_value FROM {$wpdb->usermeta} WHERE user_id = %d AND meta_key LIKE %s",
            $user_id,
            'fluent_form_%'
        )
    );

    if (empty($meta_keys)) {
        echo '<p>No saved Fluent Forms found.</p>';
        return;
    }

    echo '<div class="hld-saved-forms-container">';

    foreach ($meta_keys as $meta) {
        // Extract the form ID from the meta key
        preg_match('/fluent_form_(\d+)/', $meta->meta_key, $matches);
        if (!isset($matches[1])) continue;

        $form_id = $matches[1];
        $form_link = get_permalink(); // fallback to current page
        $resume_url = '';

        // Fluent Forms saves resume URLs inside the meta value (usually serialized JSON or plain URL)
        $meta_value = maybe_unserialize($meta->meta_value);

        if (is_array($meta_value) && isset($meta_value['url'])) {
            $resume_url = esc_url($meta_value['url']);
        } elseif (is_string($meta_value) && filter_var($meta_value, FILTER_VALIDATE_URL)) {
            $resume_url = esc_url($meta_value);
        }

        if (!$resume_url) continue;

        // Output the card
?>
        <div
            class="hld-card"
            style="background: #fff; box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1); border-radius: 8px; padding: 50px 50px; max-width: 700px; font-family: Arial, sans-serif; width: 100%; margin: 20px auto;">
            <div
                class="hld-card-content"
                style="text-align: center; margin-bottom: 20px; color: #333; font-size: 15px;">
                You have a saved form entry for <strong>Fluent Form ID #<?php echo esc_html($form_id); ?></strong>. You can resume your submission below.
            </div>
            <div class="hld-card-actions" style="text-align: center; margin-top: 50px;">
                <a
                    href="<?php echo $resume_url; ?>"
                    class="hld-card-btn"
                    target="_blank"
                    style="background-color: #6d6ffc; color: #fff; padding: 10px 16px; border-radius: 5px; text-decoration: none; font-size: 14px;">
                    Resume Form
                </a>
            </div>
        </div>
<?php
    }

    echo '</div>';
}





?>

<section id="hdlDashboard">
    <div class="container">
        <div class="tabs" style="padding-top: 1px;">
            <input type="radio" id="tab0" name="tab-control" checked />
            <input type="radio" id="tab1" name="tab-control" />
            <input type="radio" id="tab2" name="tab-control" />
            <input type="radio" id="tab3" name="tab-control" />
            <input type="radio" id="tab4" name="tab-control" />
            <input type="radio" id="tab5" name="tab-control" />
            <input type="radio" id="tab6" name="tab-control" />

            <!-- Wrap the navigation in a scrollable container -->
            <div class="tabs-nav-wrapper">
                <ul class="container">
                    <li><label for="tab0"><span class="pe-2"></span><span>Home</span></label></li>
                    <li><label for="tab1"><span class="pe-2"></span><span>Lab Order</span></label></li>
                    <li><label for="tab2"><span class="pe-2"></span><span>Order History</span></label></li>
                    <li><label for="tab3"><span class="pe-2"></span><span>Action Items</span></label></li>
                    <li><label for="tab4"><span class="pe-2"></span><span>Message Center</span></label></li>
                    <li><label for="tab5"><span class="pe-2"></span><span>Patient Profile</span></label></li>
                    <li><label for="tab6"><span class="pe-2"></span><span>Visits</span></label></li>
                </ul>
            </div>

            <div class="content">
                <section>
                    <h2>Home</h2>
                    <?php hdl_get_template('dashboard/home', ['user' => $user]); ?>

                </section>

                <section>
                    <h2>Order History</h2>
                    <div class="inner-content">
                        <?php hdl_get_template('dashboard/show-orders'); ?>
                    </div>
                </section>
                <section class="container">
                    <h2>Lab Orders</h2>
                    <iframe
                        src="https://healsend.com/chat-app/"
                        width="100%"
                        height="1000"
                        style="border: none;"
                        loading="lazy"></iframe>


                </section>
                <section>
                    <h2>Patient Profile</h2>
                    <?php hdl_get_template('dashboard/patient-profile', ['user' => $user]); ?>
                </section>
                <section>
                    <h2>Visits</h2>
                    <?php hdl_get_template('dashboard/returns'); ?>
                    <iframe
                        src="https://healsend.com/visit/"
                        width="100%"
                        height="1000"
                        style="border: none;"
                        loading="lazy"></iframe>

                </section>


                <section>
                    <h2>Lab Orders</h2>
                    <?php hdl_get_template('dashboard/lab-orders'); ?>
                </section>

                <section>
                    <h2>Action Items</h2>


                    <?php hld_display_fluent_saved_forms_cards(); ?>






                </section>

            </div>
        </div>
    </div>
</section>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabInputs = document.querySelectorAll('input[name="tab-control"]');
        const tabLabels = document.querySelectorAll('.tabs-nav-wrapper label');

        function updateActiveTab() {
            tabLabels.forEach(label => label.classList.remove('active'));
            tabInputs.forEach((input, index) => {
                if (input.checked) {
                    tabLabels[index].classList.add('active');
                }
            });
        }

        // Initialize on page load
        updateActiveTab();

        // Listen for changes to tab selection
        tabInputs.forEach(input => {
            input.addEventListener('change', updateActiveTab);
        });
    });
</script>