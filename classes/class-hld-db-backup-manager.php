<?php
if (!defined('ABSPATH')) exit;

class HLD_DB_Backup_Manager
{
    public static function init()
    {
        add_action('admin_menu', [__CLASS__, 'add_admin_page']);
        add_action('admin_post_hld_export_backup', [__CLASS__, 'handle_export']);
        add_action('admin_post_hld_import_backup', [__CLASS__, 'handle_import']);
    }

    public static function add_admin_page()
    {
        add_submenu_page(
            'options-general.php', // You can change to your plugin main slug
            'Database Backup',
            'DB Backup',
            'manage_options',
            'hld-db-backup',
            [__CLASS__, 'render_admin_page']
        );
    }

    public static function render_admin_page()
    {
        if (!current_user_can('manage_options')) {
            return;
        }

        if (isset($_GET['import']) && $_GET['import'] === 'success') {
            echo '<div class="notice notice-success is-dismissible"><p>✅ Database restored successfully!</p></div>';
        }



?>
        <div class="wrap">
            <h1>Healsend Database Backup</h1>

            <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
                <?php wp_nonce_field('hld_export_backup_nonce', 'hld_export_backup_nonce'); ?>
                <input type="hidden" name="action" value="hld_export_backup">
                <p><button type="submit" class="button button-primary">Export Backup</button></p>
            </form>

            <hr>

            <form method="post" enctype="multipart/form-data" action="<?php echo admin_url('admin-post.php'); ?>">
                <?php wp_nonce_field('hld_import_backup_nonce', 'hld_import_backup_nonce'); ?>
                <input type="hidden" name="action" value="hld_import_backup">
                <p><input type="file" name="backup_file" accept=".json" required></p>
                <p><button type="submit" class="button button-secondary">Import Backup</button></p>
            </form>
        </div>
<?php
    }

    public static function handle_export()
    {
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }

        check_admin_referer('hld_export_backup_nonce', 'hld_export_backup_nonce');

        global $wpdb;

        $tables = [
            HEALSEND_PATIENTS_TABLE,
            HEALSEND_PAYMENTS_TABLE,
            HEALSEND_PATIENT_FORMS_TABLE,
            HEALSEND_FORM_ANSWERS_TABLE,
            HEALSEND_ACTION_ITEMS_TABLE,
            HEALSEND_USER_ACTIONS_TABLE,
            $wpdb->prefix . 'hld_user_subscriptions' // adjust if different
        ];

        $backup_data = [];

        foreach ($tables as $table) {
            $results = $wpdb->get_results("SELECT * FROM $table", ARRAY_A);
            $backup_data[$table] = $results;
        }

        $json = json_encode($backup_data, JSON_PRETTY_PRINT);

        header('Content-Disposition: attachment; filename="healsend-backup-' . date('Y-m-d-His') . '.json"');
        header('Content-Type: application/json');
        echo $json;
        exit;
    }

    public static function handle_import()
    {
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }

        check_admin_referer('hld_import_backup_nonce', 'hld_import_backup_nonce');

        if (empty($_FILES['backup_file']['tmp_name'])) {
            wp_die('No file uploaded.');
        }

        $file_content = file_get_contents($_FILES['backup_file']['tmp_name']);
        $data = json_decode($file_content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            wp_die('Invalid JSON file.');
        }

        global $wpdb;

        foreach ($data as $table => $rows) {
            // Clear existing data
            $wpdb->query("TRUNCATE TABLE $table");

            if (!empty($rows)) {
                foreach ($rows as $row) {
                    $wpdb->insert($table, $row);
                }
            }
        }

        wp_redirect(admin_url('options-general.php?page=hld-db-backup&import=success'));
        exit;
    }
}

HLD_DB_Backup_Manager::init();
