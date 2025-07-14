<?php

class HLD_UserNotifications
{

    /**
     * Register custom post type for notifications
     */
    public static function register_post_type()
    {
        register_post_type('hld_notifications', [
            'labels' => [
                'name' => 'HLD Notifications',
                'singular_name' => 'HLD Notification'
            ],
            'public' => false,
            'show_ui' => true,
            'supports' => ['title', 'editor'],
            'capability_type' => 'post',
            'has_archive' => false,
            'rewrite' => false,
        ]);
    }

    /**
     * Add a notification for a user
     *
     * @param int $user_id
     * @param string $message
     * @return int|WP_Error Post ID or error
     */
    public static function add_notification($user_id, $message)
    {
        if (!is_numeric($user_id) || empty($message)) {
            return new WP_Error('invalid_input', 'User ID or message is missing.');
        }

        $post_data = [
            'post_type'    => 'hld_notifications',
            'post_title'   => wp_trim_words($message, 10, '...'),
            'post_content' => $message,
            'post_status'  => 'publish',
            'post_author'  => $user_id,
        ];

        return wp_insert_post($post_data);
    }

    /**
     * Get all notifications for a user
     *
     * @param int $user_id
     * @return array
     */
    public static function get_notifications($user_id)
    {
        if (!is_numeric($user_id)) return [];

        $query = new WP_Query([
            'post_type'      => 'hld_notifications',
            'author'         => $user_id,
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'orderby'        => 'date',
            'order'          => 'DESC',
        ]);

        $notifications = [];

        if ($query->have_posts()) {
            foreach ($query->posts as $post) {
                $notifications[] = [
                    'id'      => $post->ID,
                    'title'   => $post->post_title,
                    'message' => $post->post_content,
                    'date'    => $post->post_date,
                ];
            }
        }

        return $notifications;
    }
}
add_action('init', ['HLD_UserNotifications', 'register_post_type']);


// // Usage

// // Save a new notification
// HLD_UserNotifications::add_notification(12, 'Your prescription was approved!');

// // Get all notifications for a user
// $notifications = HLD_UserNotifications::get_notifications(12);
// print_r($notifications);
