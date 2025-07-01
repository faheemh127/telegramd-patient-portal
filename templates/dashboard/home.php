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

<div class="dashboard-wrapper">

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