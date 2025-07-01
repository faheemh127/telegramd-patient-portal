document.addEventListener("DOMContentLoaded", function () {
  const stripe = Stripe(MyStripeData.publishableKey); // Provided by wp_localize_script
  const elements = stripe.elements();

  const card = elements.create("card");
  card.mount("#card-element");

  const form = document.getElementById("fluentform_YOUR_FORM_ID");

  form.addEventListener("submit", async function (e) {
    e.preventDefault();

    const result = await stripe.confirmCardSetup(MyStripeData.clientSecret, {
      payment_method: {
        card: card,
      },
    });

    if (result.error) {
      document.getElementById("card-errors").textContent = result.error.message;
    } else {
      document.getElementById("stripe_payment_method").value =
        result.setupIntent.payment_method;
      form.submit();
    }
  });
});








document.addEventListener("DOMContentLoaded", function () {
  const stripeContainer = document.getElementById("stripe-container");
  const paypalContainer = document.getElementById("paypal-container");
  const paymentInputs = document.querySelectorAll("input[name='payment_method']");
  const selectedMethod = document.getElementById("selected_payment_method");

  paymentInputs.forEach((input) => {
    input.addEventListener("change", function () {
      if (this.value === "stripe") {
        stripeContainer.style.display = "block";
        paypalContainer.style.display = "none";
        selectedMethod.value = "stripe";
      } else {
        stripeContainer.style.display = "none";
        paypalContainer.style.display = "block";
        selectedMethod.value = "paypal";
      }
    });
  });
});
