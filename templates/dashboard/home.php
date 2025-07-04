<?php
// File: templates/dashboard/home.php

defined('ABSPATH') || exit;
?>
<?php
/**
<div class="dashboard-wrapper">

    <!-- Cards Section -->
    <div class="card-wrapper">
        <div class="card">
            <h3>Total Orders</h3>
            <div class="value">1,245</div>
        </div>
        <div class="card">
            <h3>Lab Requests</h3>
            <div class="value">327</div>
        </div>
        <div class="card">
            <h3>Patients</h3>
            <div class="value">789</div>
        </div>
        <div class="card">
            <h3>Returns</h3>
            <div class="value">15</div>
        </div>
    </div>

    <!-- Table Section -->
    <div class="table-wrapper">
        <h4>Recent Activities</h4>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Action</th>
                    <th>User</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>2025-07-01</td>
                    <td>Order #2345 placed</td>
                    <td>Ali Raza</td>
                    <td class="status success">Completed</td>
                </tr>
                <tr>
                    <td>2025-06-30</td>
                    <td>Lab request submitted</td>
                    <td>Fatima</td>
                    <td class="status pending">Pending</td>
                </tr>
                <tr>
                    <td>2025-06-29</td>
                    <td>Return initiated</td>
                    <td>Usman</td>
                    <td class="status failed">Rejected</td>
                </tr>
            </tbody>
        </table>
    </div>

</div>
 */
?>

<div class="dashboard-wrapper container">


    <p>Healsend Health</p>
    <h1>Welcome, Vineeth</h1>
    <p>This is your personal dashboard on Healsend. Here, you'll find all the information regarding your medical consultations.</p>
    <!-- Cards Section -->
    <div class="card-wrapper">
        <div class="card">
            <div class="info">
                <h3>Total Orders</h3>
                <div class="value">1,245</div>
            </div>
            <div class="icon">
                <!-- Shopping Cart Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-cart" viewBox="0 0 16 16">
                    <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L1.01 2H.5a.5.5 0 0 1-.5-.5z" />
                    <path d="M5.5 12a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm9 0a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z" />
                </svg>
            </div>
        </div>

        <div class="card">
            <div class="info">
                <h3>Lab Requests</h3>
                <div class="value">327</div>
            </div>
            <div class="icon">
                <!-- Microscope Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-eyeglasses" viewBox="0 0 16 16">
                    <path d="M5.5 2a.5.5 0 0 1 .5.5V3h4v-.5a.5.5 0 0 1 1 0V3a2 2 0 0 1 2 2v5a2 2 0 1 1-4 0V5H7v5a2 2 0 1 1-4 0V5a2 2 0 0 1 2-2v-.5a.5.5 0 0 1 .5-.5z" />
                </svg>
            </div>
        </div>

        <div class="card">
            <div class="info">
                <h3>Patients</h3>
                <div class="value">789</div>
            </div>
            <div class="icon">
                <!-- Person Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-person" viewBox="0 0 16 16">
                    <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6z" />
                    <path d="M2 14s-1 0-1-1 1-4 7-4 7 3 7 4-1 1-1 1H2z" />
                </svg>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card-wrapper">
                <div class="card">
                    <div class="hld-img-wrap">
                        <img src="" alt="">
                        <p>Patient Portal</p>
                        <p>Access and manage your medical records securely, communicate with healthcare providers, and conveniently view test results — providing you with a comprehensive and streamlined healthcare experience.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6"></div>
    </div>


    <!-- Table Section -->
    <div class="table-wrapper">
        <h4>Notifications</h4>
        <table class="table">
            <tbody>

                <!-- Notification Row 1 -->
                <tr class="align-middle">
                    <td style="width: 30px;">
                        <span class="d-inline-block rounded-circle text-warning bg-warning" style="width: 15px; height: 15px;"></span>
                    </td>
                    <td>
                        <div>
                            <strong>Appointment Reminder</strong>
                            <div class="text-muted small">You have an upcoming appointment scheduled for July 5th.</div>
                        </div>
                    </td>
                    <td class="text-end" style="white-space: nowrap;">
                        <div>
                            <button class="btn btn-sm btn-outline-secondary border-0 p-0">
                                <i class="bi bi-x-lg"></i> <!-- Bootstrap Icons (optional) -->
                            </button>
                            <div class="text-muted small">3 days ago</div>
                        </div>
                    </td>
                </tr>

                <!-- Notification Row 2 -->
                <tr class="align-middle">
                    <td style="width: 30px;">
                        <span class="d-inline-block rounded-circle text-warning bg-warning" style="width: 15px; height: 15px;"></span>
                    </td>
                    <td>
                        <div>
                            <strong>Test Results Ready</strong>
                            <div class="text-muted small">Your recent blood test results are now available.</div>
                        </div>
                    </td>
                    <td class="text-end" style="white-space: nowrap;">
                        <div>
                            <button class="btn btn-sm btn-outline-secondary border-0 p-0">
                                <i class="bi bi-x-lg"></i>
                            </button>
                            <div class="text-muted small">1 week ago</div>
                        </div>
                    </td>
                </tr>

            </tbody>
        </table>
    </div>


    <h4>Recent Consultations</h4>
    <?php for ($i = 0; $i <= 7; $i++) { ?>
        <div class="card mb-3 shadow-sm">
            <div class="card-body row hld-lab-order">
                <!-- Left Section -->
                <div class="col-md-9 d-flex flex-column flex-md-row align-items-center align-items-md-start text-center text-md-start gap-3">
                    <!-- Icon -->
                    <div class="bg-light rounded-circle p-3 fs-2">
                        <svg width="25px" class="hld_color_primary" color="fillColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                            <path d="M288 0L160 0 128 0C110.3 0 96 14.3 96 32s14.3 32 32 32l0 132.8c0 11.8-3.3 23.5-9.5 33.5L10.3 406.2C3.6 417.2 0 429.7 0 442.6C0 480.9 31.1 512 69.4 512l309.2 0c38.3 0 69.4-31.1 69.4-69.4c0-12.8-3.6-25.4-10.3-36.4L329.5 230.4c-6.2-10.1-9.5-21.7-9.5-33.5L320 64c17.7 0 32-14.3 32-32s-14.3-32-32-32L288 0zM192 196.8L192 64l64 0 0 132.8c0 23.7 6.6 46.9 19 67.1L309.5 320l-171 0L173 263.9c12.4-20.2 19-43.4 19-67.1z" />
                        </svg>
                    </div>

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
                <div class="col-md-3 text-center text-md-end mt-3 mt-md-0 hld_btn_view_detail_wrap">
                    <button class="btn btn-primary w-md-auto hld_btn_view_detail">View Detail</button>
                </div>
            </div>
        </div>

    <?php } ?>



</div>