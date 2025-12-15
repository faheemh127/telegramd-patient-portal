<style>
    .canvas-sig {
        width: 100%;
        height: 400px;
        border: 1px solid rgba(0, 0, 0, 0.2);
        margin: 10px 0;
        /* 🔑 Prevent page scrolling while drawing */
        touch-action: none;
    }

    .hld-consent .agreement-text {
        max-height: 150px;
        overflow-y: scroll;
        margin: 10px 0;
    }

    .btns-wrap {
        display: flex;
        gap: 10px;
    }

    .agreement-text {
        font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI",
            Roboto, Oxygen, Ubuntu, Cantarell, "Open Sans", "Helvetica Neue", sans-serif;
        font-size: 14px;
        line-height: 1.7;
        color: #374151;
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 20px;
        max-width: 900px;
    }

    .agreement-text h3,
    .agreement-text h4 {
        color: #111827;
        margin-top: 24px;
        margin-bottom: 10px;
    }

    .agreement-text h3 {
        font-size: 18px;
    }

    .agreement-text h4 {
        font-size: 16px;
    }

    .agreement-text p {
        margin-bottom: 12px;
    }

    .agreement-text ul,
    .agreement-text ol {
        margin: 12px 0 16px 20px;
        padding-left: 10px;
    }

    .agreement-text li {
        margin-bottom: 8px;
    }

    .agreement-text ul li::marker {
        color: #2563eb;
    }

    .agreement-text ol li::marker {
        font-weight: 600;
        color: #2563eb;
    }
</style>
<div class="container d-flex flex-column justify-content-center  mt-5 hld-upload-id hld-consent">

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
                    <h2 style="margin-bottom: 0;">Agreement</h2>
                    <h3 style="    font-size: 20px;
    font-weight: 400;
    margin-top: 5px;">Informed Consent of Services Performed.</h3>
                    <div class="agreement-text">
                        <h3>Telemedicine Information</h3>

                        <p>
                            Telemedicine involves the use of electronic communications to enable healthcare providers
                            at different locations to share individual patient medical information for the purpose
                            of improving patient care. Providers may include primary care practitioners, specialists,
                            and/or subspecialists.
                        </p>

                        <p>
                            The information may be used for diagnosis, therapy, follow-up and/or education, and may
                            include any of the following:
                        </p>

                        <ul>
                            <li>Patient medical records</li>
                            <li>Medical images</li>
                            <li>Live two-way messaging, audio, and video</li>
                            <li>Output data from medical devices and sound and video files</li>
                        </ul>

                        <p>
                            Electronic systems used will incorporate network and software security protocols to protect
                            the confidentiality of patient identification and imaging data and will include measures
                            to safeguard the data and ensure its integrity.
                        </p>

                        <p>
                            Responsibility for patient care remains with the patient’s local clinician, if applicable,
                            as does the patient’s medical record.
                        </p>

                        <h4>Expected Benefits</h4>

                        <ul>
                            <li>
                                Improved access to medical care by allowing the patient to remain at their local site
                                (e.g., home) while the physician consults from a distant location.
                            </li>
                            <li>More efficient medical evaluation and management.</li>
                            <li>Access to specialist expertise.</li>
                        </ul>

                        <h4>Possible Risks</h4>

                        <p>
                            As with any medical procedure, there are potential risks associated with telemedicine,
                            including but not limited to:
                        </p>

                        <ul>
                            <li>
                                In rare cases, the transmitted information may be of inadequate quality, requiring a
                                face-to-face visit or rescheduled consultation.
                            </li>
                            <li>
                                Delays in medical evaluation and treatment due to equipment deficiencies or failures.
                            </li>
                            <li>
                                Very rare breaches of privacy due to security protocol failures.
                            </li>
                            <li>
                                In rare cases, incomplete medical records may result in adverse drug interactions,
                                allergic reactions, or judgment errors.
                            </li>
                        </ul>

                        <h4>Informed Consent Acknowledgment</h4>

                        <p>By checking the box associated with <strong>“Informed Consent”</strong>, you acknowledge:</p>

                        <ol>
                            <li>
                                I understand that privacy and confidentiality laws apply to telemedicine and that my
                                identifiable medical information will not be disclosed without my written consent.
                            </li>
                            <li>
                                I understand that I may withdraw my consent at any time without affecting future care.
                            </li>
                            <li>
                                I understand the alternatives to telemedicine and that some physical examinations may
                                be conducted by personnel at my location under provider guidance.
                            </li>
                            <li>
                                I understand that my medical information may be shared electronically with providers
                                located in other areas, including out of state.
                            </li>
                            <li>
                                I understand that no guarantees can be made regarding outcomes of telemedicine care.
                            </li>
                            <li>
                                I understand that my information may be shared for scheduling and billing purposes and
                                that I may request privacy accommodations or terminate the consultation at any time.
                            </li>
                        </ol>

                        <h4>Patient Consent to the Use of Telemedicine</h4>

                        <p>
                            I have read and understand the information provided above regarding telemedicine and have
                            had the opportunity to discuss it with my physician or designated assistants.
                        </p>

                        <p>
                            I hereby give my informed consent to participate in a telemedicine visit under the terms
                            described herein.
                        </p>
                    </div>

                    <input type="checkbox" id="termsAndConditions" hidden>
                    <label for="termsAndConditions">By sign below you agree that you have read and agree to Terms and Conditions and Privacy Policy.</label>
                    <canvas id="canvas-signature" class="canvas-sig"></canvas>

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

                <div class="btns-wrap">
                    <button type="submit" class="btn btn-primary w-100" style="background-color: #7b68ee; border-radius: 50px; border: none;">
                        Upload Signature
                    </button>
                    <button id="signature-clear" class="btn btn-primary w-100" style="background-color: #7b68ee; border-radius: 50px; border: none;">
                        clear signature
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    let canvas = jQuery('#canvas-signature')[0];
    let clearSignature = jQuery('#signature-clear')[0];

    jQuery(document).ready(function($) {
        $("#idUploadForm").on("submit", function(e) {
            e.preventDefault();

            let base64Image = getCanvasBase64();
            let form = $(this);
            let button = form.find("button[type=submit]");
            let formData = new FormData(this);

            formData.append('signature', base64Image);
            formData.append("action", "glp_agreement_upload");

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

    clearSignature.addEventListener('click', (e) => {
        e.preventDefault();
        ctx.clearRect(0, 0, canvas.width, canvas.height);
    })

    canvas.width = canvas.offsetWidth;
    canvas.height = canvas.offsetHeight;

    let coord = {
        x: 0,
        y: 0
    };
    let ctx;

    if (canvas.getContext) {
        ctx = canvas.getContext('2d');
        canvas.addEventListener('pointerdown', drawStart);
        canvas.addEventListener('pointerout', drawStop);
        canvas.addEventListener('pointerup', drawStop);

        canvas.addEventListener('touchstart', drawStart);
        canvas.addEventListener('touchend', drawStop);
        canvas.addEventListener('touchcancel', drawStop);

        function getCanvasBase64() {
            if (!canvas) {
                console.error("Canvas element not found!");
                return null;
            }
            return canvas.toDataURL('image/png', 1.0);
        }

        // function drawStart(event) {
        //     event.preventDefault(); // 🔑 stops mobile scrolling
        //     document.body.style.overflow = 'hidden'
        //     canvas.addEventListener('pointermove', draw);
        //     canvas.addEventListener('touchmove', draw);
        //     reposition(event);
        // }
        function drawStart(event) {
            event.preventDefault(); // 🔑 stops mobile scrolling
            document.body.style.overflow = 'hidden';

            canvas.addEventListener('pointermove', draw);
            canvas.addEventListener('touchmove', draw, {
                passive: false
            });

            reposition(event);
        }

        function getTouchOrMouse(event) {
            return event.touches && event.touches.length ? event.touches[0] : event;
        }

        // function drawStop() {
        //     canvas.removeEventListener('pointermove', draw);
        //     document.body.style.overflow = ''
        // }
        function drawStop(event) {
            event && event.preventDefault();
            canvas.removeEventListener('pointermove', draw);
            canvas.removeEventListener('touchmove', draw);

            document.body.style.overflow = '';
        }

        function reposition(event) {
            const point = getTouchOrMouse(event);
            const rect = event.target.getBoundingClientRect();
            coord.x = point.clientX - rect.left;
            coord.y = point.clientY - rect.top;
        }

        function draw(event) {
            ctx.beginPath()
            ctx.lineWidth = 5;
            ctx.lineCap = "round";
            ctx.strokeStyle = '#000000';
            ctx.moveTo(coord.x, coord.y);
            reposition(event);
            ctx.lineTo(coord.x, coord.y);
            ctx.stroke();
        }

        function resizeCanvas() {
            const rect = canvas.getBoundingClientRect();
            canvas.width = rect.width;
            canvas.height = rect.height;
        }

        resizeCanvas();
        window.addEventListener('resize', resizeCanvas);


    } else {
        // Fallback content for unsupported browsers
        alert('Your browser does not support the Canvas element.');
    }
</script>