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
    this.hideNextBtnLoginWrap();
    this.initActionItemSidebar();
    // this.showActionItemSidebar();
    this.connectNavItemsWithActionItem();
    this.hideNextBtnDisqualifyStep();
    this.disqualifyLessThan18();
    // this.showAllPrevButtons();
    // 👇 Initialize the back button listener here
    this.initBackButtonListener();
  }

  // 👇 Add this new method
  initBackButtonListener() {
    const backBtn = document.getElementById("hld-back-btn");

    if (!backBtn) return;

    backBtn.addEventListener("click", () => {
      // Find the currently active FluentForm step
      const activeStep = document.querySelector(".fluentform-step.active");

      if (activeStep) {
        // Find the "Previous" button inside that active step
        const prevButton = activeStep.querySelector(".ff-btn-prev");

        if (prevButton) {
          prevButton.click(); // Trigger FluentForm's previous step
        } else {
          console.warn("⚠️ No .ff-btn-prev found inside active step.");
        }
      } else {
        console.warn("⚠️ No active .fluentform-step found.");
      }
    });
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

  // older function that was creating issue on IOS
  // disqualifyLessThan18(date) {
  //   console.log("date in disqualifyLessThan18 ", date);
  //   console.log("function disqualifyLessThan18 called");

  //   const dobSteps = document.querySelectorAll(".hld_dob_wrap");

  //   // Hide disqualify section initially
  //   const disqualifySection = document.querySelector(".dobDisqualifySection");
  //   if (disqualifySection) {
  //     disqualifySection.style.display = "none";
  //   }

  //   dobSteps.forEach(function (step) {
  //     const nextBtn = step.querySelector("button.ff-btn-next");

  //     // Hide the Next button by default
  //     if (nextBtn) {
  //       nextBtn.style.display = "none";
  //     }

  //     // If no date is provided, keep button hidden and return
  //     if (!date) {
  //       console.warn("No date provided to disqualifyLessThan18");
  //       return;
  //     }

  //     // Parse the date (expected format: MM-DD-YYYY)
  //     const parsedDate = new Date(date);
  //     if (isNaN(parsedDate)) {
  //       console.warn("Invalid date format:", date);
  //       return;
  //     }

  //     // Calculate age
  //     const today = new Date();
  //     let age = today.getFullYear() - parsedDate.getFullYear();
  //     const monthDiff = today.getMonth() - parsedDate.getMonth();
  //     if (
  //       monthDiff < 0 ||
  //       (monthDiff === 0 && today.getDate() < parsedDate.getDate())
  //     ) {
  //       age--;
  //     }

  //     console.log("Calculated Age:", age);

  //     // Toggle visibility
  //     if (age >= 18) {
  //       if (nextBtn) nextBtn.style.display = "block";
  //       if (disqualifySection) disqualifySection.style.display = "none";
  //     } else {
  //       if (nextBtn) nextBtn.style.display = "none";
  //       // if (disqualifySection) disqualifySection.style.display = "block";
  //     }
  //   });
  // }

  disqualifyLessThan18(date) {
    console.log("date in disqualifyLessThan18 ", date);
    console.log("function disqualifyLessThan18 called");

    const dobSteps = document.querySelectorAll(".hld_dob_wrap");

    // Hide disqualify section initially
    const disqualifySection = document.querySelector(".dobDisqualifySection");
    if (disqualifySection) {
      disqualifySection.style.display = "none";
    }

    dobSteps.forEach(function (step) {
      const nextBtn = step.querySelector("button.ff-btn-next");

      // Hide the Next button by default
      if (nextBtn) {
        nextBtn.style.display = "none";
      }

      // If no date is provided, keep button hidden and return
      if (!date) {
        console.warn("No date provided to disqualifyLessThan18");
        return;
      }

      // ✅ SAFELY handle MM-DD-YYYY for iOS
      let parsedDate = null;
      if (typeof date === "string" && date.includes("-")) {
        const parts = date.split("-");
        if (parts.length === 3) {
          const [month, day, year] = parts;
          // Convert to ISO format YYYY-MM-DD — works everywhere
          parsedDate = new Date(`${year}-${month}-${day}`);
        }
      }

      // Fallback if still invalid
      if (!(parsedDate instanceof Date) || isNaN(parsedDate.getTime())) {
        console.warn("Invalid date format for parsing:", date);
        return;
      }

      // Calculate age
      const today = new Date();
      let age = today.getFullYear() - parsedDate.getFullYear();
      const monthDiff = today.getMonth() - parsedDate.getMonth();
      if (
        monthDiff < 0 ||
        (monthDiff === 0 && today.getDate() < parsedDate.getDate())
      ) {
        age--;
      }

      console.log("Calculated Age:", age);

      // Toggle visibility
      if (age >= 18) {
        if (nextBtn) nextBtn.style.display = "block";
        if (disqualifySection) disqualifySection.style.display = "none";
      } else {
        if (nextBtn) nextBtn.style.display = "none";
        // if (disqualifySection) disqualifySection.style.display = "block";
      }
    });
  }

  showAllPrevButtons() {
    const stepContainers = document.querySelectorAll(
      ".ff-step-container .fluentform-step"
    );

    stepContainers.forEach((step) => {
      const prevButton = step.querySelector(".ff-btn-prev");
      if (prevButton) {
        // Add display:block !important
        prevButton.style.setProperty("display", "block", "important");
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
      window.location.href.includes("trt-prefunnel") ||
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
        hldFormHandler.getAmount();
        hldFormHandler.setStripeData();
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
