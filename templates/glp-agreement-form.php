
<style>

.canvas-sig {
  width: 400px;
  height: 400px;

}

</style>
 <div class="container d-flex flex-column justify-content-center  mt-5 hld-upload-id">

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
                     <h1>Agreement</h1>
                     <label for="patientID" class="form-label">Sign below to agree to the informed consent.</label>
                     <hr>
                     <h2>Agreement</h2>
                     <h3>Informed Consent of Services Performed.</h3>
                     <div style="max-height: 150px; overflow-y: scroll; margin: 35px 0;">

                         Telemedicine involves the use of electronic communications to enable healthcare providers at different locations to share individual patient medical information for the purpose of improving patient care. Providers may include primary care practitioners, specialists, and/or subspecialists. The information may be used for diagnosis, therapy, follow-up and/or education, and may include any of the following:

                         ● Patient medical records
                         ● Medical images
                         ● Live two-way messaging, audio and video
                         ● Output data from medical devices and sound and video files

                         Electronic systems used will incorporate network and software security protocols to protect the confidentiality of patient identification and imaging data and will include measures to safeguard the data and to ensure its integrity against intentional or unintentional corruption.

                         Responsibility for the patient care should remain with the patient’s local clinician, if you have one, as does the patient’s medical record.

                         Expected Benefits:


                         ● Improved access to medical care by enabling a patient to remain in his/her local healthcare site (i.e. home) while the physician consults and obtains test results at distant/other sites.
                         ● More efficient medical evaluation and management.
                         ● Obtaining expertise of a specialist.

                         Possible Risks:

                         As with any medical procedure, there are potential risks associated with the use of telemedicine. These risks include, but may not be limited to:

                         ● In rare cases, the consultant may determine that the transmitted information is of inadequate quality, thus necessitating a face-to-face meeting with the patient, or at least a rescheduled video consult;
                         ● Delays in medical evaluation and treatment could occur due to deficiencies or failures of the equipment;
                         ● In very rare instances, security protocols could fail, causing a breach of privacy of personal medical information;
                         ● In rare cases, a lack of access to complete medical records may result in adverse drug interactions or allergic reactions or other judgment errors;

                         By checking the box associated with “Informed Consent”, You acknowledge that you understand and agree with the following:

                         1. I understand that the laws that protect privacy and the confidentiality of medical information also apply to telemedicine, and that no information obtained in the use of telemedicine, which identifies me, will be disclosed to researchers or other entities without my written consent.
                         2. I understand that I have the right to withhold or withdraw my consent to the use of telemedicine in the course of my care at any time, without affecting my right to future care or treatment.
                         3. I understand the alternatives to telemedicine consultation as they have been explained to me, and in choosing to participate in a telemedicine consultation, I understand that some parts of the exam involving physical tests may be conducted by individuals at my location, or at a testing facility, at the direction of the consulting healthcare provider.
                         4. I understand that telemedicine may involve electronic communication of my personal medical information to other medical practitioners who may be located in other areas, including out of state.
                         5. I understand that I may expect the anticipated benefits from the use of telemedicine in my care, but that no results can be guaranteed or assured.
                         6. I understand that my healthcare information may be shared with other individuals for scheduling and billing purposes. Others may also be present during the consultation other than my healthcare provider and consulting healthcare provider in order to operate the video equipment. The above mentioned people will all maintain confidentiality of the information obtained. I further understand that I will be informed of their presence in the consultation and thus will have the right to request the following: (1) omit specific details of my medical history/physical examination that are personally sensitive to me; (2) ask non-medical personnel to leave the telemedicine examination room; and/or (3) terminate the consultation at any time.

                         Patient Consent To The Use of Telemedicine


                         I have read and understand the information provided above regarding telemedicine, have discussed it with my physician or such assistants as may be designated, and all of my questions have been answered to my satisfaction.

                         I have read this document carefully, and understand the risks and benefits of the teleconferencing consultation and have had my questions regarding the procedure explained and I hereby give my informed consent to participate in a telemedicine visit under the terms described herein.

                     </div>
                     <input type="checkbox">
                     <label for="">I have read and agree to Terms and Conditions and Privacy Policy.</label>
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

                 <button type="submit" class="btn btn-primary w-100" style="background-color: #7b68ee; border-radius: 50px; border: none;">
                     Upload Signature
                 </button>
                <button id="signature-clear" class="btn btn-primary w-100" style="background-color: #7b68ee; border-radius: 50px; border: none;">
                   clear signature
                </button>
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

    clearSignature.addEventListener('click', (e)=>{
      e.preventDefault();
      ctx.clearRect(0, 0, canvas.width, canvas.height);
    })

    canvas.width = canvas.offsetWidth;
    canvas.height = canvas.offsetHeight;

    let coord = { x: 0, y: 0};
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

        function drawStart(event){
          document.body.style.overflow = 'hidden'
          canvas.addEventListener('pointermove', draw);
          canvas.addEventListener('touchmove', draw);
          reposition(event);
        }

        function getTouchOrMouse(event) {
            return event.touches && event.touches.length ? event.touches[0] : event;
        }

        function drawStop(){
          canvas.removeEventListener('pointermove', draw);
          document.body.style.overflow = ''
        }

        function reposition(event){
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
    } else {
        // Fallback content for unsupported browsers
        alert('Your browser does not support the Canvas element.');
    }


 </script>
