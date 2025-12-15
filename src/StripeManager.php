<?php
// File: src/StripeManager.php
namespace Faheem\TelegramdPatientPortalPlugin;

class StripeManager
{
    public function charge()
    {
        \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

        $paymentIntent = \Stripe\PaymentIntent::create([
            'amount' => 1000,
            'currency' => 'usd',
        ]);

        return $paymentIntent;
    }
}
