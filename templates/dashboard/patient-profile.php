<?php
// $user



$patient = HLD_Patient::get_patient_info();

$dob = new DateTime($patient['dob']);
$now = new DateTime();
$patient['age'] = $dob->diff($now)->y;

if (isset($_GET['message'])) {  ?>
    <div class="alert alert-success" role="alert">
        <?= esc_html($_GET['message']); ?>
    </div>
<?php } ?>

<div class="container my-5 profile-container">

    <div class="row g-4">
        <div class="col-md-12">

            <!-- Editable Account Details -->
            <h3>Account Details</h3>
            <div class="card mb-4 shadow-sm hld-personal-info">
                <div class="card-body row g-3 p-4">

                    <div class="col-md-9 hld-patient-info">
                        <form id="hld-account-details-form" class="hld-account-details-form">
                            <div class="mb-3">
                                <label for="hld_full_name" class="form-label"><strong>Name:</strong></label>
                                <p><?= !empty($patient['full_name']) ? esc_html($patient['full_name']) : '<em>Not provided</em>'; ?></p>
                                <input type="hidden" class="form-control" id="hld_full_name" name="full_name" value="<?= esc_attr($patient['full_name']); ?>">
                            </div>

                            <div class="mb-3">
                                <label for="hld_email" class="form-label"><strong>Email:</strong></label>
                                <p><?= esc_html($patient['email']); ?></p>
                                <input type="hidden" class="form-control" id="hld_email" name="email" value="<?= esc_attr($patient['email']); ?>">
                            </div>

                            <div class="mb-3">
                                <label for="hld_phone" class="form-label"><strong>Phone:</strong></label>
                                <p><?= !empty($patient['phone']) ? esc_html($patient['phone']) : '<em>Not provided</em>'; ?></p>
                                <input type="hidden" class="form-control" id="hld_phone" name="phone" value="<?= esc_attr($patient['phone']); ?>">
                            </div>

                            <div class="mb-3">
                                <label for="hld_dob" class="form-label"><strong>Date of birth:</strong></label>
                                <p><?= !empty($patient['dob']) ? esc_html($patient['dob']) : '<em>Not provided</em>'; ?></p>
                                <input type="hidden" class="form-control" id="hld_dob" name="dob" value="<?= esc_attr($patient['dob']); ?>">
                            </div>

                            <button style="display: none;" type="button" id="hld_save_account_details" class="btn btn-primary">Save</button>
                            <span id="hld_account_details_message" style="display:none; margin-left:15px;"></span>
                        </form>
                    </div>
                    <div class="col-md-3 hld-edit-wrap">
                        <button class="btn_payment_method btn_edit_settings hld_btn_edit_profile">Edit Profile</button>
                    </div>


                </div>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const saveBtn = document.getElementById('hld_save_account_details');
                    const form = document.getElementById('hld-account-details-form');
                    const msgSpan = document.getElementById('hld_account_details_message');
                    saveBtn.addEventListener('click', function() {
                        msgSpan.style.display = 'none';
                        saveBtn.disabled = true;
                        const data = {
                            full_name: form.full_name.value,
                            email: form.email.value,
                            phone: form.phone.value,
                            dob: form.dob.value
                        };
                        fetch('/wp-json/hld/v1/update-account-details', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-WP-Nonce': '<?php echo wp_create_nonce('wp_rest') ?>',
                                },
                                body: JSON.stringify(data)
                            })
                            .then(res => res.json())
                            .then(res => {
                                saveBtn.disabled = false;
                                msgSpan.style.display = 'inline-block';
                                if (res.success) {
                                    msgSpan.textContent = 'Account Details updated successfully.';
                                    msgSpan.style.color = 'green';
                                } else {
                                    msgSpan.textContent = res.message || 'Error updating details.';
                                    msgSpan.style.color = 'red';
                                }
                            })
                            .catch(() => {
                                saveBtn.disabled = false;
                                msgSpan.style.display = 'inline-block';
                                msgSpan.textContent = 'Error updating details.';
                                msgSpan.style.color = 'red';
                            });
                    });
                });
            </script>

            <h3>Payment Method</h3>
            <div class="card mb-4 shadow-sm hld-payment-info">
                <div class="card-body row row-cols-1 row-cols-md-1 g-3 p-4">
                    <div class="col-md-8">
                        <div><strong>Debit Card:</strong> <span class="fw-bold"><?php if (HLD_Payments::has_card()) {
                                                                                    echo "**** **** ****" . HLD_Payments::get_last4();
                                                                                } else {
                                                                                    echo "No payment method on file.";
                                                                                } ?></span></div>
                    </div>
                    <!-- <div class="col-md-4">
                        <button class="btn_payment_method btn_edit_settings">Add Payment Method</button>
                    </div> -->



                </div>
            </div>
            <h3>Shipping Address</h3>

            <div class="card mb-4 shadow-sm hld-shipping-info">
                <div class="card-body row row-cols-1 row-cols-md-1 g-3 p-4">
                    <div class="col-md-12">
                        <p>The medication will be shipped to the same address. If you have any questions, please contact our support team.</p>
                        <div><span class="fw-bold"><?php echo $patient['address']; ?> </span></div>
                    </div>

                </div>
            </div>

            <!-- <a href="" class="hld_btn_profile_logout">logout</a> -->
            <a href="<?= wp_logout_url(home_url('?message=User+logged+out')); ?>" class="hld_btn_profile_logout">logout</a>



        </div>
    </div>
</div>