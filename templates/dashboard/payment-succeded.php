<?php
$hasCardAttached = false;
$hasCardAttached = HLD_Patient::get_card_status();

$client_secret = "";
if (!$hasCardAttached) {
    $hasRemiderScheduled = HLD_Patient::create_email_reminders_to_add_card();

    $patient = HLD_PATIENT::get_patient_info();
    $customer_id = HLD_Stripe::get_or_create_stripe_customer($patient['email'], $$patient['first_name'], $$patient['last_name']);
    $setupIntent =  \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

    $setupIntent = \Stripe\SetupIntent::create([
          'customer' => $customer_id,
          'payment_method_types' => ['card'],
      ]);

    $client_secret = $setupIntent->client_secret;
}

?>
<script>

const stripe = Stripe(STRIPE_PUBLISHABLE_KEY);

async function initializePaymentMethod() {
const elements = stripe.elements({ <?php echo $client_secret ?> });
    const paymentElement = elements.create("payment");
    paymentElement.mount("#add-payment-card");

    const form = document.getElementById('payment-form');
    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const { error, setupIntent } = await stripe.confirmSetup({
            elements,
            confirmParams: {
                return_url: <?php echo get_permalink() ?>,
            },
            redirect: 'if_required' // Prevents redirect if 3D secure isn't needed
        });

        if (error) {
            document.getElementById('payment-message').innerText = error.message;
        } else {
            // Success!
            document.getElementById('payment-message').style.color = "green";
            document.getElementById('payment-message').innerText = "Card saved successfully!";

            setTimeout(function () {
              document.getElementById('stripe-card-add').remove();
            }, 300);
        }
    });
}

initializePaymentMethod();

</script>

<h3 class="hld-subscription-title">Thank you for choosing us.</h3>
<div class="w-100 hld-card hld-subscription-card">
    <div class="card-body hld-card-body">
        <?php if (!$hasCardAttached) : ?>
            <div class="row hld-row mt-3" style="margin-left: auto;margin-right: auto; margin-top: 20px; ">
                Please add the credit card for future transactions....
              <div>
                <div id='add-payment-card'>
              
                </div>
                <div id="payment-message">
                </div>
              </div>
            </div>
        <?php endif; ?>
    </div>
</div>
