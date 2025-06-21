<?php

class DashboardShortcode
{

    public function __construct()
    {
        add_shortcode('dashboard', [$this, 'render_dashboard']);
    }

    public function render_dashboard()
    {
        ob_start();
?>

        <div class="tabs">
            <input type="radio" id="tab1" name="tab-control" checked />
            <input type="radio" id="tab2" name="tab-control" />
            <input type="radio" id="tab3" name="tab-control" />
            <input type="radio" id="tab4" name="tab-control" />

            <ul>
                <li title="Features">
                    <label for="tab1" role="button">
                        &bull;
                        <span>Order History</span>
                    </label>
                </li>
                <li title="Delivery Contents">
                    <label for="tab2" role="button">
                        &raquo;
                        <span>Lab Orders</span>
                    </label>
                </li>
                <li title="Shipping">
                    <label for="tab3" role="button">
                        &rarr;
                        <span>patient-profile</span>
                    </label>
                </li>
                <li title="Returns"><label for="tab4" role="button">&larr; <span>Returns</span></label></li>
            </ul>

            <div class="slider">
                <div class="indicator"></div>
            </div>

            <div class="content">
                <section>
                    <h2>Order History</h2>
                    <div class="inner-content">
                        <?php include plugin_dir_path(__FILE__) . 'tabs/show-orders.php'; ?>
                    </div>
                </section>
                <section>
                    <h2>Lab Orders</h2>
                    <?php include plugin_dir_path(__FILE__) . 'tabs/lab-orders.php'; ?>
                </section>
                <section>
                    <h2>Patient Profile</h2>
                    <?php include plugin_dir_path(__FILE__) . 'tabs/patient-profile.php'; ?>
                </section>
                <section>
                    <h2>Returns</h2>
                    <?php include plugin_dir_path(__FILE__) . 'tabs/returns.php'; ?>
                </section>
            </div>
        </div>

<?php
        return ob_get_clean();
    }
}
