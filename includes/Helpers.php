<?php

/**
 * This file is responsible for handling the tools functionality in the WordPress plugin.
 * It includes methods to fetch data from a JSON URL and retrieve specific objects from the JSON response
 * and can be extended in the future.
 * 
 * @package MyPluginBoilerplate
 * 
 * @since 1.0.0
 */

namespace MyPluginBoilerplate;

/**
 * This check prevents direct access to the plugin file,
 * ensuring that it can only be accessed through WordPress.
 * 
 * @since 1.0.0
 */
if (!defined('WPINC')) {
    die;
}

class Helpers
{

    /**
     * Initialize the WordPress filesystem.
     *
     * @since 1.0.0
     * 
     * @return WP_Filesystem_Base|false The filesystem instance on success, false on failure.
     */
    public static function get_filesystem() : \WP_Filesystem_Base|false
    {
        global $wp_filesystem;

        if (!function_exists('WP_Filesystem')) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
        }

        if (!WP_Filesystem()) {
            return false;
        }

        return $wp_filesystem;
    }
}
