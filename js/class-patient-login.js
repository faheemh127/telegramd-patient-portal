class HldPatientLogin {
  constructor() {
    this.usernameField = document.querySelector(".hld-username");
    this.passwordField = document.querySelector(".hld-password");
    this.loginBtn = document.querySelector(".hld-login-btn");
    this.saveBtn = document.getElementById("hld_save_account_details");
    this.form = document.getElementById("hld-account-details-form");
    this.msgSpan = document.getElementById("hld_account_details_message");
    this.revokeButton = document.getElementById("hld-revoke-sub");

    if (this.loginBtn) {
      this.loginBtn.addEventListener("click", (e) => {
        e.preventDefault();
        // Disable the button
        this.loginBtn.disabled = true;
        this.loginBtn.innerText = "Logging in..."; // optional feedback
        this.login();
      });
    }

    if (this.saveBtn) {
      this.saveBtn.addEventListener("click", () => this.saveAccountDetails());
    }

    // 🔹 Initialize Edit Profile button logic
    this.editProfileBtn = document.getElementById("hldBtnEditProfile");
    if (this.editProfileBtn) {
      this.editProfileBtn.addEventListener("click", () =>
        this.enableProfileEditing()
      );
    }

    // Automatically send email to iframes if available
    if (window.location.href.includes("my-account")) {
      this.postEmailToIframes();
    }

    if (!this.revokeButton) {
      console.warn("Revoke button not found.");
    } else {
      this.revokeButton.addEventListener("click", (e) =>
        this.handleRevokeSub(e)
      );
    }

    this.setUpPatientTypeListeners();
  }

  //  Existing sub revoke
  async handleRevokeSub(e) {
    e.preventDefault();

    let target = e.target;
    let nonce = target.getAttribute("sub-nonce");
    let data = target.getAttribute("data");

    const response = await fetch(MyPatientLogin.ajaxurl, {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: `action=revoke_patient_subscription&nonce=${nonce}&data=${data}`,
    });
    return await response.json();
  }

  saveAccountDetails() {
    console.log("saveAccountDetails called");
    if (!this.form || !this.saveBtn || !this.msgSpan) return;

    this.msgSpan.style.display = "none";
    this.saveBtn.disabled = true;

    const data = {
      action: "hld_update_account_details", // required for admin-ajax.php
      full_name: this.form.full_name.value,
      email: this.form.email.value,
      phone: this.form.phone.value,
      dob: this.form.dob.value,
      _ajax_nonce: MyPatientLogin.nonce, // Optional if you localize nonce
    };

    fetch(MyPatientLogin.ajaxurl, {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: new URLSearchParams(data).toString(),
    })
      .then((res) => res.json())
      .then((res) => {
        this.saveBtn.disabled = false;
        this.msgSpan.style.display = "inline-block";
        if (res.success) {
          this.msgSpan.textContent = "Account details updated successfully.";
          this.msgSpan.style.color = "green";
        } else {
          this.msgSpan.textContent = res.message || "Error updating details.";
          this.msgSpan.style.color = "red";
        }
      })
      .catch(() => {
        this.saveBtn.disabled = false;
        this.msgSpan.style.display = "inline-block";
        this.msgSpan.textContent = "Error updating details.";
        this.msgSpan.style.color = "red";
      });
  }

  enableProfileEditing() {
    // List of editable fields
    const fields = [
      {
        id: "hld_full_name",
        type: "text",
        displayId: "hldPatientProfileFullName",
      },
      { id: "hld_email", type: "email", displayId: "hldPatientProfileEmail" },
      { id: "hld_phone", type: "text", displayId: "hldPatientProfilePhone" },
      { id: "hld_dob", type: "date", displayId: "hldPatientProfileDOB" },
    ];

    fields.forEach((field) => {
      const input = document.getElementById(field.id);
      const displayEl = document.getElementById(field.displayId);

      // Hide <p> display value if visible
      if (displayEl) displayEl.style.display = "none";

      // Convert hidden input to editable
      if (input && input.type === "hidden") {
        input.type = field.type;
        input.setAttribute("data-editable", "true");
        input.style.display = "block";
      }
    });

    // Show Save button
    const saveBtn = document.getElementById("hld_save_account_details");
    if (saveBtn) {
      saveBtn.style.display = "inline-block";
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
      url: MyPatientLogin.ajaxurl,
      type: "POST",
      data: {
        action: "hld_patient_login",
        username: username,
        password: password,
        nonce: MyPatientLogin.nonce,
      },
      success: (response) => {
        if (response.success) {
          // Reload with parameter ?login=success
          window.location.href = window.location.pathname + "?login=success";
        } else {
          alert(response.data || "Login failed. Please try again.");
        }
        hldPatientLogin.loginBtn.disabled = false;
        hldPatientLogin.loginBtn.innerText = "Login";
      },

      error: () => {
        alert("Something went wrong. Please try again.");
        hldPatientLogin.loginBtn.disabled = false;
        hldPatientLogin.loginBtn.innerText = "Login";
      },
    });
  }
}

// Initialize
document.addEventListener("DOMContentLoaded", () => {
  window.hldPatientLogin = new HldPatientLogin();
});
