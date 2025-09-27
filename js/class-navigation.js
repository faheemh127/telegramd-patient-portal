class HldNavigation {
  constructor() {
    this.init();
  }

  // Initialize on page load
  // use .hld_disqualify_step on each step that is fluent form step
  init() {
    this.checkLoginAndNavigate();
    console.log(hldActionItem);
    this.initActionItemSidebar();
    this.showActionItemSidebar();
    this.connectNavItemsWithActionItem();
    this.hideNextBtnDisqualifyStep();
    this.hideNextBtnDisqualifyStep();
    this.hideNextBtnLoginWrap();
    this.disqualifyLessThan18();
  }

  // disqualifyLessThan18() {
  //   // hide next button first
  //   const dobStep = document.querySelectorAll(".hld_dob_wrap");
  //   dobStep.forEach(function (step) {
  //     const nextBtn = step.querySelector("button.ff-btn-next");
  //     if (nextBtn) {
  //       nextBtn.style.display = "none";
  //     }
  //   });
  // }

  disqualifyLessThan18() {
    const dobSteps = document.querySelectorAll(".hld_dob_wrap");

    if (document.querySelector(".dobDisqualifySection")) {
      document.querySelector(".dobDisqualifySection").style.display = "none";
    }




    dobSteps.forEach(function (step) {
      const nextBtn = step.querySelector("button.ff-btn-next");
      const dobField = step.querySelector(".hldDobField");

      // Hide the button by default
      if (nextBtn) {
        nextBtn.style.display = "none";
      }

      if (dobField) {
        dobField.addEventListener("change", function () {
          const dobValue = dobField.value.trim(); // Example: "03-Sep-25"

          if (!dobValue) {
            if (nextBtn) nextBtn.style.display = "none";
            return;
          }

          // Parse date from format dd-MMM-yy (e.g. "03-Sep-25")
          const parsedDate = new Date(dobValue);
          if (isNaN(parsedDate)) {
            console.warn("Invalid DOB format:", dobValue);
            if (nextBtn) nextBtn.style.display = "none";
            return;
          }

          // Calculate age
          const today = new Date();
          let age = today.getFullYear() - parsedDate.getFullYear();
          const m = today.getMonth() - parsedDate.getMonth();
          if (m < 0 || (m === 0 && today.getDate() < parsedDate.getDate())) {
            age--;
          }

          // Toggle next button
          const disqualifySection = document.querySelector(
            ".dobDisqualifySection"
          );
          if (age >= 18) {
            nextBtn.style.display = "block"; // show if >= 18
            if (disqualifySection) {
              disqualifySection.style.display = "none"; // Show the section
            }
          } else {
            nextBtn.style.display = "none"; // keep hidden if < 18
            if (disqualifySection) {
              disqualifySection.style.display = "block"; // Show the section
            }
          }
        });
      }
    });
  }

  hideNextBtnDisqualifyStep() {
    const disqualifySteps = document.querySelectorAll(".hld_disqualify_step");

    console.log("disqualify step found", disqualifySteps);

    disqualifySteps.forEach(function (step) {
      const nextBtn = step.querySelector("button.ff-btn-next");
      if (nextBtn) {
        nextBtn.style.display = "none";
      }
    });
  }

  hideNextBtnLoginWrap() {
    const loginWraps = document.querySelectorAll(".hld_login_wrap");

    if (loginWraps.length > 0) {
      console.log("Login wraps found:", loginWraps);

      loginWraps.forEach(function (wrap) {
        const nextBtn = wrap.querySelector("button.ff-btn-next");
        if (nextBtn) {
          nextBtn.style.display = "none";
        }
      });
    } else {
      console.log("No .hld_login_wrap found on this page.");
    }
  }

  connectNavItemsWithActionItem() {
    const actionItems = document.querySelector(".hld_nav_action_items");
    const subscriptions = document.querySelector(".hld_nav_subscriptions");
    const appointments = document.querySelector(".hld_nav_appointments");
    const profile = document.querySelector(".hld_nav_profile");

    // Attach listeners with correct context
    [actionItems, subscriptions, appointments, profile].forEach((el) => {
      if (el) {
        el.addEventListener("click", this.showActionItemIfExists.bind(this));
      }
    });
  }

  showActionItemIfExists() {
    // Check the global condition
    if (
      !window.hldActionItem?.glp1Prefunnel ||
      window.hldActionItem.glp1Prefunnel === "0"
    ) {
      return;
    }

    console.log("function called");
    this.showActionItemSidebar();
  }

  initActionItemSidebar() {
    if (!hldActionItem.glp1Prefunnel || hldActionItem.glp1Prefunnel == "0")
      return;

    const overlay = document.getElementById("hldSidebarOverlay");
    const sidebar = document.getElementById("hldSidebar");
    const closeBtn = document.getElementById("hldSidebarClose");

    // Show sidebar only if overlay & sidebar exist
    if (overlay && sidebar) {
      this.showActionItemSidebar();

      // Close sidebar on button click
      if (closeBtn) {
        closeBtn.addEventListener("click", () => {
          sidebar.classList.remove("active");
          setTimeout(() => overlay.classList.remove("active"), 300);
        });
      }

      // Close sidebar on overlay click (outside click)
      overlay.addEventListener("click", (e) => {
        if (e.target === overlay) {
          sidebar.classList.remove("active");
          setTimeout(() => overlay.classList.remove("active"), 300);
        }
      });
    }
  }

  showActionItemSidebar() {
    const overlay = document.getElementById("hldSidebarOverlay");
    const sidebar = document.getElementById("hldSidebar");

    if (overlay) {
      overlay.classList.add("active");
    }
    if (sidebar) {
      sidebar.classList.add("active");
    }
  }

  // Function to check if user is logged in (dummy example, replace with real logic)
  isUserLoggedIn() {
    // Example: check cookie, localStorage, or make AJAX call
    // return true if logged in, false otherwise
    return !!document.body.classList.contains("logged-in"); // WordPress adds this automatically
  }

  // If user is logged in, auto-click next button in FluentForm
  checkLoginAndNavigate() {
    console.log("function checkLoginAndNavigate is working");
    if (this.isUserLoggedIn()) {
      const stepElement = document.querySelector(".hld_login_wrap");
      if (stepElement) {
        const nextButton = stepElement.querySelector(
          'button[data-action="next"]'
        );

        if (nextButton) {
          console.log("nextButton", nextButton);
          console.log("Will click FluentForm next button in 1 second...");

          setTimeout(() => {
            nextButton.click();
            console.log("Clicked FluentForm next button!");
          }, 10); // 1000ms = 1 second
        }
      }
    }
  }
}

// Initialize only when DOM is ready
document.addEventListener("DOMContentLoaded", () => {
  window.hldNavigation = new HldNavigation();
});
