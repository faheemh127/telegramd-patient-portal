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

    // Automatically send email to iframes if available
    if (window.location.href.includes("my-account")) {
      this.postEmailToIframes();
    }

    this.setUpPatientTypeListeners();
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

  setUpPatientTypeListeners() {
    const mapping = {
      hld_nav_conversations: "clinical",
      hld_nav_support: "support",
      hld_nav_billing: "billing",
    };

    document.querySelectorAll("ul.container li").forEach((li) => {
      li.addEventListener("click", () => {
        for (const [className, value] of Object.entries(mapping)) {
          if (li.classList.contains(className)) {
            window.hldPatientType = value;
            console.log(`Patient type set to: ${value}`);
            break;
          }
        }
      });
    });
  }

  postEmailToIframes() {
    // Ensure we have patient email available
    console.log("function postEmail.... is called");
    if (!window.hldPatientEmail) {
      console.warn("⚠️ hldPatientEmail is not defined.");
      return;
    }

    // Define iframe configurations
    const iframes = [
      { id: "chat-clinical", type: "clinical" },
      { id: "chat-support", type: "support" },
      { id: "chat-billing", type: "billing" },
    ];

    console.log("iframes", iframes);
    iframes.forEach(({ id, type }) => {
      const iframe = document.getElementById(id);

      if (!iframe) {
        console.warn(`⚠️ iframe with id="${id}" not found.`);
        return;
      }
      console.log(window);
      // Send message when iframe is loaded
      iframe.addEventListener("load", () => {
        const message = {
          hldPatientEmail: window.hldPatientEmail,
          hldPatientType: type,
        };

        iframe.contentWindow.postMessage(message, "https://healsend.com");
        console.log(`✅ Sent to ${id}:`, message);
      });
    });
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
