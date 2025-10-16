class HldNavigation {
  constructor() {
    this.init();
  }

  // Initialize on page load
  // use .hld_disqualify_step on each step that is fluent form step
  init() {
    // commenting this because now we are moving the login page before checkout so its not need  at the moment
    // this.checkNextEndLoginAndNavigate(); // don't need here  its now called in mutation observer function
    this.initLoginWrapListener();
    // this.hideNextBtnLoginWrap();

    this.initActionItemSidebar();
    // this.showActionItemSidebar();
    this.connectNavItemsWithActionItem();
    this.hideNextBtnDisqualifyStep();

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
              // disqualifySection.style.display = "block"; // Show the section
            }
          }
        });
      }
    });
  }

  
  hideNextBtnDisqualifyStep() {
    const disqualifySteps = document.querySelectorAll(".hld_disqualify_step");

    if (!disqualifySteps.length) return; // ⛔ Exit if none found

    console.log("Disqualify steps found:", disqualifySteps);

    disqualifySteps.forEach((step) => {
      const nextBtn = step.querySelector("button.ff-btn-next");
      if (nextBtn) nextBtn.style.display = "none";
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

    // If on specific pages or if ?upload-id is present in query string, stop

    if (
      window.location.href.includes("glp-1-prefunnel") ||
      window.location.href.includes("glp-1-weight-loss-intake") ||
      window.location.href.includes("upload-id")
    ) {
      return;
    }

    if (!hldActionItem.userInfo.logged_in) {
      return;
    }

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

  initLoginWrapListener() {
    const loginWrap = document.querySelector(".hld_login_wrap");
    if (!loginWrap) return;

    // Observe style changes on the element
    const observer = new MutationObserver(() => {
      const computedStyle = window.getComputedStyle(loginWrap);
      const isVisible =
        computedStyle.display === "block" && computedStyle.opacity === "1";

      if (isVisible) {
        console.log("hld_login_wrap is now visible (display:block; opacity:1)");
        this.checkNextEndLoginAndNavigate();
      }
    });

    // Watch for style attribute changes
    observer.observe(loginWrap, {
      attributes: true,
      attributeFilter: ["style"],
    });

    console.log("Login wrap listener initialized.");
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
  checkNextEndLoginAndNavigate() {
    console.log("function checkNextEndLoginAndNavigate is working");

    // Parse the current URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    const isNextendLoginComplete =
      urlParams.get("nextend_login_complete") === "1";

    // Check both conditions before proceeding
    // if (this.isUserLoggedIn() && isNextendLoginComplete) {
    if (this.isUserLoggedIn()) {
      const stepElement = document.querySelector(".hld_login_wrap");

      if (stepElement) {
        const nextButton = stepElement.querySelector(
          'button[data-action="next"]'
        );

        if (nextButton) {
          console.log("nextButton found:", nextButton);
          console.log("Will click FluentForm next button in 4s...");

          setTimeout(() => {
            console.log("button is clicked 4564", nextButton);

            nextButton.click();
            console.log("Clicked FluentForm next button!");
          }, 10); // small delay to allow DOM updates
        }
      }
    } else {
      console.log(
        "Conditions not met. Either user is not logged in or nextend_login_complete != 1"
      );
    }
  }
}

// Initialize only when DOM is ready
document.addEventListener("DOMContentLoaded", () => {
  window.hldNavigation = new HldNavigation();
});
