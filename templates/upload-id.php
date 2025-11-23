<div class="container d-flex flex-column justify-content-center  mt-5 hld-upload-id">
    <p class="hld-heading">As required by law, you must upload a form of personal identification. This can be a driver's license, a state-issued ID, or a passport.</p>
    <div class="card shadow-sm rounded-3 w-100" style="max-width: 700px; border-radius: 20px;
    overflow: hidden;">
        <div class="card-body p-4">
            <form id="idUploadForm" enctype="multipart/form-data">
                <!-- <div class="mb-3">
                    <label for="patientName" class="form-label">Full Name</label>
                    <input
                        type="text"
                        class="form-control"
                        id="patientName"
                        name="patient_name"
                        required>
                </div> -->

                <div class="mb-3">
                    <label for="patientID" class="form-label">Upload ID Document</label>
                    <input
                        class="form-control"
                        type="file"
                        id="patientID"
                        name="patient_id"
                        accept=".jpg,.jpeg,.png,.pdf"
                        required>


                    <?php
                    // Safely get telegra_order_id from URL
                    $telegra_order_id = isset($_GET['telegra_order_id']) ? sanitize_text_field($_GET['telegra_order_id']) : '';
                    ?>

                    <!-- Hidden input field -->
                    <input
                        type="hidden"
                        id="telegraOrderID"
                        name="telegra_order_id"
                        value="<?php echo esc_attr($telegra_order_id); ?>">

                </div>

                <button type="submit" class="btn btn-primary w-100" style="background-color: #7b68ee; border-radius: 50px; border: none;">
                    Upload Document
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
                    if (response.data && response.data.patient_dashboard_url) {
                        window.location.href = response.data.patient_dashboard_url;
                    } else {
                        console.warn("No redirect URL returned from server.");
                    }


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