 <section id="hdlDashboard" class="hld-dashboard">
     <div class="container">

         <div class="tabs" style="padding-top: 1px;">
             <?php if (hld_should_display_dashboard_nav()): ?>
                 <input type="radio" id="tab0" name="tab-control" checked />
                 <input type="radio" id="tab1" name="tab-control" />
                 <input type="radio" id="tab2" name="tab-control" />
                 <input type="radio" id="tab3" name="tab-control" />
                 <input type="radio" id="tab4" name="tab-control" />
                 <input type="radio" id="tab5" name="tab-control" />
                 <!-- <input type="radio" id="tab6" name="tab-control" /> -->
                 <!-- <input type="radio" id="tab7" name="tab-control" /> -->
                 <input type="radio" id="tab8" name="tab-control" />
                 <input type="radio" id="tab9" name="tab-control" />
                 <!-- Wrap the navigation in a scrollable container -->
                 <div class="tabs-nav-wrapper">
                     <ul class="container">
                         <li class="hld_nav_action_items"><label for="tab0"><span> <svg style="margin-bottom: 2px;" fill="currentColor" width="20px" height="20px" viewBox="0 0 32 32" version="1.1" xmlns="http://www.w3.org/2000/svg">
                                         <title>info</title>
                                         <path d="M11.188 4.781c6.188 0 11.219 5.031 11.219 11.219s-5.031 11.188-11.219 11.188-11.188-5-11.188-11.188 5-11.219 11.188-11.219zM11.063 8.906c-0.313 0.375-0.469 0.813-0.469 1.281 0 0.375 0.125 0.688 0.313 0.906 0.219 0.219 0.531 0.344 0.844 0.344 0.438 0 0.844-0.188 1.156-0.563 0.281-0.344 0.438-0.844 0.438-1.375 0-0.313-0.094-0.594-0.313-0.813s-0.531-0.344-0.844-0.344c-0.406 0-0.813 0.188-1.125 0.563zM8.219 15.375l0.375 0.406c0.281-0.313 0.563-0.563 0.75-0.719 0.188-0.125 0.344-0.188 0.469-0.188 0.094 0 0.188 0.031 0.25 0.094 0.031 0.094 0.063 0.188 0.063 0.344 0 0.781-0.094 1.281-0.5 3.156s-0.625 3.25-0.625 4.156c0 0.344 0.063 0.594 0.188 0.75 0.094 0.156 0.281 0.281 0.531 0.281 0.406 0 1-0.313 1.688-0.844 0.688-0.563 1.375-1.344 2.125-2.344l-0.406-0.344c-0.25 0.313-0.5 0.531-0.688 0.688-0.188 0.125-0.344 0.25-0.469 0.25-0.094 0-0.188-0.094-0.25-0.156-0.031-0.094-0.063-0.219-0.063-0.406 0-0.125 0.031-0.531 0.156-1.25 0.094-0.719 0.063-0.719 0.25-1.781 0.031-0.313 0.125-0.75 0.219-1.281 0.25-1.594 0.406-2.563 0.406-2.875 0-0.281-0.094-0.531-0.188-0.688-0.125-0.156-0.313-0.219-0.531-0.219-0.375 0-0.875 0.281-1.563 0.781-0.688 0.531-1.375 1.25-2.188 2.188z"></path>
                                     </svg>Action Items</span></label></li>
                         <li class="hld_nav_subscriptions"><label for="tab1"><span>Subscriptions</span></label></li>
                         <li class="hld_nav_conversations"><label for="tab2"><span>Messages</span></label></li>
                         <li class="hld_nav_orders"><label for="tab3"><span>Orders</span></label></li>
                         <li class="hld_nav_visits"><label for="tab4"><span>Visits</span></label></li>
                         <li class="hld_nav_profile"><label for="tab5"><span>Profile</span></label></li>
                         <!-- <li class="hld_nav_lab_orders"><label for="tab6"><span>Lab Orders</span></label></li> -->
                         <!-- <li class="hld_nav_support"><label for="tab7"><span>Support</span></label></li> -->
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

                 <!-- this is will only shown if person will have any action item -->

                 <?php include HLD_PLUGIN_PATH . 'templates/dashboard/action-item-notification.php'; ?>

                 <div class="content">
                     <!-- Section 1 -->
                     <section class="container">
                         <?php


                            include HLD_PLUGIN_PATH . 'templates/dashboard/action-items.php';
                            // this template was for dashboard that we initially designed
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
                                hld_action_item(
                                    "Doctor Chat",
                                    "You’ll communicate with your doctor via chat. Once approved, your medication will be prescribed and shipped by our pharmacy.",
                                    home_url('chat-app'),
                                    "Start Chat"
                                );

                            ?>
                             <!-- <iframe
                                id="chat-clinical"
                                src="https://healsend.com/chat-app/"
                                width="100%"
                                height="1000"
                                style="border: none;"
                                loading="lazy"></iframe> -->

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

                     <!-- <section class="container"> -->
                     <?php
                        //  hdl_get_template('dashboard/lab-orders');
                        ?>
                     <!-- </section> -->

                     <section class="container hld-chat-container">


                         <?php
                            if (HLD_UserSubscriptions::has_any_subscription()) {
                                hld_action_item(
                                    "Support Chat",
                                    "You’ll communicate with support via chat.",
                                    home_url('chat-app'),
                                    "Start Chat"
                                );

                                hld_action_item(
                                    "Billing Chat",
                                    "You’ll communicate with billing team via chat.",
                                    home_url('chat-app'),
                                    "Start Chat"
                                );
                            ?>
                             <!-- <iframe
                                id="chat-support"
                                src="https://healsend.com/chat-app/"
                                width="100%"
                                height="1000"
                                style="border: none;"
                                loading="lazy"></iframe> -->

                         <?php } else {
                                hld_not_found("Support will be available once you purchase your first subscription.");
                            } ?>









                     </section>


                     <!-- <section class="container hld-chat-container"> -->


                     <?php
                        // if (HLD_UserSubscriptions::has_any_subscription()) {
                        ?>
                     <!-- <iframe
                                id="chat-billing"
                                src="https://healsend.com/chat-app/"
                                width="100%"
                                height="1000"
                                style="border: none;"
                                loading="lazy"></iframe> -->

                     <?php
                        //  } else {
                        //hld_not_found("Billing chat will be available once you purchase your first subscription.");
                        // } 
                        ?>







                     <!-- </section> -->




                 </div>
             <?php endif; ?>

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