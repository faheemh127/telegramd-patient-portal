<style>
    .treatment-container {
        max-width: 700px;
        margin: 0 auto;
        padding: 30px 0;
    }

    .section-title {
        font-size: 37px;
        font-weight: 600;
        margin-bottom: 20px;
        text-align: center;
        font-family: var(--hld-font-primary);
        max-width: 100%;
        margin: auto;
        margin-bottom: 15px;
    }

    .treatment-card {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 15px 18px;
        margin-bottom: 14px;
        border: 1px solid rgba(0, 0, 0, 0.2);
        border-radius: 12px;
        background: #fff;
        text-decoration: none;
        color: #000;
        font-size: 16px;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .treatment-card:hover {
        border-color: var(--hld-color-primary);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
        transform: translateY(-1px);
    }

    .treatment-card .icon {
        font-size: 22px;
        min-width: 28px;
        text-align: center;
    }

    .treatment-card .text {
        flex: 1;
        font-family: var(--hld-font-primary);
    }

    @media screen and (max-width: 767px) {
        .section-title {
            font-size: 30px;
        }
    }
</style>
<div class="hld_form_container">
    <div class="hld_form_wrap">
        <div class="treatment-container">
            <h2 class="section-title">What is your primary health goal?</h2>

            <a href="<?php echo esc_url(home_url('/glp-1-form')); ?>" class="treatment-card">
                <span class="icon">⚖️</span>
                <span class="text">Lose weight</span>
            </a>

            <a href="<?php echo esc_url(home_url('/nad-therapy')); ?>" class="treatment-card">
                <span class="icon">📏</span>
                <span class="text">Metabolic Optimization (NAD⁺ Therapy)</span>
            </a>

            <a href="<?php echo esc_url(home_url('/pt-141-form')); ?>" class="treatment-card">
                <span class="icon">⚡</span>
                <span class="text">Better energy &amp; mood</span>
            </a>

            <a href="<?php echo esc_url(home_url('/sermorelin-therapy')); ?>" class="treatment-card">
                <span class="icon">💪</span>
                <span class="text">Growth Hormone Optimization</span>
            </a>

        </div>
    </div>
</div>