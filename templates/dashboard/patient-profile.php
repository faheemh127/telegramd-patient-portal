<?php
// $user

$patient = [
    'full_name' => $user->data->user_nicename,
    'gender' => 'Male',
    'dob' => '1990-05-15',
    'email' => $user->data->user_email,
    'phone' => '+1 234-567-8900',
    'blood_group' => 'B+',
    'allergies' => 'Penicillin',
    'chronic_conditions' => 'Diabetes, Hypertension',
    'current_medications' => 'Metformin, Aspirin',
    'previous_surgeries' => 'Appendectomy (2012), Hernia Repair (2018)',
    'primary_physician' => 'Dr. Sarah Thompson',
    'address' => '123 Main St, New York, NY, USA',
    'emergency_contact' => 'Jane Doe - +1 234-555-7890',
];

$dob = new DateTime($patient['dob']);
$now = new DateTime();
$patient['age'] = $dob->diff($now)->y;
?>

<div class="container my-5 profile-container">

    <div class="row g-4">
        <div class="col-md-12">
            <!-- Basic Information -->
            <h3>Account Details</h3>
            <div class="card mb-4 shadow-sm">
                <div class="card-body row row-cols-1 row-cols-md-1 g-3 p-4">

                    <!-- Name row with Edit button -->
                    <div class="d-flex justify-content-between align-items-center">
                        <div><strong>Name:</strong> <span class="fw-bold"><?= $patient['full_name']; ?></span></div>
                        <a href="#" class=" btn_edit_settings">Edit</a>
                    </div>

                    <div><strong>Email:</strong> <span class="fw-bold"><?= $patient['email']; ?></span></div>
                    <div><strong>Phone:</strong> <span class="fw-bold"><?= $patient['phone']; ?></span></div>
                    <div><strong>Birthday:</strong> <span class="fw-bold"><?= $patient['dob']; ?></span></div>
                </div>
            </div>

            <h3>Payment Method</h3>
            <div class="card mb-4 shadow-sm">
                <div class="card-body row row-cols-1 row-cols-md-1 g-3 p-4">
                    <div class="d-flex justify-content-between">
                        <div><strong>Debit Card:</strong> <span class="fw-bold">**** **** **** 0000</span></div>
                        <button class="btn_payment_method btn_edit_settings">Add Payment Method</button>
                    </div>


                </div>
            </div>
            <h3>Shipping Address</h3>

            <div class="card mb-4 shadow-sm">
                <div class="card-body row row-cols-1 row-cols-md-1 g-3 p-4">
                    <div><strong>Orders:</strong> <span class="fw-bold">Need to change the address of an order that's in-progress? </span> <a href="#">Contact customer support</a></div>
                    <div class="text-secondary">subscriptions</div>
                    <div class="text-danger">Update your shipping address in your <a href="">subscriptions page</a></div>

                </div>
            </div>

            <a href="" class="hld_btn_profile_logout">logout</a>

            <!-- Medical Information -->
            <!-- <div class="card mb-4 shadow-sm">
                <div class="card-header text-primary fw-bold">Medical Information</div>
                <div class="card-body row row-cols-1 row-cols-md-2 g-3">
                    <div><strong>Blood Group:</strong> <?= $patient['blood_group']; ?></div>
                    <div><strong>Allergies:</strong> <?= $patient['allergies']; ?></div>
                    <div><strong>Chronic Conditions:</strong> <?= $patient['chronic_conditions']; ?></div>
                    <div><strong>Current Medications:</strong> <?= $patient['current_medications']; ?></div>
                    <div><strong>Previous Surgeries:</strong> <?= $patient['previous_surgeries']; ?></div>
                    <div><strong>Primary Physician:</strong> <?= $patient['primary_physician']; ?></div>
                </div>
            </div> -->

            <!-- Emergency & Contact -->
            <!-- <div class="card shadow-sm">
                <div class="card-header text-primary fw-bold">Emergency & Contact</div>
                <div class="card-body row row-cols-1 row-cols-md-2 g-3">
                    <div><strong>Address:</strong> <?= $patient['address']; ?></div>
                    <div><strong>Emergency Contact:</strong> <?= $patient['emergency_contact']; ?></div>
                </div>
            </div> -->
        </div>
    </div>
</div>