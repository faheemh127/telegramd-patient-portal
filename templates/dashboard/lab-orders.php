<div class="container py-4 hld_lab_order_container">
    <!-- <h1 class="hdl_tab_heading">Patient Order History</h1> -->

    <?php for ($i = 0; $i <= 7; $i++) { ?>
        <div class="card mb-3 shadow-sm">
            <div class="card-body row hld-lab-order">
                <!-- Left Section -->
                <div class="col-md-9 d-flex flex-column flex-md-row align-items-center align-items-center text-center text-md-start gap-3">
                    <!-- Icon -->
                    <div class="bg-light rounded-circle p-3 fs-2 lab-icon-wrap">
                        <svg width="25px" class="hld_color_primary" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                            <path fill="currentColor" d="M288 0L160 0 128 0C110.3 0 96 14.3 96 32s14.3 32 32 32l0 132.8c0 11.8-3.3 23.5-9.5 33.5L10.3 406.2C3.6 417.2 0 429.7 0 442.6C0 480.9 31.1 512 69.4 512l309.2 0c38.3 0 69.4-31.1 69.4-69.4c0-12.8-3.6-25.4-10.3-36.4L329.5 230.4c-6.2-10.1-9.5-21.7-9.5-33.5L320 64c17.7 0 32-14.3 32-32s-14.3-32-32-32L288 0zM192 196.8L192 64l64 0 0 132.8c0 23.7 6.6 46.9 19 67.1L309.5 320l-171 0L173 263.9c12.4-20.2 19-43.4 19-67.1z" />
                        </svg>
                    </div>

                    <!-- Text Block -->
                    <div class="w-100">
                        <div>
                            <h5 class="mb-1">
                                Lab Order #<?php echo '776875' ?>
                            </h5>

                        </div>
                        <div class="d-flex flex-column flex-md-row gap-2 small text-muted">
                            <div>Order # <?php echo '285-C' ?></div>
                            <div>25 Aug 2025</div>
                        </div>
                    </div>
                </div>

                <!-- Right Section -->
                <div class="col-md-3 text-center text-md-end mt-3 mt-md-0 hld_btn_view_detail_wrap ">
                    <button class="btn btn-primary w-md-auto hld_btn_view_detail">View Detail</button>
                </div>
            </div>
        </div>

    <?php } ?>
</div>