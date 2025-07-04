<?php
$icon_capsule_tablet = '<svg width="35px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512">
                        <path d="M112 96c-26.5 0-48 21.5-48 48l0 112 96 0 0-112c0-26.5-21.5-48-48-48zM0 144C0 82.1 50.1 32 112 32s112 50.1 112 112l0 224c0 61.9-50.1 112-112 112S0 429.9 0 368L0 144zM554.9 399.4c-7.1 12.3-23.7 13.1-33.8 3.1L333.5 214.9c-10-10-9.3-26.7 3.1-33.8C360 167.7 387.1 160 416 160c88.4 0 160 71.6 160 160c0 28.9-7.7 56-21.1 79.4zm-59.5 59.5C472 472.3 444.9 480 416 480c-88.4 0-160-71.6-160-160c0-28.9 7.7-56 21.1-79.4c7.1-12.3 23.7-13.1 33.8-3.1L498.5 425.1c10 10 9.3 26.7-3.1 33.8z" />
                    </svg>';

$icon_tablets = '<svg width="12px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512">
                                    <path d="M614.3 247c-5.2 7.9-16.2 8.5-22.9 1.8L391.2 48.6c-6.7-6.7-6.2-17.8 1.8-22.9C418.1 9.4 447.9 0 480 0c88.4 0 160 71.6 160 160c0 32.1-9.4 61.9-25.7 87zM567 294.3c-25 16.3-54.9 25.7-87 25.7c-88.4 0-160-71.6-160-160c0-32.1 9.4-61.9 25.7-87c5.2-7.9 16.2-8.5 22.9-1.8L568.8 271.4c6.7 6.7 6.2 17.8-1.8 22.9zM301.5 368c9.5 0 16.9 8.2 15 17.5C301.1 457.8 236.9 512 160 512S18.9 457.8 3.5 385.5c-2-9.3 5.5-17.5 15-17.5l283.1 0zm0-32L18.5 336c-9.5 0-16.9-8.2-15-17.5C18.9 246.2 83.1 192 160 192s141.1 54.2 156.5 126.5c2 9.3-5.5 17.5-15 17.5z" />
                                </svg>';

$icon_calendar = '<svg width="12px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                                    <path d="M152 24c0-13.3-10.7-24-24-24s-24 10.7-24 24l0 40L64 64C28.7 64 0 92.7 0 128l0 16 0 48L0 448c0 35.3 28.7 64 64 64l320 0c35.3 0 64-28.7 64-64l0-256 0-48 0-16c0-35.3-28.7-64-64-64l-40 0 0-40c0-13.3-10.7-24-24-24s-24 10.7-24 24l0 40L152 64l0-40zM48 192l352 0 0 256c0 8.8-7.2 16-16 16L64 464c-8.8 0-16-7.2-16-16l0-256z" />
                                </svg>';

$icon_file = '<svg width="12px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512">
                                    <path d="M320 464c8.8 0 16-7.2 16-16l0-288-80 0c-17.7 0-32-14.3-32-32l0-80L64 48c-8.8 0-16 7.2-16 16l0 384c0 8.8 7.2 16 16 16l256 0zM0 64C0 28.7 28.7 0 64 0L229.5 0c17 0 33.3 6.7 45.3 18.7l90.5 90.5c12 12 18.7 28.3 18.7 45.3L384 448c0 35.3-28.7 64-64 64L64 512c-35.3 0-64-28.7-64-64L0 64z" />
                                </svg>';


?>

<div class="container py-5 hld-orders">
    <!-- <h2 class="mb-4 fw-bold text-dark">🧾 Patient Order History</h2> -->

    <?php for ($i = 0; $i <= 7; $i++) { ?>
        <div class="card mb-3 shadow-sm p-4 hld-order-item">
            <div class="row align-items-center">
                <!-- Left Section -->
                <div class="col-md-8 d-flex align-items-center gap-3">
                    <div class="icon-wrap bg-light rounded-circle p-3 d-flex align-items-center justify-content-center">
                        <?php echo $icon_capsule_tablet; ?>
                    </div>
                    <div class="desc-wrap">
                        <h5 class="fw-bold text-primary">B12 INJECTION</h5>
                        <div class="d-flex flex-wrap gap-3 small text-muted">
                            <div class="d-flex align-items-center gap-1">
                                <?php echo $icon_tablets ?>
                                10 Tablets
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                <?php echo $icon_calendar ?>
                                24 May 2025
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                <?php echo $icon_file ?>
                                LTV Test
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Section -->
                <div class="col-md-4 btn-group-wrapper text-md-end mt-3 mt-md-0 d-flex flex-md-row justify-content-center gap-2 ">
                    <!-- <a href="#"> -->
                    <button class="btn btn-outline-primary">View Detail</button>
                    <button class="btn btn-primary">Completed</button>
                    <!-- </a> -->
                </div>

            </div>
        </div>
    <?php } ?>
</div>