<?php

class DashboardShortcode
{

    public $icon_order_history;
    public $icon_lab_history;
    public $icon_patient_profile;
    public $icon_returns;


    public function __construct()
    {
        add_shortcode('dashboard', [$this, 'render_dashboard']);
        $this->icons();
    }


    public function icons()
    {

        $this->icon_order_history = '<svg width="20px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M75 75L41 41C25.9 25.9 0 36.6 0 57.9L0 168c0 13.3 10.7 24 24 24l110.1 0c21.4 0 32.1-25.9 17-41l-30.8-30.8C155 85.5 203 64 256 64c106 0 192 86 192 192s-86 192-192 192c-40.8 0-78.6-12.7-109.7-34.4c-14.5-10.1-34.4-6.6-44.6 7.9s-6.6 34.4 7.9 44.6C151.2 495 201.7 512 256 512c141.4 0 256-114.6 256-256S397.4 0 256 0C185.3 0 121.3 28.7 75 75zm181 53c-13.3 0-24 10.7-24 24l0 104c0 6.4 2.5 12.5 7 17l72 72c9.4 9.4 24.6 9.4 33.9 0s9.4-24.6 0-33.9l-65-65 0-94.1c0-13.3-10.7-24-24-24z"/></svg>';

        $this->icon_lab_history = '<svg width="20px"  xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M288 0L160 0 128 0C110.3 0 96 14.3 96 32s14.3 32 32 32l0 132.8c0 11.8-3.3 23.5-9.5 33.5L10.3 406.2C3.6 417.2 0 429.7 0 442.6C0 480.9 31.1 512 69.4 512l309.2 0c38.3 0 69.4-31.1 69.4-69.4c0-12.8-3.6-25.4-10.3-36.4L329.5 230.4c-6.2-10.1-9.5-21.7-9.5-33.5L320 64c17.7 0 32-14.3 32-32s-14.3-32-32-32L288 0zM192 196.8L192 64l64 0 0 132.8c0 23.7 6.6 46.9 19 67.1L309.5 320l-171 0L173 263.9c12.4-20.2 19-43.4 19-67.1z"/></svg>';

        $this->icon_patient_profile = '<svg width="20px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M32 32c17.7 0 32 14.3 32 32l0 256 224 0 0-160c0-17.7 14.3-32 32-32l224 0c53 0 96 43 96 96l0 224c0 17.7-14.3 32-32 32s-32-14.3-32-32l0-32-224 0-32 0L64 416l0 32c0 17.7-14.3 32-32 32s-32-14.3-32-32L0 64C0 46.3 14.3 32 32 32zm144 96a80 80 0 1 1 0 160 80 80 0 1 1 0-160z"/></svg>';

        $this->icon_returns = '<svg width="20px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M163.9 136.9c-29.4-29.8-29.4-78.2 0-108s77-29.8 106.4 0l17.7 18 17.7-18c29.4-29.8 77-29.8 106.4 0s29.4 78.2 0 108L310.5 240.1c-6.2 6.3-14.3 9.4-22.5 9.4s-16.3-3.1-22.5-9.4L163.9 136.9zM568.2 336.3c13.1 17.8 9.3 42.8-8.5 55.9L433.1 485.5c-23.4 17.2-51.6 26.5-80.7 26.5L192 512 32 512c-17.7 0-32-14.3-32-32l0-64c0-17.7 14.3-32 32-32l36.8 0 44.9-36c22.7-18.2 50.9-28 80-28l78.3 0 16 0 64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-64 0-16 0c-8.8 0-16 7.2-16 16s7.2 16 16 16l120.6 0 119.7-88.2c17.8-13.1 42.8-9.3 55.9 8.5zM193.6 384c0 0 0 0 0 0l-.9 0c.3 0 .6 0 .9 0z"/></svg>';
    }

    public function render_dashboard()
    {
        ob_start();
?>

        <section id="hdlDashboard">

            <div class="container">
                <div class="tabs">
                    <input type="radio" id="tab1" name="tab-control" checked />
                    <input type="radio" id="tab2" name="tab-control" />
                    <input type="radio" id="tab3" name="tab-control" />
                    <input type="radio" id="tab4" name="tab-control" />

                    <ul>
                        <li title="Features">
                            <label for="tab1" role="button">
                                <span><?php echo $this->icon_order_history ?></span>
                                <span>Order History</span>
                            </label>
                        </li>
                        <li title="Delivery Contents">
                            <label for="tab2" role="button">
                                <span><?php echo $this->icon_lab_history ?></span>
                                <span>Lab Orders</span>
                            </label>
                        </li>
                        <li title="Shipping">
                            <label for="tab3" role="button">
                                <span><?php echo $this->icon_patient_profile ?></span>
                                <span>Patient Profile</span>
                            </label>
                        </li>
                        <li title="Returns">
                            <label for="tab4" role="button">
                                <span><?php echo $this->icon_returns ?></span>
                                <span>Subscriptions</span>
                            </label>
                        </li>
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
                            <h2>Subscriptions</h2>
                            <?php include plugin_dir_path(__FILE__) . 'tabs/returns.php'; ?>
                        </section>
                    </div>
                </div>

            </div>
        </section>

<?php
        return ob_get_clean();
    }
}
