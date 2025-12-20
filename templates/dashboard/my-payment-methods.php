
<?php
//

require_once HLD_PLUGIN_PATH . 'vendor/autoload.php';
\Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

$user_id   = get_current_user_id();
$user_info = get_userdata($user_id);
$patient_email = $user_info->user_email;
$first_name    = $user_info->first_name ?? '';
$last_name     = $user_info->last_name ?? '';

$cus_id = HLD_Stripe::get_or_create_stripe_customer($patient_email, $first_name, $last_name);

$intent = \Stripe\SetupIntent::create([
    'customer' => $cus_id,
]);

$client_secret = $intent->client_secret;
$payment_nonce = wp_create_nonce('payment_nonce');

?>

<style>
  .stripe-management-container { max-width: 600px; margin: auto; }
  .pm-row { 
      background: #fff; border: 1px solid #ddd; padding: 20px; 
      border-radius: 8px; margin-bottom: 12px; transition: 0.2s;
  }
  /* Highlight the Default method */
  .pm-row.is-default { border-left: 5px solid #2196f3; background: #f9fbfd; }

  .badge-default {
      background: #e3f2fd; color: #1976d2; padding: 4px 8px;
      border-radius: 4px; font-size: 11px; font-weight: bold; text-transform: uppercase;
  }

  .actions button { margin-left: 10px; cursor: pointer; }
  .btn-delete { color: #d32f2f; border: none; background: none; }
  .btn-delete:disabled { color: #ccc; cursor: not-allowed; }

  .divider { text-align: center; margin: 20px 0; border-bottom: 1px solid #eee; line-height: 0.1em; }
  .divider span { background:#fff; padding:0 10px; color: #999; }

  .modal {
      display: none; 
      position: fixed; 
      z-index: 9999; 
      left: 0;
      top: 0;
      width: 100%; 
      height: 100%; 
      background-color: rgba(0,0,0,0.5); /* Black w/ opacity */
  }

  .modal-content {
      background-color: #fefefe;
      margin: 10% auto; 
      padding: 30px;
      border-radius: 12px;
      width: 90%;
      max-width: 450px;
      position: relative;
      box-shadow: 0 4px 20px rgba(0,0,0,0.2);
  }

  .close-modal {
      color: #aaa;
      position: absolute;
      right: 20px;
      top: 15px;
      font-size: 28px;
      font-weight: bold;
      cursor: pointer;
  }

  .close-modal:hover { color: #333; }

  #card-element {
      padding: 12px;
      border: 1px solid #ccc;
      border-radius: 4px;
      background: #fff;
      margin-bottom: 20px;
  }

  .btn-full {
      width: 100%;
      padding: 12px;
      background: #2196f3;
      color: white;
      border: none;
      border-radius: 4px;
      font-weight: bold;
      cursor: pointer;
  }
</style>

<div class="stripe-management-container">
    <div class="header-row">
        <h2>Payment Methods</h2>
        <button id="open-add-modal" class="btn-primary">+ Add New</button>
    </div>

    <div id="payment-methods-list">Please Wait Loading...</div>

    <div id="add-method-modal" class="modal" style="display:none;">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <h3>Add New Payment Method</h3>
            
            <div id="payment-request-button"></div>
            
            <div class="divider"><span>or</span></div>

            <form id="payment-form">
                <div id="card-element"></div>
                <button id="submit-card" class="btn-full">Save Card</button>
                <div id="card-errors" role="alert"></div>
            </form>
        </div>
    </div>
</div>

<script>
  let paymentNonce = '<?php echo $payment_nonce; ?>'

  jQuery(document).ready(function($) {
      const stripe = Stripe('<?php echo STRIPE_PUBLISHABLE_KEY ?>');

      async function initializePaymentMethod() {
        const elements = stripe.elements();
        const cardElement = elements.create('card');

        document.addEventListener('DOMContentLoaded', function() {
            if (document.getElementById('card-element')) {
                cardElement.mount('#card-element');
                renderMethods();
            }
        });

        async function renderMethods() {
            const list = document.getElementById('payment-methods-list');
            list.innerHTML = '<p>Loading Available Payment Methods...</p>';

            const formData = new FormData();
            formData.append('action', 'get_payment_methods');
            formData.append('nonce', paymentNonce);

            const response = await fetch(MyStripeData.ajax_url, { method: 'POST', body: formData }).then(r => r.json());

            if (!response.success || response.data.length === 0) {
                list.innerHTML = '<div class="empty-state"><p>No payment methods saved.</p></div>';
                return;
            }

            const methods = response.data;
            const canDelete = methods.length > 1;

            list.innerHTML = methods.map((pm, index) => `
                <div class="pm-row ${pm.is_default ? 'is-default' : ''}" style="border: 1px solid #ddd; padding: 15px; margin-bottom: 10px; border-radius: 8px;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <strong>${pm.label}</strong> **** ${pm.last4} 
                            ${pm.is_default ? '<span class="badge" style="background: #e1f5fe; padding: 2px 8px; border-radius: 4px; font-size: 12px;">Default</span>' : ''}
                        </div>
                        <div class="actions">
                            ${!pm.is_default ? `<button onclick="setDefault('${pm.id}')">Make Default</button>` : ''}
                            <button onclick="deleteMethod('${pm.id}')" ${!canDelete ? 'disabled' : ''} style="${!canDelete ? 'opacity:0.5' : 'color:red'}">
                                Delete
                            </button>
                        </div>
                    </div>
                    ${!canDelete ? '<small style="color: #666; display:block; margin-top:5px;">Add another method to delete this one.</small>' : ''}
                </div>
            `).join('');
        }

        document.getElementById('payment-form')?.addEventListener('submit', async (e) => {
            e.preventDefault();
            const submitBtn = e.target.querySelector('button');
            submitBtn.disabled = true;

            const {setupIntent, error} = await stripe.confirmCardSetup("<?php echo $client_secret; ?>", {
                payment_method: { card: cardElement }
            });

            if (error) {
                alert(error.message);
                submitBtn.disabled = false;
            } else {
                const formData = new FormData();
                formData.append('action', 'add_payment_method');
                formData.append('pm_id', setupIntent.payment_method);
            formData.append('nonce', paymentNonce);

                await fetch(MyStripeData.ajax_url, { method: 'POST', body: formData });
                location.reload(); // Refresh to update list and generate new client secret
            }
        });

        async function setDefault(pmId) {
            const formData = new FormData();
            formData.append('action', 'set_default_payment_method');
            formData.append('pm_id', pmId);
            formData.append('nonce', paymentNonce);
            
            await fetch(MyStripeData.ajax_url, { method: 'POST', body: formData });
            renderMethods();
        }

        async function deleteMethod(pmId) {
            if(!confirm('Delete this payment method?')) return;
            const formData = new FormData();
            formData.append('action', 'delete_payment_method');
            formData.append('pm_id', pmId);
            formData.append('nonce', paymentNonce);
            
            const res = await fetch(MyStripeData.ajax_url, { method: 'POST', body: formData }).then(r => r.json());
            if(!res.success) alert(res.data.message);
            renderMethods();
        }

        const paymentRequest = stripe.paymentRequest({
            country: 'US',
            currency: 'usd',
            total: { label: 'Add Card to Account', amount: 0 },
            requestPayerName: true,
        });

        const prButton = elements.create('paymentRequestButton', { paymentRequest });

        (async () => {
            const result = await paymentRequest.canMakePayment();
            if (result) { prButton.mount('#payment-request-button'); }
        })();

        paymentRequest.on('paymentmethod', async (ev) => {
            const res = await callAjax('add_payment_method', { pm_id: ev.paymentMethod.id });
            if (res.success) {
                ev.complete('success');
                location.reload();
            } else {
                ev.complete('fail');
            }
        });

        async function callAjax(action, extraData = {}) {
            const formData = new FormData();
            formData.append('action', action);
            formData.append('nonce', paymentNonce);
            for (let key in extraData) formData.append(key, extraData[key]);
            
            return fetch(MyStripeData.ajax_url, { method: 'POST', body: formData }).then(r => r.json());
        }



        const modal = document.getElementById('add-method-modal');
        const openBtn = document.getElementById('open-add-modal');
        const closeBtn = document.querySelector('.close-modal');

        openBtn.addEventListener('click', function() {
            modal.style.display = 'block';
            // Re-mount card element if needed to ensure it's visible
            cardElement.mount('#card-element');
        });

        closeBtn.addEventListener('click', function() {
            modal.style.display = 'none';
        });

        window.addEventListener('click', function(event) {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        });

        renderMethods();
      }
      initializePaymentMethod();
  })

</script>
