<div class="container py-4">
    <h1 class="mb-4">Patient Order History</h1>

    <?php for ($i = 0; $i <= 7; $i++) { ?>
        <div class="card mb-3 shadow-sm">
            <div class="card-body row hld-lab-order">
                <!-- Left Section -->
                <div class="col-md-9 d-flex flex-column flex-md-row align-items-center align-items-md-start text-center text-md-start gap-3">
                    <!-- Icon -->
                    <div class="bg-light rounded-circle p-3 fs-2">🧪</div>

                    <!-- Text Block -->
                    <div class="w-100">
                        <div>
                            <h5 class="mb-1">
                                Lab Order #<?php echo '776875' ?>
                            </h5>
                            <span class="badge bg-warning text-dark mb-2 d-inline-block">status</span>
                        </div>
                        <div class="d-flex flex-column flex-md-row gap-2 small text-muted">
                            <div>Order # <?php echo 'aenn230A:: POASDNF-ASLEKNF-ASLENFB' ?></div>
                            <div class="d-flex align-items-center gap-1">
                                <svg width="12px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512">
                                    <path d="M320 464c8.8 0 16-7.2 16-16l0-288-80 0c-17.7 0-32-14.3-32-32l0-80L64 48c-8.8 0-16 7.2-16 16l0 384c0 8.8 7.2 16 16 16l256 0zM0 64C0 28.7 28.7 0 64 0L229.5 0c17 0 33.3 6.7 45.3 18.7l90.5 90.5c12 12 18.7 28.3 18.7 45.3L384 448c0 35.3-28.7 64-64 64L64 512c-35.3 0-64-28.7-64-64L0 64z" />
                                </svg>
                                24 May 2025
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Section -->
                <div class="col-md-3 text-center text-md-end mt-3 mt-md-0">
                    <button class="btn btn-outline-primary w-100 w-md-auto">View Detail</button>
                </div>
            </div>
        </div>

    <?php } ?>
</div>