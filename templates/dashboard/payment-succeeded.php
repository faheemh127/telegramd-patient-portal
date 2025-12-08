<?php
$hasCardAttached = false;
$hasCardAttached = HLD_Patient::get_card_status();

$client_secret = "";
if (!$hasCardAttached) {
    $hasRemiderScheduled = HLD_Patient::create_email_reminders_to_add_card();

    $patient = HLD_Patient::get_patient_info();
    $customer_id = HLD_Stripe::get_or_create_stripe_customer($patient['email'], $patient['first_name'], $patient['last_name']);
    $setupIntent =  \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

    $setupIntent = \Stripe\SetupIntent::create([
        'customer' => $customer_id,
        'payment_method_types' => ['card'],
    ]);

    $client_secret = $setupIntent->client_secret;
}

?>
<script>
    jQuery(document).ready(function($) {
        const stripe = Stripe('<?php echo STRIPE_PUBLISHABLE_KEY ?>');

        async function initializePaymentMethod() {
            const elements = stripe.elements({
                clientSecret: "<?php echo $client_secret; ?>"
            });

            const paymentElement = elements.create("payment");
            paymentElement.mount("#add-payment-card");

            paymentElement.on('ready', function() {
                document.getElementById('submit-button').style.display = 'block';
            });

            const form = document.getElementById('payment-form');

            form.addEventListener('submit', async (e) => {
                e.preventDefault();

                const btn = document.getElementById('submit-button');
                btn.disabled = true;
                btn.innerText = "Processing...";

                const {
                    error,
                    setupIntent
                } = await stripe.confirmSetup({
                    elements,
                    confirmParams: {
                        return_url: "<?php echo get_permalink(); ?>",
                    },
                    redirect: 'if_required'
                });

                console.log("card setup intent 57 ", setupIntent);
                if (error) {
                    btn.disabled = false;
                    btn.innerText = "Save Card";
                    document.getElementById('payment-message').innerText = error.message;
                } else {
                    document.getElementById('payment-message').style.color = "green";
                    document.getElementById('payment-message').innerText = "Card saved successfully!";
                    const pm_id = setupIntent.payment_method;
                    const res = await fetch(MyStripeData.ajax_url, {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/x-www-form-urlencoded"
                        },
                        // body: `action=cancel_card_reminders`,
                        body: `action=cancel_card_reminders&pm_id=${pm_id}`
                    });

                }
            });
        }
        initializePaymentMethod();
    })
</script>

<div class="hld-thank-you-wrap">
    <h3 class="hld-thank-you-title">Thank you for choosing us.</h3>
    <?php
    if (!$hasCardAttached) {
        echo '<p class="hld-thank-you-desc">Please add the credit card for future transactions....</p>';
    } else {
        echo '<p class="hld-thank-you-desc">Kindly check your dashboard; you may have some pending action items that need to be completed so we can proceed with your medication.</p>';
        echo '<a class="hld-btn hld-btn-back-to-home" href="' . esc_url(HLD_PATIENT_DASHBOARD_URL) . '" class="button">Go to Dashboard</a>';
    }
    ?>

    <div class="w-100 hld-card hld-subscription-card">
        <div class="card-body hld-card-body hld-add-card-wrap">
            <?php if (!$hasCardAttached) : ?>
                <div class="row hld-row mt-3" style="margin-left: auto;margin-right: auto; margin-top: 20px; ">

                    <form id="payment-form">
                        <div id="add-payment-card"></div>
                        <button id="submit-button" class="hld-btn-save-card" style="display: none">Save Card</button>
                        <div id="payment-message" style="color: red;"></div>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>