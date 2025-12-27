 <p class="heading">Payment Method</p>
 <div class="hld-payment-card">
     <div class="hld-payment-header">
         <span class="hld-card-brand hld-card-brand-<?php echo esc_attr(strtolower($card_brand)); ?>">
             <?php echo ucfirst($card_brand); ?>
         </span>
         <span class="hld-card-last4">
             •••• <?php echo esc_html($card_last4); ?>
         </span>
     </div>

     <div class="hld-payment-body">
         <div class="hld-payment-row">
             <span class="hld-label">Card Holder</span>
             <span class="hld-value"><?php echo esc_html($card_holder_name ?: '—'); ?></span>
         </div>

         <div class="hld-payment-row">
             <span class="hld-label">Expires</span>
             <span class="hld-value">
                 <?php echo esc_html($card_exp_month . '/' . $card_exp_year); ?>
             </span>
         </div>

         <div class="hld-payment-row">
             <span class="hld-label">Funding Type</span>
             <span class="hld-value"><?php echo ucfirst($card_funding); ?></span>
         </div>

         <div class="hld-payment-row">
             <span class="hld-label">Country</span>
             <span class="hld-value"><?php echo esc_html($card_country); ?></span>
         </div>

         <div class="hld-payment-row">
             <span class="hld-label">Postal Code</span>
             <span class="hld-value"><?php echo esc_html($card_postal_code ?: '—'); ?></span>
         </div>
     </div>
 </div>