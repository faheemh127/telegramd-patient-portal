class HldPatientLogin {
    constructor() {
        this.usernameField = document.querySelector('.hld-username');
        this.passwordField = document.querySelector('.hld-password');
        this.loginBtn = document.querySelector('.hld-login-btn');

        if (this.loginBtn) {
            this.loginBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.login();
            });
        }
    }

    login() {
        const username = this.usernameField.value.trim();
        const password = this.passwordField.value.trim();

        // Validation
        if (!username || !password) {
            alert('Username and password are required.');
            return;
        }

        // AJAX call
        jQuery.ajax({
            url: hld_ajax_obj.ajaxurl,
            type: 'POST',
            data: {
                action: 'hld_patient_login',
                username: username,
                password: password,
                nonce: hld_ajax_obj.nonce
            },
            success: (response) => {
                if (response.success) {
                    // Reload with parameter ?login=success
                    window.location.href = window.location.pathname + '?login=success';
                } else {
                    alert(response.data || 'Login failed. Please try again.');
                }
            },

            error: () => {
                alert('Something went wrong. Please try again.');
            }
        });
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    new HldPatientLogin();
});
