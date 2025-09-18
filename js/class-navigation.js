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
  }

  hideNextBtnDisqualifyStep() {
    const disqualifySteps = document.querySelectorAll(".hld_disqualify_step");

    disqualifySteps.forEach(function (step) {
      const nextBtn = step.querySelector("button.ff-btn-next");
      if (nextBtn) {
        nextBtn.style.display = "none";
      }
    });
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

    this.showActionItemSidebar();

    // Close sidebar on button click
    closeBtn.addEventListener("click", () => {
      sidebar.classList.remove("active");
      setTimeout(() => overlay.classList.remove("active"), 300);
    });

    // Close sidebar on overlay click (outside click)
    overlay.addEventListener("click", (e) => {
      if (e.target === overlay) {
        sidebar.classList.remove("active");
        setTimeout(() => overlay.classList.remove("active"), 300);
      }
    });
  }

  showActionItemSidebar() {
    const overlay = document.getElementById("hldSidebarOverlay");
    const sidebar = document.getElementById("hldSidebar");

    overlay.classList.add("active");
    sidebar.classList.add("active");
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
