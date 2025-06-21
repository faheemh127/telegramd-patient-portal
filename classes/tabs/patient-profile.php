<?php
// Dummy data for placeholder
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

// Calculate age
$dob = new DateTime($patient['dob']);
$now = new DateTime();
$patient['age'] = $dob->diff($now)->y;
?>

<div class="profile-container">
    <h1>Patient Profile</h1>

    <div class="profile-grid">
        <div class="profile-section">
            <h2>Basic Information</h2>
            <ul>
                <li><strong>Name:</strong> <?= $patient['full_name']; ?></li>
                <li><strong>Gender:</strong> <?= $patient['gender']; ?></li>
                <li><strong>Date of Birth:</strong> <?= $patient['dob']; ?> (<?= $patient['age']; ?> years)</li>
                <li><strong>Email:</strong> <?= $patient['email']; ?></li>
                <li><strong>Phone:</strong> <?= $patient['phone']; ?></li>
            </ul>
        </div>

        <div class="profile-section">
            <h2>Medical Information</h2>
            <ul>
                <li><strong>Blood Group:</strong> <?= $patient['blood_group']; ?></li>
                <li><strong>Allergies:</strong> <?= $patient['allergies']; ?></li>
                <li><strong>Chronic Conditions:</strong> <?= $patient['chronic_conditions']; ?></li>
                <li><strong>Current Medications:</strong> <?= $patient['current_medications']; ?></li>
                <li><strong>Previous Surgeries:</strong> <?= $patient['previous_surgeries']; ?></li>
                <li><strong>Primary Physician:</strong> <?= $patient['primary_physician']; ?></li>
            </ul>
        </div>

        <div class="profile-section">
            <h2>Emergency & Contact</h2>
            <ul>
                <li><strong>Address:</strong> <?= $patient['address']; ?></li>
                <li><strong>Emergency Contact:</strong> <?= $patient['emergency_contact']; ?></li>
            </ul>
        </div>
    </div>
</div>