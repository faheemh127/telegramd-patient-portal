<div class="container d-flex justify-content-center mt-5">
    <div class="card shadow-sm rounded-3 w-100" style="max-width: 700px; border-radius: 20px;
    overflow: hidden;">
        <div class="card-body p-4">
            <h5 class="card-title text-center mb-4">Upload Patient ID</h5>

            <form id="idUploadForm" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="patientName" class="form-label">Full Name</label>
                    <input
                        type="text"
                        class="form-control"
                        id="patientName"
                        name="patient_name"
                        required>
                </div>

                <div class="mb-3">
                    <label for="patientID" class="form-label">Upload ID Document</label>
                    <input
                        class="form-control"
                        type="file"
                        id="patientID"
                        name="patient_id"
                        accept=".jpg,.jpeg,.png,.pdf"
                        required>
                </div>

                <button type="submit" class="btn btn-primary w-100" style="background-color: #7b68ee; border-radius: 50px; border: none;">
                    Submit
                </button>
            </form>
        </div>
    </div>
</div>
<script>
    jQuery(document).ready(function($) {
        $("#idUploadForm").on("submit", function(e) {
            e.preventDefault();

            let form = $(this);
            let button = form.find("button[type=submit]");
            let formData = new FormData(this);
            formData.append("action", "id_upload"); // WP AJAX action name

            $.ajax({
                url: "<?php echo admin_url('admin-ajax.php'); ?>",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    console.log("Uploading...");
                    // Disable button and show processing text
                    button.prop("disabled", true).text("Processing...");
                },
                success: function(response) {
                    console.log(response);
                    alert("Your file has been uploaded successfully!");
                },
                error: function(err) {
                    console.error(err);
                    alert("Upload failed!");
                },
                complete: function() {
                    // Re-enable button after request finishes
                    button.prop("disabled", false).text("Submit");
                }
            });
        });
    });
</script>