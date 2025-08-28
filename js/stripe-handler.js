class hldStripeHandler {
  constructor(config) {
    this.publishableKey = config.publishableKey;
    this.ajaxUrl = config.ajaxUrl;
    this.formId = config.formId || "fluentform_13";
    this.cardElementId = config.cardElementId || "card-element";
    this.errorElementId = config.errorElementId || "card-errors";
    this.paymentButtonId = config.paymentButtonId || "hdlMakeStipePayment";
    this.submitWrapperClass = config.submitWrapperClass || ".hld_form_main_submit_button";

    this.stripe = null;
    this.elements = null;
    this.card = null;

    this.init();
  }

  init() {
    console.log("hldStripeHandler initialized 🚀");

    document.addEventListener("DOMContentLoaded", () => {
      this.setupStripe();
      this.bindEvents();
    });
  }

  setupStripe() {
    this.stripe = Stripe(this.publishableKey);
    this.elements = this.stripe.elements();
    this.card = this.elements.create("card");
    this.card.mount(`#${this.cardElementId}`);
  }

  bindEvents() {
    const form = document.getElementById(this.formId);
    this.errorDisplay = document.getElementById(this.errorElementId);
    this.paymentButton = document.getElementById(this.paymentButtonId);

    if (!form || !this.paymentButton) {
      console.warn("Form or payment button not found.");
      return;
    }

    this.paymentButton.addEventListener("click", (e) => this.handlePayment(e));
  }

  async handlePayment(e) {
    e.preventDefault();
    console.log("hdlMakeStipePayment button clicked");

    this.toggleButtonState(true, "Processing...");

    try {
      const setupIntent = await this.createSetupIntent();

      if (!setupIntent.success) {
        this.showError(
          setupIntent.data?.message ||
            "An error occurred while creating setup intent."
        );
        this.toggleButtonState(false, "Save and Continue");
        return;
      }

      const { clientSecret, customerId } = setupIntent.data;

      const result = await this.stripe.confirmCardSetup(clientSecret, {
        payment_method: {
          card: this.card,
          billing_details: {
            name: "John Doe", // TODO: replace with dynamic user name
          },
        },
      });

      if (result.error) {
        this.errorDisplay.textContent = result.error.message;
        this.toggleButtonState(false, "Save and Continue");
        return;
      }

      const paymentMethod = result.setupIntent.payment_method;

      const saveResult = await this.savePaymentMethod(customerId, paymentMethod);

      if (!saveResult.success) {
        this.showError(
          saveResult.data?.message || "An error occurred. Please try again."
        );
        this.toggleButtonState(false, "Save and Continue");
        return;
      }

      console.log("Payment method saved successfully!");

      this.submitForm();
    } catch (error) {
      console.error("Error during payment handling:", error);
      this.showError("Something went wrong. Please try again.");
      this.toggleButtonState(false, "Save and Continue");
    }
  }

  async createSetupIntent() {
    const response = await fetch(this.ajaxUrl, {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: "action=create_setup_intent",
    });
    return await response.json();
  }

  async savePaymentMethod(customerId, paymentMethod) {
    const response = await fetch(this.ajaxUrl, {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: `action=save_later_payment_method&customer_id=${customerId}&payment_method=${paymentMethod}`,
    });
    return await response.json();
  }

  submitForm() {
    const submitWrapper = document.querySelector(this.submitWrapperClass);
    if (submitWrapper) {
      const submitButton = submitWrapper.querySelector('button[type="submit"]');
      if (submitButton) {
        submitButton.click();
      }
    }
  }

  toggleButtonState(disabled, text) {
    if (this.paymentButton) {
      this.paymentButton.disabled = disabled;
      if (text) this.paymentButton.textContent = text;
    }
  }

  showError(message) {
    if (this.errorDisplay) {
      this.errorDisplay.textContent = message;
    } else {
      alert(message);
    }
  }
}

// Usage
const stripeHandler = new hldStripeHandler({
  publishableKey: MyStripeData.publishableKey,
  ajaxUrl: MyStripeData.ajax_url,
  formId: "fluentform_24",
  cardElementId: "card-element",
  errorElementId: "card-errors",
  paymentButtonId: "hdlMakeStipePayment",
  submitWrapperClass: ".hld_form_main_submit_button",
});
