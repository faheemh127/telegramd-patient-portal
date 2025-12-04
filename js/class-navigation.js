class HldNavigation {
  constructor() {
    this.init();
  }

  // Initialize on page load
  // use .hld_disqualify_step on each step that is fluent form step
  init() {
    // commenting this because now we are moving the login page before checkout so its not need  at the moment
    // this.checkNextEndLoginAndNavigate(); // don't need here  its now called in mutation observer function
    // this.initLoginWrapListener();
    this.hideNextBtnLoginWrap();
    this.initActionItemSidebar();
    // this.showActionItemSidebar();
    this.connectNavItemsWithActionItem();
    this.hideNextBtnDisqualifyStep();
    this.disqualifyLessThan18();
    this.hideLayoutIfForm();
    this.showNextButtonAfterSelectSelection();
    this.showNextButtonOnRadioSelection();
    this.hideNextBtnOnDOB();
    // this.showAllPrevButtons();
    // 👇 Initialize the back button listener here
    this.initBackButtonListener();
  }

  // 👇 Add this new method
  initBackButtonListener() {
    const backBtn = document.getElementById("hld-back-btn");

    // const stepElement = document.querySelector(".hld_login_wrap");
    // const nextButton = stepElement.querySelector('button[data-action="next"]');
    // document.addEventListener("ff_to_next_page", () => alert("ASDFASDF"));

    if (!backBtn) return;

    backBtn.addEventListener("click", () => {
      // Find the currently active FluentForm step
      var $ = jQuery;
      let steps = $(".fluentform-step");
      const activeStep = document.querySelector(".fluentform-step.active");
      let lastStep = jQuery(steps[steps.length - 1]);

      if (activeStep) {
        let firstStep = $(steps[0]);
        // Find the "Previous" button inside that active step
        const prevButton = activeStep.querySelector(".ff-btn-prev");

        if (lastStep.hasClass("active") || lastStep.is(":visible")) {
          prevButton.click();
          jQuery(prevButton).addClass("back-clicked");
        }

        if (firstStep.hasClass("active") || firstStep.is(":visible")) {
          window.location.href = hldClassNavData.homeUrl;
          return;
        }

        if (prevButton) {
          prevButton.click(); // Trigger FluentForm's previous step
        } else {
          console.warn("⚠️ No .ff-btn-prev found inside active step.");
          if (
            typeof ldClassNavData !== "undefined" &&
            hldClassNavData.homeUrl
          ) {
            window.location.href = hldClassNavData.homeUrl;
          }
        }
      } else {
        console.warn("⚠️ No active .fluentform-step found.");
        if (typeof hldClassNavData !== "undefined" && hldClassNavData.homeUrl) {
          window.location.href = hldClassNavData.homeUrl;
        }
      }
    });
  }

  showNextButtonAfterSelectSelection() {
    const steps = document.querySelectorAll(".fluentform-step");

    steps.forEach((step) => {
      // ⛔ Skip DOB step (fluentform-step + hld_dob_wrap combo)
      if (step.classList.contains("hld_dob_wrap")) {
        return;
      }

      const select = step.querySelector("select");
      const nextBtn = step.querySelector('button[data-action="next"]');

      if (select && nextBtn) {
        // Helper function to show the button
        const showBtn = () => {
          nextBtn.classList.remove("hld-hidden");
          nextBtn.style.visibility = "visible";
          nextBtn.style.display = "block";
        };

        // 1️⃣ Show if already selected
        if (select.value.trim() !== "") {
          showBtn();
        }

        // 2️⃣ Show on change
        select.addEventListener("change", function () {
          if (select.value.trim() !== "") {
            showBtn();
          }
        });
      }
    });
  }

  hideNextBtnOnDOB() {
    // Select the wrapper
    const dobWrap = document.querySelector(".hld_dob_wrap");
    if (!dobWrap) return;

    // Find the Next button inside it
    const nextBtn = dobWrap.querySelector('[data-action="next"]');

    // Hide it
    if (nextBtn) {
      nextBtn.style.display = "none";
      nextBtn.style.visibility = "hidden";
    }
  }

  //   showNextBtnOnDOB() {
  //     const dobWrap = document.querySelector('.hld_dob_wrap');
  //     if (!dobWrap) return;
  //     const nextBtn = dobWrap.querySelector('[data-action="next"]');
  //     if (nextBtn) {
  //         nextBtn.style.display = "";
  //         nextBtn.style.visibility = "visible";
  //     }
  // }

  disqualifyLessThan18(date) {
    // return;
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
        console.log("if statement called 247");
        console.log(age);
        console.log("nextBtn", nextBtn);
        console.log("disqualifySection", disqualifySection);

        if (nextBtn) {
          nextBtn.style.visibility = "visible";
          nextBtn.style.display = "block";
        }
        if (disqualifySection) disqualifySection.style.display = "none";
      } else {
        console.log("else is called");
        console.log("nextBtn", nextBtn);
        if (nextBtn) {
          console.log("none is called 255");
          nextBtn.style.display = "none";
        }
        if (nextBtn) {
          console.log("hidden is called 259");
          nextBtn.style.visibility = "hidden";
        }
        console.log("nextBtn after", nextBtn);

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

  hideLayoutIfForm() {
    const formWrap = document.querySelector(".hld_form_wrap");

    if (formWrap) {
      // Hide Elementor header
      const header = document.querySelector("header.elementor");
      if (header) header.style.display = "none";

      // Hide footer
      const footer = document.querySelector("footer");
      if (footer) footer.style.display = "none";

      // Adjust container style
      const container = document.querySelector(".ast-container");
      if (container) {
        container.style.minHeight = "100vh";
        container.style.background = "#f7f5f5";
      }
    }
  }

  showNextButtonOnRadioSelection() {
    const formWrap = document.querySelector(".hld_form_wrap");
    if (!formWrap) return;

    const steps = formWrap.querySelectorAll(".fluentform-step");

    steps.forEach((step) => {
      const radioInputs = step.querySelectorAll('input[type="radio"]');
      const nextBtn = step.querySelector('button[data-action="next"]');

      if (radioInputs.length && nextBtn) {
        // Helper to show button
        const showBtn = () => {
          nextBtn.style.display = "block";
          nextBtn.style.visibility = "visible";
          nextBtn.classList.remove("hld-hidden");
        };

        // If one is already selected (page reload case)
        const alreadyChecked = step.querySelector(
          'input[type="radio"]:checked'
        );
        if (alreadyChecked) showBtn();

        // Add click listeners
        radioInputs.forEach((radio) => {
          radio.addEventListener("click", () => showBtn());
        });
      }
    });
  }

  initActionItemSidebar() {
    if (!hldActionItem.glp1Prefunnel || hldActionItem.glp1Prefunnel == "0")
      return;

    // If on specific pages or if ?upload-id is present in query string, stop

    if (
      window.location.href.includes("glp-1-form") ||
      window.location.href.includes("glp-1-weight-loss-intake") ||
      window.location.href.includes("trt-prefunnel") ||
      window.location.href.includes("pt-141-prefunnel") ||
      window.location.href.includes("nad-therapy") ||
      window.location.href.includes("upload-id")
    ) {
      return;
    }

    // also do not show action item on any form page

    if (document.querySelector(".hld_form_wrap")) {
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
        // this.checkNextEndLoginAndNavigate();
        // hldFormHandler.getAmount();
        // hldFormHandler.setStripeData();
      }
    });

    // Watch for style attribute changes
    observer.observe(loginWrap, {
      attributes: true,
      attributeFilter: ["style"],
    });

    console.log("Login wrap listener initialized.");
  }

  // toggleLoader(show) {
  //   let loader = document.getElementById("global-loading-overlay");

  //   console.log("toggleLoader called");
  //   // Create overlay if it doesn't exist
  //   if (!loader) {
  //     loader = document.createElement("div");
  //     loader.id = "global-loading-overlay";

  //     // Inline CSS for overlay + loading bar
  //     loader.style.position = "fixed";
  //     loader.style.top = "0";
  //     loader.style.left = "0";
  //     loader.style.width = "100%";
  //     loader.style.height = "100%";
  //     loader.style.background = "rgba(0, 0, 0, 0.55)";
  //     loader.style.backdropFilter = "blur(2px)";
  //     loader.style.display = "flex";
  //     loader.style.alignItems = "center";
  //     loader.style.justifyContent = "center";
  //     loader.style.zIndex = "999999";
  //     loader.style.pointerEvents = "auto";

  //     // Loading bar container
  //     const barWrapper = document.createElement("div");
  //     barWrapper.style.width = "250px";
  //     barWrapper.style.height = "6px";
  //     barWrapper.style.background = "rgba(255, 255, 255, 0.2)";
  //     barWrapper.style.borderRadius = "10px";
  //     barWrapper.style.overflow = "hidden";

  //     // Animated loading bar
  //     const bar = document.createElement("div");
  //     bar.id = "loader-bar";
  //     bar.style.width = "0%";
  //     bar.style.height = "100%";
  //     bar.style.background = "white";
  //     bar.style.borderRadius = "10px";
  //     bar.style.animation = "loaderBarAnim 10s ease-in-out infinite";

  //     barWrapper.appendChild(bar);
  //     loader.appendChild(barWrapper);

  //     // Add keyframes via JS
  //     // const styleSheet = document.createElement("style");
  //     // styleSheet.innerHTML = `
  //     //       @keyframes loaderBarAnim {
  //     //           0%   { width: 0%; transform: translateX(0); }
  //     //           50%  { width: 80%; transform: translateX(20%); }
  //     //           100% { width: 0%; transform: translateX(100%); }
  //     //       }
  //     //   `;
  //     // console.log(styleSheet);
  //     // console.log(loader);
  //     // document.head.appendChild(styleSheet);

  //     document.body.appendChild(loader);
  //   }

  //   // Show overlay
  //   if (show === true) {
  //     loader.style.display = "flex";
  //     document.body.style.pointerEvents = "none"; // Disable page clicks
  //   } else {
  //     loader.style.display = "none";
  //     document.body.style.pointerEvents = "auto"; // Enable page clicks
  //   }
  // }

  toggleLoader(show) {
    let loader = document.getElementById("global-loading-overlay");

    console.log("toggleLoader called");
    // Create overlay if it doesn't exist
    if (!loader) {
      loader = document.createElement("div");
      loader.id = "global-loading-overlay";

      // Inline CSS for overlay + loading bar
      loader.style.position = "fixed";
      loader.style.top = "0";
      loader.style.left = "0";
      loader.style.width = "100%";
      loader.style.height = "6px";
      loader.style.background = "white";
      // loader.style.backdropFilter = "blur(2px)";
      loader.style.display = "flex";
      loader.style.alignItems = "center";
      loader.style.justifyContent = "center";
      loader.style.zIndex = "999999";
      loader.style.pointerEvents = "auto";

      // Loading bar container
      const barWrapper = document.createElement("div");
      barWrapper.style.width = "100%";
      barWrapper.style.height = "6px";
      barWrapper.style.background = "rgba(255, 255, 255, 0.2)";
      barWrapper.style.borderRadius = "10px";
      barWrapper.style.overflow = "hidden";

      // Animated loading bar
      const bar = document.createElement("div");
      bar.id = "loader-bar";
      bar.style.width = "0%";
      bar.style.height = "100%";
      bar.style.background = "var(--hld-color-primary)";
      bar.style.borderRadius = "10px";
      bar.style.animation = "loaderBarAnim 7s ease-in-out infinite";

      barWrapper.appendChild(bar);
      loader.appendChild(barWrapper);

      document.body.appendChild(loader);
    }

    // Show overlay
    if (show === true) {
      loader.style.display = "flex";
      document.body.style.pointerEvents = "none"; // Disable page clicks
    } else {
      loader.style.display = "none";
      document.body.style.pointerEvents = "auto"; // Enable page clicks
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
  // checkNextEndLoginAndNavigate() {
  //   console.log("function checkNextEndLoginAndNavigate is working");
  //
  //   // Parse the current URL parameters
  //   const urlParams = new URLSearchParams(window.location.search);
  //   const isNextendLoginComplete =
  //     urlParams.get("nextend_login_complete") === "1";
  //
  //   // Check both conditions before proceeding
  //   // if (this.isUserLoggedIn() && isNextendLoginComplete) {
  //   if (this.isUserLoggedIn()) {
  //     const stepElement = document.querySelector(".hld_login_wrap");
  //
  //     if (stepElement) {
  //       const nextButton = stepElement.querySelector(
  //         'button[data-action="next"]',
  //       );
  //       const prevButton = jQuery(".ff-btn-prev");
  //
  //       if (nextButton && !prevButton.hasClass("back-clicked")) {
  //         console.log("nextButton found:", nextButton);
  //         console.log("Will click FluentForm next button in 4s...");
  //
  //         setTimeout(() => {
  //           console.log("button is clicked 4564", nextButton);
  //
  //           nextButton.click();
  //           console.log("Clicked FluentForm next button!");
  //         }, 10); // small delay to allow DOM updates
  //       }
  //     }
  //   } else {
  //     console.log(
  //       "Conditions not met. Either user is not logged in or nextend_login_complete != 1",
  //     );
  //   }
  // }
}

// Initialize only when DOM is ready
document.addEventListener("DOMContentLoaded", () => {
  window.hldNavigation = new HldNavigation();
});
