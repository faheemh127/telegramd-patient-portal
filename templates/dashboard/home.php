<?php
// File: templates/dashboard/home.php

defined('ABSPATH') || exit;

// $custom_logo_id = get_theme_mod('custom_logo');
// $logo_url = wp_get_attachment_image_url($custom_logo_id, 'full');

$logo_url = 'https://healsend.com/wp-content/uploads/2025/05/HealSend__1_-removebg-preview.png';

$logo_html = $logo_url
    ? sprintf('<img src="%s" alt="Site Logo" style="width: 120px">', esc_url($logo_url))
    : 'Healsend.com';

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


    <p class="hld_small_title">Healsend</p>
    <h1 class="hld_dashboard_title">Welcome, <span class="first-letter-cap"><?php echo $user->data->display_name ?></span></h1>
    <p class="hld_dashboard_subtitle">This is your personal dashboard on Healsend. Here, you'll find all the information regarding your medical consultations.</p>
    <!-- Cards Section -->
    <div class="card-wrapper">
        <div class="card">
            <div class="info">
                <h3>Total Orders</h3>
                <?php
                $orders = HLD_UserOrders::get_orders($user_id);
                $order_count = is_array($orders) ? count($orders) : 0;
                ?>
                <div class="value"><?php echo $order_count; ?></div>
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
                <div class="value">0</div>
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
                <h3>Profile</h3>
                <div class="value" onclick="seeProfile()">See Profile</div>
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

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card-wrapper h-100">
                <div class="card">
                    <div class="hld_content">
                        <div class="hld-img-wrap">
                            <img src="https://www.shutterstock.com/image-photo/brothers-friends-portrait-three-young-260nw-2275006135.jpg" alt="">

                        </div>
                        <p class="hld_title">Patient Portal</p>
                        <p>Access and manage your medical records securely, communicate with healthcare providers, and conveniently view test results — providing you with a comprehensive and streamlined healthcare experience.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 hld_home_notifications">
            <div class="table-wrapper h-100">
                <h4>Notifications</h4>
                <table class="table">
                    <tbody>








                        <?php
                        if (is_user_logged_in()) {
                            $user_id = get_current_user_id();
                            $notifications = HLD_UserNotifications::get_notifications($user_id);

                            if (!empty($notifications)) {
                                foreach ($notifications as $notification) {
                                    $title = esc_html($notification['title']);
                                    $message = esc_html($notification['message']);
                                    $time = human_time_diff(strtotime($notification['date']), current_time('timestamp')) . ' ago';
                        ?>

                                    <tr class="align-middle">
                                        <td style="width: 30px;">
                                            <span class="d-inline-block rounded-circle text-warning bg-warning" style="width: 15px; height: 15px;"></span>
                                        </td>
                                        <td>
                                            <div>
                                                <strong><?php echo $title; ?></strong>
                                                <div class="text-muted small"><?php echo $message; ?></div>
                                            </div>
                                        </td>
                                        <td class="text-end" style="white-space: nowrap;">
                                            <div>
                                                <button class="btn btn-sm btn-outline-secondary border-0 p-0">
                                                    <i class="bi bi-x-lg"></i>
                                                </button>
                                                <div class="text-muted small"><?php echo $time; ?></div>
                                            </div>
                                        </td>
                                    </tr>

                        <?php
                                }
                            } else {
                                echo '<tr><td colspan="3" class="text-muted text-center">No notifications found.</td></tr>';
                            }
                        } else {
                            echo '<tr><td colspan="3" class="text-muted text-center">Please log in to view notifications.</td></tr>';
                        }
                        ?>





                        <!-- Notification Row 2 -->
                        <!-- <tr class="align-middle">
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
                        </tr> -->

                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <!-- Table Section -->



    <!-- <h4>Recent Consultations</h4> -->
    <div class="table-wrapper hdl_recent_consultations_home">
        <h4>Recent Consultations</h4>
        <table class="table align-middle">
            <tbody>

                <!-- Row 1 -->
                <tr>
                    <!-- Profile image -->
                    <td style="width: 60px;" class="hld_td">
                        <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Doctor" class="rounded-circle" width="50" height="50">
                    </td>

                    <!-- Info -->
                    <td>
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <strong>Dr. Jonathan Clark</strong>
                                <span class="badge bg-success ms-2">Completed</span>
                            </div>
                        </div>
                        <div class="text-muted small mt-1">Order #54321 &nbsp; | &nbsp; 2025-06-12</div>
                        <div class="text-muted small">Medicine Date: 2025-06-14</div>
                    </td>

                    <!-- Brand -->
                    <td class="text-end">
                        <div class="fw-semibold fs-5"><?php echo $logo_html ?></div>
                    </td>
                </tr>

                <!-- Row 2 -->
                <tr>
                    <td>
                        <img src="https://randomuser.me/api/portraits/women/45.jpg" alt="Doctor" class="rounded-circle" width="50" height="50">
                    </td>
                    <td>
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <strong>Dr. Sarah Mitchell</strong>
                                <span class="badge bg-success ms-2">Completed</span>
                            </div>
                        </div>
                        <div class="text-muted small mt-1">Order #98765 &nbsp; | &nbsp; 2025-06-18</div>
                        <div class="text-muted small">Medicine Date: 2025-06-20</div>
                    </td>
                    <td class="text-end">
                        <div class="fw-semibold fs-5"><?php echo $logo_html ?></div>
                    </td>
                </tr>

                <!-- Row 3 -->
                <tr>
                    <td>
                        <img src="https://randomuser.me/api/portraits/men/76.jpg" alt="Doctor" class="rounded-circle" width="50" height="50">
                    </td>
                    <td>
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <strong>Dr. Kevin Diaz</strong>
                                <span class="badge bg-success ms-2">Completed</span>
                            </div>
                        </div>
                        <div class="text-muted small mt-1">Order #13579 &nbsp; | &nbsp; 2025-06-25</div>
                        <div class="text-muted small">Medicine Date: 2025-06-27</div>
                    </td>
                    <td class="text-end">
                        <div class="fw-semibold fs-5"><?php echo $logo_html ?></div>
                    </td>
                </tr>

            </tbody>
        </table>
    </div>




</div>


<script>
    function seeProfile() {
        const tabAction = document.querySelector('label[for="tab3"]'); // Just one
        if (tabAction) tabAction.click()
        else {
            console.log("Tab does not exist")
            console.log(tabAction);

        };
    }
</script>