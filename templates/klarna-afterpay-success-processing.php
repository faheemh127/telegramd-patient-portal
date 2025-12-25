<div class="hld_prefix_loading_wrapper">
    <div class="hld_prefix_loader"></div>
    <p class="hld_prefix_loading_message">
        Your payment was successful. We are processing your order. Please do not close this window.
    </p>
</div>

<style>
    /* Wrapper */
    .hld_prefix_loading_wrapper {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 40px 20px;
        background-color: #f9f9f9;
        border-radius: 12px;
        box-shadow: 0 2px 7px rgba(0, 0, 0, 0.04);
        max-width: 400px !important;
        margin: 0 auto;
        text-align: center;
        margin-top: 100px;
    }

    /* Loader (animated spinning circle) */
    .hld_prefix_loader {
        border: 6px solid #f3f3f3;
        border-top: 6px solid var(--hld-color-primary);
        /* Green color */
        border-radius: 50%;
        width: 60px;
        height: 60px;
        animation: hld_prefix_spin 1s linear infinite;
        margin-bottom: 20px;
    }

    /* Loader animation */
    @keyframes hld_prefix_spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    /* Message text */
    .hld_prefix_loading_message {
        font-size: 16px;
        color: #333;
        font-weight: 500;
        line-height: 1.5;
    }
</style>