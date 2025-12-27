<style>
    .hld-subscription-info {
        border-top: 1px solid #e2e8f0;
        /* light gray border */
        margin-top: 20px;
        padding-top: 15px;
        font-family: Arial, sans-serif;
        font-size: 14px;
        color: #333;
    }

    .hld-info-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
    }

    .hld-info-label {
        font-weight: 600;
        color: #555;
    }

    .hld-info-value {
        font-weight: 500;
        color: #111;
    }
</style>
<div class="hld-subscription-info">
    <!-- Discounts / Promo -->
    <?php if ($has_discount): ?>
        <div class="hld-info-row">
            <span class="hld-info-label">Discount Applied:</span>
            <span class="hld-info-value">$<?php echo number_format($discount_amount, 2); ?></span>
        </div>
    <?php endif; ?>

    <!-- Upcoming Payments -->
    <div class="hld-info-row">
        <span class="hld-info-label">Next Payment:</span>
        <span class="hld-info-value">
            $<?php echo number_format($next_payment_amount, 2); ?> on
            <?php echo date("F j, Y", $next_payment_timestamp); ?>
        </span>
    </div>

    <!-- Pause / Cancelation Status -->
    <div class="hld-info-row">
        <span class="hld-info-label">Status:</span>
        <span class="hld-info-value">
            <?php
            if ($is_paused) echo "Paused";
            elseif ($is_scheduled_cancel) echo "Scheduled to Cancel";
            else echo "Active";
            ?>
        </span>
    </div>
</div>