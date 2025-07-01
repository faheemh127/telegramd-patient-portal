console.log("javascript104 is working");

// this code is to hide radio buttons next button
document.addEventListener("DOMContentLoaded", function () {
  const steps = document.querySelectorAll(".fluentform-step");

  steps.forEach((step) => {
    const radios = step.querySelectorAll('input[type="radio"]');
    const checkboxes = step.querySelectorAll('input[type="checkbox"]');
    const allInputs = step.querySelectorAll("input");

    const nextButton = step.querySelector('button[data-action="next"]');
    // If there are no radios and only checkboxes, hide the next button

    if (radios.length > 0 && checkboxes.length === 0) {
      if (nextButton) {
        nextButton.style.display = "none";
      }
    }
  });
});
