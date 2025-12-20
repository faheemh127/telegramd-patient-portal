<?php
// File: templates/dashboard/wrapper.php

defined('ABSPATH') || exit;




if (!function_exists('hld_display_fluent_saved_forms_cards')) {
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
                'fluent_form_%',
            ),
        );

        if (empty($meta_keys)) {
            echo '<p>No saved Fluent Forms found.</p>';
            return;
        }

        echo '<div class="hld-saved-forms-container">';

        foreach ($meta_keys as $meta) {
            // Extract the form ID from the meta key
            preg_match('/fluent_form_(\d+)/', $meta->meta_key, $matches);
            if (!isset($matches[1])) {
                continue;
            }

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

            if (!$resume_url) {
                continue;
            }

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
}

// if wanna display the clinical chat in full window
if (isset($_GET["care-team"])) {
    include HLD_PLUGIN_PATH . 'templates/dashboard/care-team-chat.php';
    return;
}

if (isset($_GET["my-payment-methods"])) {
    include HLD_PLUGIN_PATH . 'templates/dashboard/my-payment-methods.php';
    return;
}

if (isset($_GET["payment-succeeded"])) {
    include HLD_PLUGIN_PATH . 'templates/dashboard/payment-succeeded.php';
    return;
}
if (isset($_GET["questionnaire-answered"])) {
    include HLD_PLUGIN_PATH . 'templates/dashboard/questionnaire-answered.php';
    return;
}


?>

<section id="hdlDashboard">
    <div class="container">

        <div class="tabs" style="padding-top: 1px;">
            <?php if (hld_should_display_dashboard_nav()): ?>
                <input type="radio" id="tab0" name="tab-control" checked />
                <input type="radio" id="tab1" name="tab-control" />
                <input type="radio" id="tab2" name="tab-control" />
                <input type="radio" id="tab3" name="tab-control" />
                <input type="radio" id="tab4" name="tab-control" />
                <input type="radio" id="tab5" name="tab-control" />
                <input type="radio" id="tab6" name="tab-control" />
                <input type="radio" id="tab7" name="tab-control" />
                <input type="radio" id="tab8" name="tab-control" />
                <input type="radio" id="tab9" name="tab-control" />
                <!-- Wrap the navigation in a scrollable container -->
                <div class="tabs-nav-wrapper">
                    <ul class="container">
                        <li class="hld_nav_action_items"><label for="tab0"><span>Action Items</span></label></li>
                        <li class="hld_nav_subscriptions"><label for="tab1"><span>Subscriptions</span></label></li>
                        <li class="hld_nav_conversations"><label for="tab2"><span>Conversations</span></label></li>
                        <li class="hld_nav_orders"><label for="tab3"><span>Orders</span></label></li>
                        <li class="hld_nav_visits"><label for="tab4"><span>Visits</span></label></li>
                        <li class="hld_nav_profile"><label for="tab5"><span>Profile</span></label></li>
                        <li class="hld_nav_lab_orders"><label for="tab6"><span>Lab Orders</span></label></li>
                        <li class="hld_nav_support"><label for="tab7"><span>Support</span></label></li>
                        <li class="hld_nav_billing"><label for="tab8"><span>Billing</span></label></li>
                        <li class="hld_nav_logout"><label for="tab9"><span class="hld_btn_logout_main"><a href="<?= wp_logout_url(home_url('?message=User+logged+out')); ?>">Logout</a></span></label></li>
                    </ul>
                </div>

                <!-- <p class="hld_tabs_hint">Swipe to navigate.</p> -->
            <?php endif; ?>


            <?php // error_log(print_r(get_user_meta(get_current_user_id()), true));
            ?>

            <?php if (isset($_GET['upload-id'])) : ?>
                <?php require_once HLD_PLUGIN_PATH . 'templates/upload-id.php'; ?>
            <?php elseif (isset($_GET['informed-consent-for-treatment'])) : ?>
                <?php require_once HLD_PLUGIN_PATH . 'templates/glp-agreement-form.php'; ?>
            <?php else : ?>
                <div class="content">
                    <!-- Section 1 -->
                    <section class="container">






                        <?php
                        include HLD_PLUGIN_PATH . 'templates/dashboard/action-items.php';
                // hdl_get_template('dashboard/home', ['user' => $user]);
                ?>

                    </section>
                    <!-- Section 2 -->
                    <section class="container">


                        <?php hdl_get_template('dashboard/subscription'); ?>


                    </section>

                    <!-- Section 3 -->
                    <section class="container hld-chat-container">


                        <?php

                if (HLD_UserSubscriptions::has_any_subscription()) {


                    ?>
                            <iframe
                                id="chat-clinical"
                                src="https://healsend.com/chat-app/"
                                width="100%"
                                height="1000"
                                style="border: none;"
                                loading="lazy"></iframe>

                        <?php } else {
                            hld_not_found("Clinical chat will be activated once you purchase your first subscription.");
                        } ?>


                    </section>
                    <!-- Section 4 -->
                    <section class="container">
                        <h2>Order History</h2>
                        <div class="inner-content">
                            <?php hdl_get_template('dashboard/show-orders'); ?>
                        </div>
                    </section>
                    <!-- Section 5 -->
                    <section class="container">
                        <?php // hdl_get_template('dashboard/returns');
                        ?>



                        <?php
                        if (HLD_UserSubscriptions::has_any_subscription()) {
                            ?>
                            <iframe
                                src="https://healsend.com/visit/"
                                width="100%"
                                height="1000"
                                style="border: none;"
                                loading="lazy"></iframe>

                        <?php } else {
                            hld_not_found("Your visit records will be available once you purchase your first subscription.");
                        } ?>








                    </section>
                    <section class="container">
                        <h2>Patient Profile</h2>
                        <?php hdl_get_template('dashboard/patient-profile', ['user' => $user]); ?>
                    </section>
                    <!-- Section 6 -->

                    <section class="container">
                        <?php hdl_get_template('dashboard/lab-orders');
                ?>
                    </section>

                    <section class="container hld-chat-container">


                        <?php
                if (HLD_UserSubscriptions::has_any_subscription()) {
                    ?>
                            <iframe
                                id="chat-support"
                                src="https://healsend.com/chat-app/"
                                width="100%"
                                height="1000"
                                style="border: none;"
                                loading="lazy"></iframe>

                        <?php } else {
                            hld_not_found("Support will be available once you purchase your first subscription.");
                        } ?>









                    </section>


                    <section class="container hld-chat-container">


                        <?php
                        if (HLD_UserSubscriptions::has_any_subscription()) {
                            ?>
                            <iframe
                                id="chat-billing"
                                src="https://healsend.com/chat-app/"
                                width="100%"
                                height="1000"
                                style="border: none;"
                                loading="lazy"></iframe>

                        <?php } else {
                            hld_not_found("Billing chat will be available once you purchase your first subscription.");
                        } ?>







                    </section>



                    <!-- Section 7 -->
                    <!-- <section> -->
                    <!-- <h2>Action Items</h2> -->
                    <?php // hld_display_fluent_saved_forms_cards();
                    ?>
                    <!-- </section> -->

                </div>
            <?php endif; ?>

        </div>

    </div>
</section>



<!-- Sidebar Wrapper -->
<!-- <div class="hld-sidebar-overlay" id="hldSidebarOverlay">
    <div class="hld-sidebar" id="hldSidebar">
        <button class="hld-sidebar-close" id="hldSidebarClose">&times;</button>
        <div class="hld-sidebar-content">
            <h2>Action Item</h2>
            <?php // include HLD_PLUGIN_PATH . 'templates/dashboard/action-items.php';
            ?>
        </div>
    </div>
</div> -->



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
