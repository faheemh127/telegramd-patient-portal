class HldNavigation {
  constructor() {
    this.init();
  }

  // Initialize on page load
  init() {
    this.checkLoginAndNavigate();
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
      const stepElement = document.querySelector(
        '[data-name="step_start-24_24"]'
      );
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
          }, 100); // 1000ms = 1 second
        }
      }
    }
  }
}

// Initialize only when DOM is ready
document.addEventListener("DOMContentLoaded", () => {
  new HldNavigation();
});
