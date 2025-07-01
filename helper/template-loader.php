<?php

/**
 * Healsend Plugin - Template Loader & Utility Functions
 */

defined('ABSPATH') || exit;

/**
 * Load a template file from the /templates directory and pass data to it.
 *
 * @param string $template_name Relative path inside /templates (without `.php`)
 * @param array  $data          Associative array of variables to extract
 */
function hdl_get_template($template_name, $data = [])
{
    $template_path = realpath(plugin_dir_path(__FILE__) . "../templates/{$template_name}.php");

    if (file_exists($template_path)) {
        extract($data);
        include $template_path;
    }
}

/**
 * Get the absolute file system path to the plugin root.
 *
 * @return string
 */
function hdl_plugin_path()
{
    return plugin_dir_path(__DIR__); // Goes one level up from /helper
}

/**
 * Get the URL of the plugin root.
 *
 * @return string
 */
function hdl_plugin_url()
{
    return plugin_dir_url(__DIR__);
}

/**
 * Get the path to a plugin asset (CSS, JS, images etc.)
 *
 * @param string $relative_path Path relative to plugin root (e.g., 'css/main.css')
 * @return string
 */
function hdl_asset_url($relative_path)
{
    return trailingslashit(hdl_plugin_url()) . ltrim($relative_path, '/');
}

/**
 * Get the WordPress uploads directory (array with path, url, subdir, etc.)
 *
 * @return array|false
 */
function hdl_upload_dir()
{
    return wp_upload_dir(); // Returns array with 'path', 'url', 'basedir', etc.
}

/**
 * Get full path to plugin's templates directory.
 *
 * @return string
 */
function hdl_template_path()
{
    return hdl_plugin_path() . 'templates/';
}
