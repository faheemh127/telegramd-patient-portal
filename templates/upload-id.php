<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h4 class="card-title mb-3 text-center">Upload Your ID</h4>
                    <p class="text-muted text-center mb-4">
                        Please provide a valid government-issued ID to verify your identity.
                    </p>

                    <form action="upload-handler.php" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>

                        <!-- Patient Name -->
                        <div class="mb-3">
                            <label for="patientName" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="patientName" name="patient_name" required>
                            <div class="invalid-feedback">
                                Please enter your full name.
                            </div>
                        </div>

                        <!-- Upload ID -->
                        <div class="mb-3">
                            <label for="patientID" class="form-label">Upload ID Document</label>
                            <input class="form-control" type="file" id="patientID" name="patient_id" accept=".jpg,.jpeg,.png,.pdf" required>
                            <div class="form-text">
                                Accepted formats: JPG, PNG, or PDF. Max size: 5MB.
                            </div>
                            <div class="invalid-feedback">
                                Please upload a valid ID document.
                            </div>
                        </div>

                        <!-- Submit -->
                        <button type="submit" class="btn btn-primary w-100 hld_color_primary">Submit</button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>