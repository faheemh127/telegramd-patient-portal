<style>

.canvas-sig {
  width: 400px;
  height: 400px;

}

</style>
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
                    <label for="patientID" class="form-label">Signature (Required) </label>

                    <input
                        class="form-control"
                        type="file"
                        id="patientID"
                        name="patient_id"
                        accept=".jpg,.jpeg,.png,.pdf"
                        hidden
                        required>


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
                    Upload
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


    clearSignature.addEventListener('click', ()=>{
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

        function drawStart(event){
          canvas.addEventListener('pointermove', draw);
          reposition(event);
        }

        function drawStop(){
          canvas.removeEventListener('pointermove', draw);
        }

        function reposition(event){
          const rect = event.target.getBoundingClientRect();
          coord.x = event.clientX - rect.left;
          coord.y = event.clientY - rect.top;
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
