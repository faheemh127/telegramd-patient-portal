 <style>
     .hld-invoice-section {
         margin-top: 28px;
     }

     .hld-invoice-title {
         font-size: 16px;
         font-weight: 600;
         margin-bottom: 12px;
         color: #111827;
     }

     .hld-invoice-card {
         border: 1px solid #e5e7eb;
         border-radius: 14px;
         padding: 18px;
         background: #ffffff;
     }

     .hld-invoice-row {
         display: flex;
         justify-content: space-between;
         align-items: center;
         padding: 6px 0;
         font-size: 14px;
     }

     .hld-invoice-label {
         color: #6b7280;
     }

     .hld-invoice-value {
         font-weight: 500;
         color: #111827;
     }

     .hld-invoice-total {
         border-top: 1px dashed #e5e7eb;
         margin-top: 10px;
         padding-top: 10px;
     }

     .hld-invoice-amount {
         font-size: 16px;
         font-weight: 700;
         color: #111827;
     }

     /* Status badges */
     .hld-invoice-status {
         padding: 4px 10px;
         border-radius: 999px;
         font-size: 12px;
         font-weight: 600;
         text-transform: capitalize;
     }

     .hld-status-paid {
         background: #DCFCE7;
         color: #166534;
     }

     .hld-status-open {
         background: #FEF3C7;
         color: #92400E;
     }

     .hld-status-uncollectible,
     .hld-status-void {
         background: #FEE2E2;
         color: #991B1B;
     }

     /* Actions */
     .hld-invoice-actions {
         display: flex;
         gap: 12px;
         margin-top: 16px;
         flex-wrap: wrap;
     }

     .hld-invoice-btn {
         padding: 10px 16px;
         border-radius: 999px;
         background: #6366F1;
         color: #ffffff;
         font-size: 14px;
         font-weight: 600;
         text-decoration: none;
         transition: background 0.2s ease;
     }

     .hld-invoice-btn:hover {
         background: #4F46E5;
     }

     .hld-btn-secondary {
         background: #F3F4F6;
         color: #111827;
     }

     .hld-btn-secondary:hover {
         background: #E5E7EB;
     }
 </style>

 <!-- Last Invoice -->
 <div class="hld-invoice-section">
     <h4 class="hld-invoice-title">Last Invoice</h4>

     <div class="hld-invoice-card">
         <div class="hld-invoice-row">
             <span class="hld-invoice-label">Invoice ID</span>
             <span class="hld-invoice-value"><?php echo esc_html($invoice_id); ?></span>
         </div>

         <div class="hld-invoice-row">
             <span class="hld-invoice-label">Status</span>
             <span class="hld-invoice-status hld-status-<?php echo esc_attr($invoice_status); ?>">
                 <?php echo ucfirst($invoice_status); ?>
             </span>
         </div>

         <div class="hld-invoice-row">
             <span class="hld-invoice-label">Subtotal</span>
             <span class="hld-invoice-value">$<?php echo number_format($invoice_subtotal, 2); ?></span>
         </div>

         <div class="hld-invoice-row">
             <span class="hld-invoice-label">Discount</span>
             <span class="hld-invoice-value">
                 -$<?php echo number_format($invoice_discount, 2); ?>
             </span>
         </div>

         <div class="hld-invoice-row hld-invoice-total">
             <span class="hld-invoice-label">Total Paid</span>
             <span class="hld-invoice-amount">
                 $<?php echo number_format($invoice_total, 2); ?>
             </span>
         </div>

         <?php if (!empty($invoice_paid_at)) : ?>
             <div class="hld-invoice-row">
                 <span class="hld-invoice-label">Paid On</span>
                 <span class="hld-invoice-value">
                     <?php echo date('jS M Y', $invoice_paid_at); ?>
                 </span>
             </div>
         <?php endif; ?>

         <div class="hld-invoice-actions">
             <?php if (!empty($hosted_invoice_url)) : ?>
                 <a href="<?php echo esc_url($hosted_invoice_url); ?>" target="_blank" class="hld-invoice-btn">
                     View Invoice
                 </a>
             <?php endif; ?>

             <?php if (!empty($invoice_pdf_url)) : ?>
                 <a href="<?php echo esc_url($invoice_pdf_url); ?>" target="_blank" class="hld-invoice-btn hld-btn-secondary">
                     Download PDF
                 </a>
             <?php endif; ?>
         </div>
     </div>
 </div>