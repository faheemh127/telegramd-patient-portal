class HldPatientLogin {
  constructor() {
    this.usernameField = document.querySelector(".hld-username");
    this.passwordField = document.querySelector(".hld-password");
    this.loginBtn = document.querySelector(".hld-login-btn");

    if (this.loginBtn) {
      this.loginBtn.addEventListener("click", (e) => {
        e.preventDefault();
        // Disable the button
        this.loginBtn.disabled = true;
        this.loginBtn.innerText = "Logging in..."; // optional feedback
        this.login();
      });
    }
  }

  postEmailToIframe() {
    // Example: send hldPatientEmail to the iframe
    const iframe = document.querySelector("iframe");
    iframe.onload = () => {
      iframe.contentWindow.postMessage(
        { hldPatientEmail: window.hldPatientEmail },
        "https://healsend.com" // target origin (must match iframe's origin)
      );
    };
  }

  login() {
    const username = this.usernameField.value.trim();
    const password = this.passwordField.value.trim();

    // Validation
    if (!username || !password) {
      alert("Username and password are required.");
      return;
    }

    // AJAX call
    jQuery.ajax({
      url: hld_ajax_obj.ajaxurl,
      type: "POST",
      data: {
        action: "hld_patient_login",
        username: username,
        password: password,
        nonce: hld_ajax_obj.nonce,
      },
      success: (response) => {
        if (response.success) {
          // Reload with parameter ?login=success
          window.location.href = window.location.pathname + "?login=success";
        } else {
          alert(response.data || "Login failed. Please try again.");
        }
      },

      error: () => {
        alert("Something went wrong. Please try again.");
      },
    });

    this.loginBtn.disabled = false;
    this.loginBtn.innerText = "Login";
  }
}

// Initialize
document.addEventListener("DOMContentLoaded", () => {
  new HldPatientLogin();
});
