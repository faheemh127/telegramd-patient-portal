<?php
// File: templates/dashboard/wrapper.php

defined('ABSPATH') || exit;
?>

<section id="hdlDashboard">
    <div class="container">
        <div class="tabs">
            <input type="radio" id="tab0" name="tab-control" checked />
            <input type="radio" id="tab1" name="tab-control" />
            <input type="radio" id="tab2" name="tab-control" />
            <input type="radio" id="tab3" name="tab-control" />
            <input type="radio" id="tab4" name="tab-control" />

            <ul>
                <li><label for="tab0"><span class="pe-2"><?= $icons['home'] ?></span><span>Home</span></label></li>
                <li><label for="tab1"><span class="pe-2"><?= $icons['order_history'] ?></span><span>Order History</span></label></li>
                <li><label for="tab2"><span class="pe-2"><?= $icons['lab_orders'] ?></span><span>Lab Orders</span></label></li>
                <li><label for="tab3"><span class="pe-2"><?= $icons['patient_profile'] ?></span><span>Patient Profile</span></label></li>
                <li><label for="tab4"><span class="pe-2"><?= $icons['returns'] ?></span><span>Subscriptions</span></label></li>
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
                <section>
                    <h2>Lab Orders</h2>
                    <?php hdl_get_template('dashboard/lab-orders'); ?>
                </section>
                <section>
                    <h2>Patient Profile</h2>
                    <?php hdl_get_template('dashboard/patient-profile', ['user' => $user]); ?>
                </section>
                <section>
                    <h2>Subscriptions</h2>
                    <?php hdl_get_template('dashboard/returns'); ?>
                </section>
            </div>
        </div>
    </div>
</section>