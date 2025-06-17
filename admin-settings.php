<?php
// admin-settings.php

add_action('admin_menu', function() {
    add_options_page('TelegraMD Settings', 'TelegraMD Settings', 'manage_options', 'telegramd-settings', 'telegramd_settings_page');
});

function telegramd_settings_page() {
    ?>
    <div class="wrap">
        <h1>TelegraMD API Settings</h1>
        <form method="post" action="options.php">
            <?php
                settings_fields('telegramd-settings-group');
                do_settings_sections('telegramd-settings');
                submit_button();
            ?>
        </form>
    </div>
    <?php
}

add_action('admin_init', function() {
    register_setting('telegramd-settings-group', 'telegramd_api_key');

    add_settings_section('telegramd_main', 'Main Settings', null, 'telegramd-settings');

    add_settings_field('telegramd_api_key', 'API Key', function() {
        $value = get_option('telegramd_api_key');
        echo '<input type="text" name="telegramd_api_key" value="' . esc_attr($value) . '" size="50" />';
    }, 'telegramd-settings', 'telegramd_main');
});
