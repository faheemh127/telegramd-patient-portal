console.log("custom javascript 109 loaded");
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










// // button = document.querySelector('button[name="save_progress_button"]');
// const button = document.querySelector('button[name="save_progress_button"]');

// if (button) {
//   button.addEventListener('click', function () {
//     console.log('Button clicked, waiting 4 seconds...');
    
//     setTimeout(() => {
//       // Get the first matching input field
//       const input = document.querySelector('.ff-el-input--content .ff_input-group input');
      
//       if (input) {
//         console.log('Input value:', input.value);
//       } else {
//         console.log('Input field not found.');
//       }
//     }, 4000); // 4000 ms = 4 seconds
//   });
// } else {
//   console.log('Button not found.');
// }

























const saveProgressBtn = document.querySelector('button[name="save_progress_button"]');
const formID = 13; // dummy form ID
if (saveProgressBtn) {
  saveProgressBtn.addEventListener("click", function () {
    console.log("Button clicked, waiting 4 seconds...");

    setTimeout(() => {
      const input = document.querySelector(
        ".ff-el-input--content .ff_input-group input"
      );

      if (input) {
        const inputValue = input.value;

        console.log("Sending value to backend:", inputValue);

        // Send AJAX request to WordPress
        fetch(ajaxurl, {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
          body: new URLSearchParams({
            action: "save_form_url",
            form_id: formID,
            form_url: inputValue,
          }),
        })
          .then((response) => response.json())
          .then((data) => {
            if (data.success) {
              console.log("Form value saved successfully");
            } else {
              console.log("Error saving form value:", data.data);
            }
          })
          .catch((error) => {
            console.error("AJAX error:", error);
          });
      } else {
        console.log("Input field not found.");
      }
    }, 4000);
  });
} else {
  console.log("saveProgressBtn not found.");
}





























  const nextButtons = document.querySelectorAll(".ff-btn-next");

  nextButtons.forEach(function (button) {
    button.addEventListener("click", function (e) {
      console.log("➡️ Fluent Form Next button clicked");

      // ✅ Your custom logic here
      handleNextButtonClick(button);
    });
  });

  function handleNextButtonClick(btn) {
    // You can access the button DOM or trigger actions here
    console.log("✅ Next button triggered:", btn);




    
      const button = document.querySelector('button[name="save_progress_button"]');
      button.click();


















  }// handleNextButtonClick




































}); // main document loaded container

// set patient email as global so other dev can use this email
window.hldPatientEmail = hldData.hldPatientEmail || null;
