<?php
if (! class_exists('HLD_Mail')) {

    class HLD_Mail
    {

        /**
         * Send Welcome Email to Newly Registered Patient
         */
        public static function patient_signup_welcome($user_id)
        {
            // Get current logged-in user
            $user = get_userdata($user_id);

            if (! $user || ! is_email($user->user_email)) {
                return; // stop if invalid email
            }

            $to = sanitize_email($user->user_email);
            $subject = 'Welcome to Healsend – Your Personalized Health Journey Begins!';

            // Get site name and domain dynamically
            $site_name = get_bloginfo('name');
            $home_url  = home_url();
            $dashboard_url = trailingslashit($home_url) . 'my-account';
            $treatments_url = trailingslashit($home_url) . 'treatments';

            $first_name = get_user_meta($user_id, 'first_name', true);
            $display_name = $first_name ?: $user->display_name;



            // Beautiful HTML Email Template
            $message = '
            <html>
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
                <title>Welcome to ' . esc_html($site_name) . '</title>
                <style>
                    body {
                        font-family: "Segoe UI", Arial, sans-serif;
                        background-color: #f9f9f9;
                        margin: 0;
                        padding: 0;
                        color: #333333;
                    }
                    .email-container {
                        max-width: 640px;
                        margin: 30px auto;
                        background: #ffffff;
                        border-radius: 12px;
                        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
                        overflow: hidden;
                    }
                    .header {
                        background-color: #0d6efd;
                        color: #ffffff;
                        text-align: center;
                        padding: 25px 10px;
                    }
                    .header h1 {
                        margin: 0;
                        font-size: 26px;
                    }
                    .content {
                        padding: 30px 40px;
                        line-height: 1.6;
                        font-size: 16px;
                    }
                    .content h2 {
                        color: #0d6efd;
                        font-size: 22px;
                        margin-bottom: 10px;
                    }
                    .btn {
                        display: inline-block;
                        background: #0d6efd;
                        color: #ffffff !important;
                        text-decoration: none;
                        padding: 12px 25px;
                        border-radius: 8px;
                        margin-top: 20px;
                        font-weight: 600;
                        transition: background 0.3s ease;
                    }
                    .btn:hover {
                        background: #084298;
                    }
                    .footer {
                        text-align: center;
                        padding: 20px;
                        font-size: 13px;
                        background: #f1f1f1;
                        color: #666;
                    }
                    .footer a {
                        color: #0d6efd;
                        text-decoration: none;
                    }
                </style>
            </head>
            <body>
                <div class="email-container">
                    <div class="header">
                        <h1>Welcome to ' . esc_html($display_name) . '</h1>
                    </div>
                    <div class="content">
                        <h2>Hi ' . esc_html($display_name) . ',</h2>
                        <p>Thank you for creating your account with <strong>' . esc_html($site_name) . '</strong>. We’re excited to have you on board!</p>

                        <p>At <strong>' . esc_html($site_name) . '</strong>, our mission is to help you take charge of your health journey. You can explore our wide range of <a href="' . esc_url($treatments_url) . '">programs and treatments</a> to find what suits you best.</p>

                        <p>Your account dashboard gives you easy access to your profile, health programs, and treatment plans anytime.</p>

                        <a href="' . esc_url($dashboard_url) . '" class="btn">Go to My Dashboard</a>

                        <p style="margin-top: 25px;">If you have any questions, our support team is always here to assist you. Together, let’s make your wellness journey a success!</p>

                        <p>Stay healthy,<br><strong>The ' . esc_html($site_name) . ' Team</strong></p>
                    </div>
                    <div class="footer">
                        <p>© ' . date('Y') . ' ' . esc_html($site_name) . '. All rights reserved.</p>
                        <p><a href="' . esc_url($home_url) . '">' . esc_html($home_url) . '</a></p>
                    </div>
                </div>
            </body>
            </html>
            ';

            // Set headers for HTML email
            $headers = array(
                'Content-Type: text/html; charset=UTF-8',
                'From: ' . $site_name . ' <no-reply@' . wp_parse_url(home_url(), PHP_URL_HOST) . '>'
            );

            // Send the email
            wp_mail($to, $subject, $message, $headers);
        }
    }
}
