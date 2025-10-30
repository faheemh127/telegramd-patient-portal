class hldStripeHandler {
  constructor(config) {
    this.publishableKey = config.publishableKey;
    this.ajaxUrl = config.ajaxUrl;
    this.formId = config.formId || "fluentform_45";
    this.cardElementId = config.cardElementId || "card-element";
    this.errorElementId = config.errorElementId || "card-errors";
    this.paymentButtonId = config.paymentButtonId || "hdlMakeStipePayment";
    this.prButtonId = config.prButtonId || "payment-request-button"; // NEW: container for Google Pay button
    this.stripe = null;
    this.elements = null;
    this.card = null;
    this.chargeImmediately = true; // default: false
    this.isSubscription = true; // <-- set to true for subscription flow
    this.stripePriceId = ""; // dummy Price ID for testing
    this.telegraProdID = ""; // dummy Price ID for testing
    this.gl1Duration = 1; // 1 is default
    this.init();
    this.submitWrapperClass =
      config.submitWrapperClass || ".hld_form_main_submit_button";
  }

  init() {
    document.addEventListener("DOMContentLoaded", () => {
      this.setupStripe();
      this.bindEvents();
    });
  }

  async fetchStripePrice() {
    if (!this.stripePriceId) {
      console.warn("Stripe Price ID is missing.");
      return null;
    }

    try {
      const response = await fetch(this.ajaxUrl, {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: new URLSearchParams({
          action: "hld_get_stripe_price",
          price_id: this.stripePriceId,
        }),
      });

      const result = await response.json();
      if (result.success && result.data) {
        console.log(" Stripe price fetched:", result.data);
        return result.data; // { amount, currency, interval }
      } else {
        console.error("Error fetching Stripe price:", result.data?.message);
        return null;
      }
    } catch (err) {
      console.error("Error fetching Stripe price:", err);
      return null;
    }
  }

  async setupStripe() {
    this.stripe = Stripe(this.publishableKey);
    this.elements = this.stripe.elements();

    //  Card Element (keep existing flow)
    this.card = this.elements.create("card");

    const cardElement = document.getElementById(this.cardElementId);
    if (cardElement) {
      this.card.mount(`#${this.cardElementId}`);
    } else {
      console.warn(`Card element with ID "${this.cardElementId}" not found.`);
    }

    // const amount = hldFormHandler.getAmount();

    const priceData = await this.fetchStripePrice();
    if (!priceData) {
      console.warn("Using fallback amount 0 since price fetch failed.");
    }

    const amount = priceData?.amount || 0;
    const currency = priceData?.currency?.toLowerCase() || "usd";

    const paymentRequest = this.stripe.paymentRequest({
      country: "US",
      currency: currency,
      total: {
        label: "Prescription Subscription",
        amount: amount * 100,
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
        // console.log("Google Pay / Apple Pay is available");
      } else {
        console.log("Google Pay / Apple Pay not available on this device.");
      }
    });

    // // Handle payment method selection
    // paymentRequest.on("paymentmethod", async (ev) => {
    //   try {
    //     const setupIntent = await this.createSetupIntent();

    //     if (!setupIntent.success) {
    //       ev.complete("fail");
    //       this.showError("Failed to create SetupIntent");
    //       return;
    //     }

    //     const { clientSecret, customerId } = setupIntent.data;

    //     // Confirm setup with Google Pay method
    //     const { error, setupIntent: confirmedIntent } =
    //       await this.stripe.confirmCardSetup(clientSecret, {
    //         payment_method: ev.paymentMethod.id,
    //       });

    //     if (error) {
    //       ev.complete("fail");
    //       this.showError(error.message);
    //       return;
    //     }

    //     ev.complete("success");

    //     // Save method in backend
    //     const saveResult = await this.savePaymentMethod(
    //       customerId,
    //       confirmedIntent.payment_method
    //     );

    //     if (!saveResult.success) {
    //       this.showError("Failed to save Google Pay method.");
    //       return;
    //     }

    //     console.log("Google Pay method saved successfully!");
    //     this.submitForm();
    //   } catch (err) {
    //     console.error("Error in Google Pay flow:", err);
    //     ev.complete("fail");
    //     this.showError("Something went wrong with Google Pay.");
    //   }
    // });

    // Google Pay / Apple Pay Charge
    paymentRequest.on("paymentmethod", async (ev) => {
      try {
        //  Create subscription directly
        const subResult = await fetch(MyStripeData.ajax_url, {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: `action=subscribe_patient&payment_method=${encodeURIComponent(
            ev.paymentMethod.id
          )}&price_id=${encodeURIComponent(
            this.stripePriceId
          )}&duration=${encodeURIComponent(this.gl1Duration)}`,
        });

        const subResponse = await subResult.json();

        if (!subResponse.success) {
          ev.complete("fail");
          this.showError(
            "Failed to create subscription: " +
              (subResponse.data?.message || "")
          );
          return;
        }

        console.log(" Subscription created:", subResponse.data);

        ev.complete("success");
        this.submitForm();
      } catch (err) {
        console.error("Error in Google/Apple Pay subscription flow:", err);
        ev.complete("fail");
        this.showError("Something went wrong with Google/Apple Pay.");
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

  //  Existing card flow
  async handleCardPayment(e) {
    e.preventDefault();

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

      if (this.isSubscription) {
        // Call subscription AJAX instead of charge_now
        const subResult = await fetch(MyStripeData.ajax_url, {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: `action=subscribe_patient&customer_id=${encodeURIComponent(
            setupIntent.data.customerId
          )}&payment_method=${encodeURIComponent(
            paymentMethod
          )}&price_id=${encodeURIComponent(
            this.stripePriceId
          )}&duration=${encodeURIComponent(this.gl1Duration)}`,
        });

        const subResponse = await subResult.json();

        if (!subResponse.success) {
          this.showError(
            "Failed to create subscription: " +
              (subResponse.data?.message || "")
          );
          this.toggleButtonState(false, "Save and Continue");
          return;
        }

        console.log(" Subscription created:", subResponse.data);
      } else if (this.chargeImmediately) {
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

const isLocalhost =
  window.location.hostname === "localhost" ||
  window.location.hostname === "127.0.0.1" ||
  window.location.hostname === "::1";

// Check if connection is secure (HTTPS)
const isSecure = window.location.protocol === "https:";
// Usage
let glp1FormID;
if (isLocalhost) {
  glp1FormID = 45;
} else {
  glp1FormID = Number(MyStripeData.prefunnelFormId);
}

const cardElement = document.querySelector("#card-element");

if (cardElement) {
  window.stripeHandler = new hldStripeHandler({
    publishableKey: MyStripeData.publishableKey,
    ajaxUrl: MyStripeData.ajax_url,
    formId: "fluentform_" + glp1FormID,
    cardElementId: "card-element",
    errorElementId: "card-errors",
    paymentButtonId: "hdlMakeStipePayment",
    submitWrapperClass: ".hld_form_main_submit_button",
    prButtonId: "payment-request-button", // NEW: add this container in HTML
  });
  // console.log(" hldStripeHandler initialized!");
} else {
  // console.log("#card-element not found. Stripe handler not initialized.");
}
