<?php
$patient = [
    'full_name' => 'John Doe',
    'gender' => 'Male',
    'dob' => '1990-05-15',
    'email' => 'john@example.com',
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

<div class="profile-container">

    <h1>🩺 Patient Profile</h1>

    <!-- Profile Image -->
    <div class="profile-image">
        <img src="https://cindykophotography.com/wp-content/uploads/49946859_605163983238131_143987782165987328_o-1.jpg" alt="Profile Picture" />
    </div>

    <div class="flex-wrapper">

        <!-- Profile Info -->
        <div class="profile-details">


            <!-- Basic Info -->
            <div class="section-card">
                <h2>Basic Information</h2>
                <div class="info-grid">
                    <div><strong>Name:</strong> <?= $patient['full_name']; ?></div>
                    <div><strong>Gender:</strong> <?= $patient['gender']; ?></div>
                    <div><strong>Date of Birth:</strong> <?= $patient['dob']; ?></div>
                    <div><strong>Age:</strong> <?= $patient['age']; ?> years</div>
                    <div><strong>Email:</strong> <?= $patient['email']; ?></div>
                    <div><strong>Phone:</strong> <?= $patient['phone']; ?></div>
                </div>
            </div>

            <!-- Medical Info -->
            <div class="section-card">
                <h2>Medical Information</h2>
                <div class="info-grid">
                    <div><strong>Blood Group:</strong> <?= $patient['blood_group']; ?></div>
                    <div><strong>Allergies:</strong> <?= $patient['allergies']; ?></div>
                    <div><strong>Chronic Conditions:</strong> <?= $patient['chronic_conditions']; ?></div>
                    <div><strong>Current Medications:</strong> <?= $patient['current_medications']; ?></div>
                    <div><strong>Previous Surgeries:</strong> <?= $patient['previous_surgeries']; ?></div>
                    <div><strong>Primary Physician:</strong> <?= $patient['primary_physician']; ?></div>
                </div>
            </div>

            <!-- Contact Info -->
            <div class="section-card">
                <h2>Emergency & Contact</h2>
                <div class="info-grid">
                    <div><strong>Address:</strong> <?= $patient['address']; ?></div>
                    <div><strong>Emergency Contact:</strong> <?= $patient['emergency_contact']; ?></div>
                </div>
            </div>
        </div>
    </div>
</div>