<div class="hld_questionnaire_wrap">
    <?php if (HLD_Patient::is_phone_call_state()): ?>
        <div class="hld_phone_state_box">
            <div class="hld_phone_state_icon">
                <!-- Phone SVG -->
                <svg width="42" height="42" viewBox="0 0 24 24" fill="none">
                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2
            19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6
            19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.12 2h3a2 
            2 0 0 1 2 1.72c.12.88.38 1.74.77 2.55a2 
            2 0 0 1-.45 2.18l-1.27 1.27a16 16 0 0 0 6 6l1.27-1.27
            a2 2 0 0 1 2.18-.45c.81.39 1.67.65 2.55.77A2 
            2 0 0 1 22 16.92z"
                        stroke="#6d6ffc" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>

            <div class="hld_phone_state_text">
                <h3>Quick Provider Call Required in Your State</h3>
                <p>
                    To stay compliant with state rules, your provider will complete a short live consultation
                    with you after your intake. No delay — it only takes a few minutes.
                </p>
            </div>
        </div>
    <?php endif; ?>

    <div class="hld_thankyou_box">
        <h2>Thank you</h2>
        <p>
            Thank you for submitting your questionnaire. Our medical team will review your information
            and connect with you shortly.
        </p>

        <a class="hld_btn_dashboard" href="https://healsend.com/my-account">
            Go to Dashboard
        </a>
    </div>
</div>