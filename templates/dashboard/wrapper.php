<?php
// File: templates/dashboard/wrapper.php

defined('ABSPATH') || exit;
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

            <style>
                #hdlDashboard ul {
                    background: white;
                    padding: 15px 0 !important;
                    border-radius: 50px;
                    margin-bottom: 0 !important;
                    margin-right: auto !important;
                    margin-left: auto !important overflow: hidden !important;
                }
            </style>
            <ul class="container">
                <li><label for="tab0"><span class="pe-2"></span><span>Home</span></label></li>
                <li><label for="tab1"><span class="pe-2"></span><span>Order History</span></label></li>
                <li><label for="tab2"><span class="pe-2"></span><span>Message Center</span></label></li>
                <li><label for="tab3"><span class="pe-2"></span><span>Patient Profile</span></label></li>
                <li><label for="tab4"><span class="pe-2"></span><span>Visits</span></label></li>
                <li><label for="tab5"><span class="pe-2"></span><span>Lab Order</span></label></li>
                <li><label for="tab6"><span class="pe-2"></span><span>Action Items</span></label></li>

            </ul>

            <div class="slider">
                <div class="indicator"></div>
            </div>

            <div class="content">
                <section>
                    <h2>Home</h2>
                    <?php hdl_get_template('dashboard/home'); ?>

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
                    <div
                        class="hld-card"
                        style="
    background: #fff;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
    padding: 50px 50px;
    max-width: 700px;
    font-family: Arial, sans-serif;
    width: 100%;
    margin: auto;
  ">
                        <div
                            class="hld-card-content"
                            style="text-align: center;margin-bottom: 20px; color: #333; font-size: 15px">
                            This is a sample card with some dummy text inside. You can use it to display
                            messages or call-to-actions in your plugin or page.
                        </div>
                        <div class="hld-card-actions" style="text-align: center; margin-top: 50px">
                            <a
                                href="/"
                                class="hld-card-btn"
                                style="
        background-color: #6d6ffc;
        color: #fff;
        padding: 10px 16px;
        border-radius: 5px;
        text-decoration: none;
        font-size: 14px;
      ">
                                Action Item
                            </a>
                        </div>
                    </div>


                </section>

            </div>
        </div>
    </div>
</section>