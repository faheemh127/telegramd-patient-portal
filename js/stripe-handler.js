console.log("javascriipt 2324 is working");

document.addEventListener("DOMContentLoaded", function () {
  const stripe = Stripe(MyStripeData.publishableKey);
  const elements = stripe.elements();
  const card = elements.create("card");
  card.mount("#card-element");
  const form = document.getElementById("fluentform_13");
  const errorDisplay = document.getElementById("card-errors");
  const hdlMakeStipePayment = document.getElementById("hdlMakeStipePayment");

  console.log("code after hdlMakeStipePayment called");
  if (!form || !hdlMakeStipePayment) return;

  hdlMakeStipePayment.addEventListener("click", async function (e) {
    console.log("hdlMakeStipePayment button clicked");
    e.preventDefault();

    hdlMakeStipePayment.disabled = true;
    hdlMakeStipePayment.textContent = "Processing...";

    const setupIntentRes = await fetch(MyStripeData.ajax_url, {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: "action=create_setup_intent",
    });

    const setupIntentResult = await setupIntentRes.json();

    if (!setupIntentResult.success) {
      const message =
        setupIntentResult.data?.message ||
        "An error occurred while creating setup intent.";
      alert(` ${message}`);
      hdlMakeStipePayment.disabled = false;
      hdlMakeStipePayment.textContent = "Save and Continue";
      return;
    }

    const clientSecret = setupIntentResult.data.clientSecret;
    const customerId = setupIntentResult.data.customerId;

    // Step 2: Confirm setup intent (no charge, just card save)
    const result = await stripe.confirmCardSetup(clientSecret, {
      payment_method: {
        card: card,
        billing_details: {
          name: "John Doe", // Optional
        },
      },
    });

    if (result.error) {
      errorDisplay.textContent = result.error.message;
    } else {
      const paymentMethod = result.setupIntent.payment_method;

      // Step 3: Save payment method for later
      try {
        const response = await fetch(MyStripeData.ajax_url, {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
          body: `action=save_later_payment_method&customer_id=${customerId}&payment_method=${paymentMethod}`,
        });

        const result = await response.json();

        if (!result.success) {
          const message =
            result.data?.message || "An error occurred. Please try again.";

          // Show a professional alert
          alert(` ${message}`);
          hdlMakeStipePayment.disabled = false;
          hdlMakeStipePayment.textContent = "Save and Continue";
          // Or better: use a custom modal or toast library here (e.g. SweetAlert)
          return;
        }

        // Success pat
        console.log("Payment method saved successfully!");
        // Optional: Show success message
        // alert("Your Payment method saved successfully!");
      } catch (error) {
        console.error("AJAX error:", error);
        alert(
          "Something went wrong while saving your payment method. Please try again."
        );
      }

      const submitWrapper = document.querySelector(
        ".hld_form_main_submit_button"
      );
      if (submitWrapper) {
        const submitButton = submitWrapper.querySelector(
          'button[type="submit"]'
        );
        if (submitButton) {
          submitButton.click();
        }
      }
    }
  });
});
