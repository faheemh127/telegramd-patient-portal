console.log("custom javascript 108 loaded");
// This code is to hide radio buttons' next button
document.addEventListener("DOMContentLoaded", function () {


  const steps = document.querySelectorAll(".fluentform-step");

  steps.forEach((step) => {
    const radios = step.querySelectorAll('input[type="radio"]');
    const checkboxes = step.querySelectorAll('input[type="checkbox"]');
    const allInputs = step.querySelectorAll("input");
    const finishMessage = step.querySelector('p[data-form-finish="true"]');

    const nextButton = step.querySelector('button[data-action="next"]');
    const progressBar = document.querySelector(".ff-el-progress-bar");

    // If the step has radios and no checkboxes, or has a finish message, hide the next button
    if ((radios.length > 0 && checkboxes.length === 0) || finishMessage) {
      if (nextButton) {
        nextButton.style.display = "none";
      }

      if (finishMessage && progressBar) {
        progressBar.style.width = "100%";
      }
    }
  });
});

// set patient email as global so other dev can use this email
window.hldPatientEmail = hldData.hldPatientEmail || null;
