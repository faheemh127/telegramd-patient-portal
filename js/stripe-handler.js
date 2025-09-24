class hldStripeHandler {
  constructor(config) {
    this.publishableKey = config.publishableKey;
    this.ajaxUrl = config.ajaxUrl;
    this.formId = config.formId || "fluentform_45";
    this.cardElementId = config.cardElementId || "card-element";
    this.errorElementId = config.errorElementId || "card-errors";
    this.paymentButtonId = config.paymentButtonId || "hdlMakeStipePayment";
    this.submitWrapperClass =
      config.submitWrapperClass || ".hld_form_main_submit_button";
    this.prButtonId = config.prButtonId || "payment-request-button"; // NEW: container for Google Pay button

    this.stripe = null;
    this.elements = null;
    this.card = null;
    this.chargeImmediately = true; // default: false

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

    // ✅ Card Element (keep existing flow)
    this.card = this.elements.create("card");

    const cardElement = document.getElementById(this.cardElementId);
    if (cardElement) {
      this.card.mount(`#${this.cardElementId}`);
    } else {
      console.warn(`Card element with ID "${this.cardElementId}" not found.`);
    }

    const amount = hldFormHandler.getAmount();
    // ✅ Google Pay / Apple Pay (Payment Request Button)
    const paymentRequest = this.stripe.paymentRequest({
      country: "US", // <-- change to your country
      currency: "usd", // <-- change to your currency
      total: {
        label: "Prescription Payment",
        amount: amount * 100, // 0 for now since we’re only saving method, not charging
      },
      requestPayerName: true,
      requestPayerEmail: true,
    });

    const prButton = this.elements.create("paymentRequestButton", {
      paymentRequest,
      style: {
        paymentRequestButton: {
          type: "default",
          theme: "dark",
          height: "40px",
        },
      },
    });

    // Check if Google/Apple Pay is available
    paymentRequest.canMakePayment().then((result) => {
      if (result) {
        prButton.mount(`#${this.prButtonId}`);
        console.log("Google Pay / Apple Pay is available");
      } else {
        console.log("Google Pay / Apple Pay not available on this device.");
      }
    });

    // Handle payment method selection
    paymentRequest.on("paymentmethod", async (ev) => {
      try {
        const setupIntent = await this.createSetupIntent();

        if (!setupIntent.success) {
          ev.complete("fail");
          this.showError("Failed to create SetupIntent");
          return;
        }

        const { clientSecret, customerId } = setupIntent.data;

        // Confirm setup with Google Pay method
        const { error, setupIntent: confirmedIntent } =
          await this.stripe.confirmCardSetup(clientSecret, {
            payment_method: ev.paymentMethod.id,
          });

        if (error) {
          ev.complete("fail");
          this.showError(error.message);
          return;
        }

        ev.complete("success");

        // Save method in backend
        const saveResult = await this.savePaymentMethod(
          customerId,
          confirmedIntent.payment_method
        );

        if (!saveResult.success) {
          this.showError("Failed to save Google Pay method.");
          return;
        }

        console.log("Google Pay method saved successfully!");
        this.submitForm();
      } catch (err) {
        console.error("Error in Google Pay flow:", err);
        ev.complete("fail");
        this.showError("Something went wrong with Google Pay.");
      }
    });
  }

  bindEvents() {
    const form = document.getElementById(this.formId);
    this.errorDisplay = document.getElementById(this.errorElementId);
    this.paymentButton = document.getElementById(this.paymentButtonId);

    if (!form || !this.paymentButton) {
      console.warn("Form or payment button not found.");
      return;
    }

    this.paymentButton.addEventListener("click", (e) =>
      this.handleCardPayment(e)
    );
  }

  // ✅ Existing card flow
  async handleCardPayment(e) {
    e.preventDefault();
    console.log("hdlMakeStipePayment button clicked");

    this.toggleButtonState(true, "Processing...");

    try {
      const setupIntent = await this.createSetupIntent();

      if (!setupIntent.success) {
        this.showError("Error creating SetupIntent.");
        this.toggleButtonState(false, "Save and Continue");
        return;
      }

      const { clientSecret, customerId } = setupIntent.data;
      const customerName = hldFormHandler.getFullNameFromContainer();

      const result = await this.stripe.confirmCardSetup(clientSecret, {
        payment_method: {
          card: this.card,
          billing_details: {
            name: customerName, // TODO: replace with dynamic user info
          },
        },
      });

      if (result.error) {
        this.errorDisplay.textContent = result.error.message;
        this.toggleButtonState(false, "Save and Continue");
        return;
      }

      const paymentMethod = result.setupIntent.payment_method;

      // Check the flag
      if (this.chargeImmediately) {
        // Charge immediately using PaymentIntent (instead of just saving method)

        // Charge immediately using PaymentIntent
        const amount = hldFormHandler.getAmount();
        const chargeResult = await fetch(MyStripeData.ajax_url, {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: `action=charge_now&customer_id=${encodeURIComponent(
            setupIntent.data.customerId
          )}&payment_method=${encodeURIComponent(
            paymentMethod
          )}&amount=${encodeURIComponent(amount)}`,
        });

        const chargeResponse = await chargeResult.json();

        if (!chargeResponse.success) {
          this.showError(
            "Failed to charge the card: " + (chargeResponse.data?.message || "")
          );
          this.toggleButtonState(false, "Save and Continue");
          return;
        }

        console.log(
          "Payment charged immediately! PaymentIntent ID:",
          chargeResponse.data.payment_intent
        );
      } else {
        // Just save for later
        const saveResult = await this.savePaymentMethod(
          setupIntent.data.customerId,
          paymentMethod
        );

        if (!saveResult.success) {
          this.showError("Error saving card.");
          this.toggleButtonState(false, "Save and Continue");
          return;
        }

        console.log("Card saved for later!");
      }

      // const saveResult = await this.savePaymentMethod(
      //   customerId,
      //   paymentMethod
      // );

      // if (!saveResult.success) {
      //   this.showError("Error saving card.");
      //   this.toggleButtonState(false, "Save and Continue");
      //   return;
      // }
      // console.log("Card saved successfully!");

      this.submitForm();
    } catch (error) {
      console.error("Error during card payment handling:", error);
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
  prButtonId: "payment-request-button", // NEW: add this container in HTML
});

// customer name
// payment description
// {"success":true,"data":{"payment_intent":"pi_3S9TwZAcgi1hKyLW1pgH2X4Y"}} show payment_id in customer detail
