class hldFluentFormClass {
  constructor() {
    // Initialization logic here (if needed)
  }

  clickNextMultipleTimesToReachStep(currentStepDiv, nextButton) {
    if (!currentStepDiv || !nextButton) {
      console.warn("Missing required parameters.");
      return;
    }

    let count = 0;
    let sibling = currentStepDiv.previousElementSibling;

    console;

    // Count all previous siblings with class 'fluentform-step'
    while (sibling) {
      if (sibling.classList.contains("fluentform-step")) {
        count++;
      }
      sibling = sibling.previousElementSibling;
    }

    console.log(`Clicking next button ${count} time(s)`);

    // Click the next button 'count' number of times
    let i = 0;
    const interval = setInterval(() => {
      if (i < count) {
        nextButton.click();
        i++;
      } else {
        clearInterval(interval);
        console.log("Finished clicking Next button");
      }
    }, 1200); // Delay between clicks (adjust if needed)
  }

  getActiveStepNumber() {
    const activeStepDiv = document.querySelector(".fluentform-step.active");
    if (!activeStepDiv) return null;

    const dataName = activeStepDiv.getAttribute("data-name");
    const match = dataName.match(/_(\d+)$/); // extract number at the end after underscore

    if (match && match[1]) {
      return parseInt(match[1], 10);
    }

    return null;
  }

  // You can add your methods below
} // Class ends

const hldFluentFormHelper = new hldFluentFormClass();

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

  // on form 14 on step one click check if the user is logged in then move him to step 3

  const goalRadios = document.querySelectorAll(
    '.fluentform-step.active .survey-radio input[type="radio"][name="input_radio"]'
  );

  goalRadios.forEach(function (radio) {
    radio.addEventListener("change", function () {
      if (this.checked) {
        console.log("🎯 Selected weight loss goal:", this.value);

        // ✅ Call your own custom function here
        hldHandleGoalSelection(this.value);
      }
    });
  });

  function hldHandleGoalSelection(value) {
    console.log("✅ Callback triggered with:", value);

    // ✅ Check if patient email is available
    if (window.hldPatientEmail != null) {
      // 🔍 Find the correct 'Next' button inside the step
      const nextBtn = document.querySelector(
        '.fluentform-step[data-name="step_start-13_50"] button[data-action="next"]'
      );

      if (nextBtn) {
        console.log("✅ Auto-clicking Next button...");
        nextBtn.click(); // 🚀 Simulate the click
      } else {
        console.warn("⚠️ Next button not found inside step_start-13_50");
      }
    } else {
      console.log("❌ Patient email not set. Skipping auto-step.");
    }
  }

  const urlParams = new URLSearchParams(window.location.search);
  const fluentState = urlParams.get("fluent_state");
  console.log("working active form step code");

  if (fluentState) {
    const savedStep = hldFormData.activeStep; // Example: step saved previously

    // Get form ID from the URL or your system
    const formID = 13;
    const currentStepName = `form_step-${formID}_${savedStep}`;

    // Wait for Fluent Form DOM to fully render
    setTimeout(() => {
      // Find the step div with the current saved step
      const targetStepDiv = document.querySelector(
        `div.fluentform-step[data-name="${currentStepName}"]`
      );

      if (targetStepDiv) {
        // Find the previous .fluentform-step element
        const previousStepDiv = targetStepDiv.previousElementSibling;

        if (
          previousStepDiv &&
          previousStepDiv.classList.contains("fluentform-step")
        ) {
          // Find the 'Next' button inside the previous step
          const nextButton = previousStepDiv.querySelector(
            'button[data-action="next"]'
          );

          if (nextButton) {
            document
              .querySelectorAll(".fluentform-step.active")
              .forEach(function (el) {
                el.classList.remove("active");
              });

            if (
              previousStepDiv &&
              previousStepDiv.classList.contains("fluentform-step")
            ) {
              previousStepDiv.classList.add("active");
            }

            console.log("previousStepDiv", previousStepDiv);
            console.log(nextButton);
            console.log("Clicking Next button to jump to step", savedStep);
            hldFluentFormHelper.clickNextMultipleTimesToReachStep(
              previousStepDiv,
              nextButton
            );
            // nextButton.click();
            // nextButton.click();
          } else {
            console.warn("Next button not found in previous step.");
          }
        } else {
          console.warn("Previous step element not found.");
        }
      } else {
        console.warn("Target step div not found for step:", savedStep);
      }
    }, 3000); // Wait a second for Fluent Forms to render
  }
}); // main document loaded container

// set patient email as global so other dev can use this email
window.hldPatientEmail = hldData.hldPatientEmail || null;

// Class for BMI calculation
class hld_BMICalculator {
  constructor() {
    this.feetInput = document.querySelector(".hld_bmi-feet");
    this.inchesInput = document.querySelector(".hld_bmi-inches");
    this.weightInput = document.querySelector(".hld_bmi-weight");
    this.bmiValue = document.querySelector(".hld_bmi-value");
    this.successNotification = document.querySelector(
      ".hld_bmi-notification.hld_success"
    );
    this.warningNotification = document.querySelector(
      ".hld_bmi-notification.hld_warning"
    );

    this.init();
  }

  init() {
    if (!this.feetInput || !this.inchesInput || !this.weightInput) return;
    [this.feetInput, this.inchesInput, this.weightInput].forEach((input) => {
      input.addEventListener("input", () => this.calculateBMI());
    });
    this.calculateBMI(); // initial run
  }

  calculateBMI() {
    const feet = parseFloat(this.feetInput.value) || 0;
    const inches = parseFloat(this.inchesInput.value) || 0;
    const weight = parseFloat(this.weightInput.value) || 0;

    const heightInInches = feet * 12 + inches;
    if (heightInInches === 0 || weight === 0) {
      this.bmiValue.textContent = "0";
      this.toggleNotifications(null);
      return;
    }

    const bmi = (weight / (heightInInches * heightInInches)) * 703;
    this.bmiValue.textContent = bmi.toFixed(1);

    // Demo condition: change "25" to your real threshold later
    if (bmi >= 25) {
      this.toggleNotifications("success");
      hldFormHandler.showBmiNextBtn();
    } else {
      this.toggleNotifications("warning");
      hldFormHandler.hideBmiNextBtn();
    }
  }

  toggleNotifications(type) {
    this.successNotification.style.display = "none";
    this.warningNotification.style.display = "none";

    // Select elements
    const bmiLabel = document.querySelector(".hld_bmi-label");
    const bmiValue = document.querySelector(".hld_bmi-value");
    const bmiCircle = document.querySelector(".hld_bmi-circle");

    // Reset styles first
    if (bmiLabel) bmiLabel.style.color = "";
    if (bmiValue) bmiValue.style.border = "";
    if (bmiCircle) bmiCircle.style.border = "";

    if (type === "success") {
      this.successNotification.style.display = "flex";

      if (bmiLabel) bmiLabel.style.color = "#2e7d32"; // green
      if (bmiValue) bmiValue.style.color = "#2e7d32";
      if (bmiCircle) bmiCircle.style.border = "6px solid #2e7d32";
    } else if (type === "warning") {
      this.warningNotification.style.display = "flex";

      if (bmiLabel) bmiLabel.style.color = "#d32f2f"; // red/danger
      if (bmiValue) bmiValue.style.color = "#d32f2f";
      if (bmiCircle) bmiCircle.style.border = "6px solid #d32f2f";
    }
  }
}

// Init on page load
document.addEventListener("DOMContentLoaded", () => {
  new hld_BMICalculator();
});

document.addEventListener("DOMContentLoaded", function () {
  const visibleInput = document.querySelector(".hldDobFieldRollout");
  if (visibleInput) {
    visibleInput.id = "hldPrefunnelDOBInput"; // assign your own ID
  }

  visibleInput.setAttribute("readonly", true);

  new Rolldate({
    el: "#hldPrefunnelDOBInput",
    format: "MM-DD-YYYY",
    beginYear: 1950,
    lang: {
      title: "Select A Date",
      cancel: "Cancel",
      confirm: "Confirm",
      year: "",
      month: "",
      day: "",
      hour: "",
      min: "",
      sec: "",
    },
    moveEnd: function (scroll) {
      console.log(scroll);
      console.log("scroll end");
    },
    confirm: function (date) {
      console.log(date);
      console.log("confirm");
    },
    cancel: function () {
      console.log("cancel");
    },
  });
});
