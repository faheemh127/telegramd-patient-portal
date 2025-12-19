class hldStripeHandler {
  constructor(config) {
    this.publishableKey = config.publishableKey;
    this.ajaxUrl = config.ajaxUrl;
    this.formId = config.formId || "fluentform_45";
    this.cardElementId = config.cardElementId || "card-element";
    this.errorElementId = config.errorElementId || "card-errors";
    this.paymentButtonId = config.paymentButtonId || "hdlMakeStipePayment";
    this.revokeButtonId = config.paymentButtonId || "hdlrevokeSub";
    this.prButtonId = config.prButtonId || "payment-request-button"; // NEW: container for Google Pay button
    this.klarnaBtnId = config.klarnaBtnId || "hldPayWithKlarna";
    this.afterPayBtnId = config.afterPayBtnId || "hldPayWithAP";
    this.stripe = null;
    this.elements = null;
    this.card = null;
    this.klarna = null;
    this.afterPay = null;
    this.klarnaButton = null;
    this.afterPayButton = null;
    this.chargeImmediately = true; // default: false
    this.isSubscription = false; // <-- set to true for subscription flow
    this.stripePriceId = ""; // dummy Price ID for testing
    this.telegraProdID = ""; // dummy Price ID for testing
    this.packageDuration = 1; // 1 is default
    this.promo = null;
    this.stripeData = null;
    this.init();
    this.submitWrapperClass =
      config.submitWrapperClass || ".hld_form_main_submit_button";
  }

  init() {
    document.addEventListener("DOMContentLoaded", () => {
      this.setupStripe();
      this.bindEvents();

      if (
        MyStripeData?.payment_intent_id &&
        MyStripeData.payment_intent_id.startsWith("pi_")
      ) {
        hldNavigation.toggleLoader(true);
        let input = document.querySelector(
          'input[name="my_stripe_subscription_id"]'
        );

        // If input doesn't exist, create it
        if (!input) {
          input = document.createElement("input");
          input.type = "hidden";
          input.name = "payment_intent_id";
          document.querySelector("form").appendChild(input); // append to the form
        }

        // Set the value
        input.value = MyStripeData.payment_intent_id;
        console.log("Hidden input value set:", input.value);
        setTimeout(function () {
          stripeHandler.submitForm();
        }, 4000);
      } else if (
        MyStripeData?.afterpay_klarna_payment &&
        MyStripeData.afterpay_klarna_payment == "failed"
      ) {
        // if this part executed means klarna or afterpay payment failed
        // now we need to display error message and do not submit the form
        const errMsg = MyStripeData.afterpay_klarna_msg;
        this.errorDisplay.textContent = errMsg;
      }

      // if ends
    });
  }

  /**
   * Both functions have similar functions but this function is used to get realtime price data and show in summary * so we directly show to customer that we are going to charge him
   * @param {*} priceId
   * @param {*} promoCode
   * @returns
   */
  async fetchStripePriceData(priceId, promoCode = null) {
    const payload = new URLSearchParams({
      action: "hld_get_stripe_price_data",
      price_id: priceId,
      promo_code: promoCode ? promoCode : "",
    });

    const response = await fetch(MyStripeData.ajax_url, {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: payload.toString(),
    });

    const data = await response.json();
    return data;
  }

  async getPriceData(priceId, promo) {
    if (this.stripeData != null) {
      return this.stripeData;
    }

    const stripePrice = await stripeHandler.fetchStripePriceData(
      priceId,
      promo
    );
    console.log("[Stripe Price Info**] ", stripePrice);
    this.stripeData = stripePrice;
    this.showSummary();
    return stripePrice;
  }

  showSummary() {
    const data = this.stripeData.data;
    const discountAmount = document.getElementById("hldDiscountAmount");
    const monthSupply = document.getElementById("hldOneMonthSupply");
    const dueWas = document.getElementById("hldDueWas");
    const finalPayable = document.getElementById("hldDueToday");

    // setup UI Elements
    if (data.discount_applied != 0) {
      discountAmount.classList.remove("hidden");
      hldDueWas.classList.remove("hidden");
      discountAmount.innerHTML = "$" + data.discount_display;
      dueWas.innerHTML = "$" + (data.original_amount / 100).toFixed(2);
    }

    monthSupply.innerHTML = (data.original_amount / 100).toFixed(2);
    dueWas.innerHTML = "$" + (data.original_amount / 100).toFixed(2);

    finalPayable.innerHTML =
      "$" + ((data.original_amount - data.discount_applied) / 100).toFixed(2);
  }

  getTelegraIdByValue(selectedValue) {
    // Find the div that matches the data-value
    const element = document.querySelector(
      `.hld-custom-checkbox.hld-medicine[data-value="${selectedValue}"]`
    );

    // If found, return its data-telegra-id
    return element ? element.getAttribute("data-telegra-id") : null;
  }
  isNewPatient() {
    return MyStripeData.isNewPatient;
  }

  setCheckoutImage(med) {
    const image = document.getElementById("checkoutImg");
    if (!image || !med?.image) {
      console.log(
        "image url and style or something else missing about checkout image"
      );
      return;
    }

    // Set image URL
    image.src = med.image.url;

    // Apply checkout styles
    const styles = med.image.checkout_style || {};
    Object.keys(styles).forEach((key) => {
      image.style[key] =
        typeof styles[key] === "number" ? `${styles[key]}px` : styles[key];
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

    const amount = 1999;
    const priceData = await this.fetchStripePrice();
    if (!priceData) {
      console.warn("Using fallback amount 0 since price fetch failed.");
    }
    const currency = priceData?.currency?.toLowerCase() || "usd";

    this.card = this.elements.create("card");

    const cardElement = document.getElementById(this.cardElementId);
    if (cardElement) {
      this.card.mount(`#${this.cardElementId}`);
    } else {
      console.warn(`Card element with ID "${this.cardElementId}" not found.`);
    }

    const paymentRequest = this.stripe.paymentRequest({
      country: "US",
      currency: currency,
      total: {
        label: "Confirm Payment",
        amount: amount,
      },
      requestPayerName: true,
      requestPayerEmail: true,
      paymentMethodTypes: ["card", "afterpay_clearpay"],
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
        document.getElementById("payment-request-button").style.display =
          "none";
        console.log("Google Pay / Apple Pay not available on this device.");
      }
    });

    // Google Pay / Apple Pay Charge
    // paymentRequest.on("paymentmethod", async (ev) => {
    //   try {
    //     //  Create subscription directly
    //     const subResult = await fetch(MyStripeData.ajax_url, {
    //       method: "POST",
    //       headers: { "Content-Type": "application/x-www-form-urlencoded" },
    //       body: `action=subscribe_patient&payment_method=${encodeURIComponent(
    //         ev.paymentMethod.id
    //       )}
    //       &promo=${encodeURIComponent(this.promo)}
    //       &price_id=${encodeURIComponent(this.stripePriceId)}
    //       &slug=${encodeURIComponent(this.slug)}
    //       &duration=${encodeURIComponent(this.packageDuration)}`,
    //     });

    //     const subResponse = await subResult.json();

    //     const { error: stripeError, paymentIntent } =
    //       await this.stripe.confirmCardPayment(
    //         subResponse.clientSecret,
    //         {
    //           payment_method: ev.paymentMethod.id,
    //         },
    //         {
    //           handleActions: false,
    //         }
    //       );

    //     if (stripeError) {
    //       ev.complete("fail");
    //       alert("Payment failed: " + stripeError.message);
    //       return;
    //     } else {
    //       ev.complete("success");

    //       if (paymentIntent.status === "succeeded") {
    //         alert("Payment successful!");
    //         console.log(paymentIntent);
    //       } else {
    //         alert("Payment processing or requires action.");
    //         return;
    //       }
    //     }
    //     // if (!subResponse.success) {
    //     //   ev.complete("fail");
    //     //   this.showError(
    //     //     "Failed to create subscription: " +
    //     //       (subResponse.data?.message || ""),
    //     //   );
    //     //   return;
    //     // }

    //     // console.log(" Subscription created:", subResponse.data);

    //     // ev.complete("success");
    //     this.submitForm();
    //   } catch (err) {
    //     console.error("Error in Google/Apple Pay subscription flow:", err);
    //     ev.complete("fail");
    //     this.showError("Something went wrong with Google/Apple Pay.");
    //   }
    // });

    paymentRequest.on("paymentmethod", async (ev) => {
      try {
        // 1. Create subscription on server
        const res = await fetch(MyStripeData.ajax_url, {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: new URLSearchParams({
            action: "subscribe_patient",
            payment_method: ev.paymentMethod.id,
            promo: this.promo,
            price_id: this.stripePriceId,
            slug: this.slug,
            duration: this.packageDuration,
          }),
        });

        const subResponse = await res.json();

        if (!subResponse.success) {
          ev.complete("fail");
          console.log(subResponse.data?.message || "Subscription failed");
          return;
        }

        // 2. Confirm payment (NO payment_method passed here)
        const { error, paymentIntent } = await this.stripe.confirmCardPayment(
          subResponse.data.clientSecret,
          {
            // VERY IMPORTANT
            payment_method: ev.paymentMethod.id,
          },
          {
            handleActions: false,
          }
        );

        if (error) {
          ev.complete("fail");
          alert(error.message);
          return;
        }

        // 3. Complete Payment Request
        ev.complete("success");

        // 4. Handle next actions (3DS, SCA)
        if (paymentIntent.status === "requires_action") {
          const { error: actionError } = await this.stripe.confirmCardPayment(
            subResponse.data.clientSecret
          );

          if (actionError) {
            alert(actionError.message);
            return;
          }
        }

        // 5. Success
        alert("Payment successful!");
        this.submitForm();
      } catch (err) {
        ev.complete("fail");
        console.error(err);
        alert("Payment failed", err);
      }
    });

    // ends google appley code here
  }

  setSlug() {
    const slugField = document.querySelector('[name="hld_plan_slug"]');
    const slug = slugField ? slugField.value : "";
    this.slug = slug;
  }
  bindEvents() {
    const form = document.getElementById(this.formId);
    this.errorDisplay = document.getElementById(this.errorElementId);
    this.paymentButton = document.getElementById(this.paymentButtonId);
    this.revokeButton = document.getElementById(this.revokeButtonId);
    this.klarnaButton = document.getElementById(this.klarnaBtnId);
    this.afterPayButton = document.getElementById(this.afterPayBtnId);
    this.setSlug();
    console.log("bindEvents function called");
    if (
      !form ||
      !this.paymentButton ||
      !this.afterPayButton ||
      !this.klarnaButton
    ) {
      console.warn("Form or payment button not found.");
      return;
    }

    this.afterPayButton.addEventListener("click", (e) =>
      this.handleCardPayment(e, "afterpay")
    );

    this.klarnaButton.addEventListener("click", (e) =>
      this.handleCardPayment(e, "klarna")
    );

    // this.handleCardPayment(e, "klarna");
    // );

    this.paymentButton.addEventListener("click", (e) =>
      this.handleCardPayment(e, "card")
    );
  }

  //  Existing card flow
  async handleCardPayment(e, type) {
    console.log("button clicked and type is ", type);
    e.preventDefault();

    let intent;
    this.toggleButtonState(true, "Processing...", this.paymentButton);
    hldNavigation.toggleLoader(true);

    if (type == "card") intent = await this.createIntent("setup");
    if (type == "klarna") intent = await this.createIntent("klarna");
    if (type == "afterpay") intent = await this.createIntent("afterpay");
    // hldNavigation.toggleLoader(true);

    try {
      if (!intent.success) {
        this.showError("Error creating SetupIntent.");
        hldNavigation.toggleLoader(false);
        this.toggleButtonState(false, "Save and Continue", this.paymentButton);
        return;
      }

      const { clientSecret, customerId } = intent.data;
      const customerName = hldFormHandler.getFullNameFromContainer();

      let result;
      let stateButton;
      let stateButtonText = "Save and Continue";
      switch (type) {
        case "card":
          this.isSubscription = true;
          stateButton = this.paymentButton;
          result = await this.stripe.confirmCardSetup(clientSecret, {
            payment_method: {
              card: this.card,
              billing_details: {
                name: customerName, // TODO: replace with dynamic user info
              },
            },
          });

          break;
        case "klarna":
          result = await this.stripe.confirmKlarnaPayment(clientSecret, {
            return_url: MyStripeData.return_url,
          });
          break;
        case "afterpay":
        default:
          result = await this.stripe.confirmAfterpayClearpayPayment(
            clientSecret,
            {
              return_url: MyStripeData.return_url,
            }
          );
      }

      if (result.error) {
        this.errorDisplay.textContent = result.error.message;
        this.toggleButtonState(false, stateButtonText, stateButton);
        hldNavigation.toggleLoader(false);
        return;
      }

      const paymentMethod = result.setupIntent.payment_method;

      const planSlugField = document.querySelector('[name="hld_plan_slug"]');
      const planSlug = planSlugField ? planSlugField.value : "";
      console.log("plan slug is ", planSlug);

      if (this.isSubscription) {
        /**
         * the below code should be deleted because its replaced with a betterone
         */
        // const subResult = await fetch(MyStripeData.ajax_url, {
        //   method: "POST",
        //   headers: { "Content-Type": "application/x-www-form-urlencoded" },
        //   body: `action=subscribe_patient&customer_id=${encodeURIComponent(
        //     intent.data.customerId
        //   )}&payment_method=${encodeURIComponent(
        //     paymentMethod
        //   )}&slug=${encodeURIComponent(
        //     planSlug
        //   )}&price_id=${encodeURIComponent(
        //     this.stripePriceId
        //   )}&duration=${encodeURIComponent(this.packageDuration)}`,
        // });

        // Call subscription AJAX instead of charge_now
        const planMedication = hldFormHandler.getSelectedMedication();

        const data = new URLSearchParams({
          action: "subscribe_patient",
          customer_id: intent.data.customerId,
          payment_method: paymentMethod,
          slug: planSlug,
          medication: planMedication,
          price_id: this.stripePriceId,
          duration: this.packageDuration,
          telegra_product_id: this.telegraProdID,
          promo: this.promo,
        });

        const subResult = await fetch(MyStripeData.ajax_url, {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: data.toString(),
        });

        const subResponse = await subResult.json();

        if (!subResponse.success) {
          this.showError(
            "Failed to create subscription: " +
              (subResponse.data?.message || "")
          );
          this.toggleButtonState(
            false,
            "Save and Continue",
            this.paymentButton
          );
          return;
        }

        /**
         * Save subscription_id to this hidden input
         * then of fluent form submit we will update telegra_id to subscription table based on this id
         */
        const subIdInput = document.querySelector(
          '[name="my_stripe_subscription_id"]'
        );

        if (subIdInput) {
          subIdInput.value = subResponse?.data?.subscription_id || "";
          console.log("my_stripe_subscription_id set:", subIdInput.value);
        } else {
          console.warn(
            '⚠️ No element found with name="my_stripe_subscription_id" — VERY IMPORTANT: this prevents saving Telegra order_id in the patient table.'
          );
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
          this.toggleButtonState(
            false,
            "Save and Continue",
            this.paymentButton
          );
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
          this.toggleButtonState(
            false,
            "Save and Continue",
            this.paymentButton
          );
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
      this.toggleButtonState(false, "Save and Continue", this.paymentButton);
    }
  }

  async createIntent(type) {
    let response;
    const shippingInfo = hldPatientLogin.getShippingInfo();
    const medicationName = document.querySelector('[name="dropdown_4"]').value;
    if (type == "setup")
      response = await fetch(this.ajaxUrl, {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `action=create_setup_intent`,
      });
    else
      response = await fetch(this.ajaxUrl, {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body:
          `action=create_payment_intent` +
          `&for=${type}` +
          `&duration=${this.packageDuration}` +
          `&price_id=${this.stripePriceId}` +
          `&product_name=${medicationName}` +
          `&promo=${this.promo}` +
          `&shipping_info=${encodeURIComponent(JSON.stringify(shippingInfo))}`,
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
    // set the telegra_id

    console.log("submit function called");
    const dropdown = document.querySelector('select[name="dropdown_4"]');
    const selectedValue = dropdown.value; // example: "NAD+ Injections"
    const telegraId = this.getTelegraIdByValue(selectedValue);
    this.telegraProdID = telegraId;
    if (this.telegraProdID == "") {
      console.error(
        "Telegra Product Variation ID is empty! cannot submit the form"
      );
    }

    const input = document.querySelector('input[name="telegra_product_id"]');
    if (input) {
      if (this.telegraProdID != "") {
        input.value = this.telegraProdID || "";
      }
      console.log("✅ telegra_product_id set to:", this.telegraProdID);
    } else {
      console.warn("⚠️ Hidden input 'telegra_product_id' not found in DOM");
      hldNavigation.toggleLoader(false);
    }

    // its important to call this function to avoid fluent form error while submit becuase fluent form only accept select values we add from fluent form settings so it do not accept
    hldFormHandler.setDOB("year", "month", "day");
    // submit the form
    const submitWrapper = document.querySelector(this.submitWrapperClass);
    if (submitWrapper) {
      const submitButton = submitWrapper.querySelector('button[type="submit"]');
      if (submitButton) {
        submitButton.click();
      }
    } else {
      hldNavigation.toggleLoader(false);
    }
  }

  toggleButtonState(disabled, text, btn) {
    if (btn) {
      btn.disabled = disabled;
      if (text) btn.textContent = text;
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

const cardElement = document.querySelector("#card-element");

if (cardElement) {
  window.stripeHandler = new hldStripeHandler({
    publishableKey: MyStripeData.publishableKey,
    ajaxUrl: MyStripeData.ajax_url,
    formId: "fluentform_" + Number(fluentFormData.form_id),
    cardElementId: "card-element",
    errorElementId: "card-errors",
    paymentButtonId: "hdlMakeStipePayment",
    submitWrapperClass: ".hld_form_main_submit_button",
    prButtonId: "payment-request-button", // NEW: add this container in HTML
  });
} else {
  console.log("#card-element not found. Stripe handler not initialized.");
}
