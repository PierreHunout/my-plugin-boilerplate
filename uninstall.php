<?php
/**
 * Uninstall My Plugin Boilerplate
 *
 * This file is called when the plugin is uninstalled.
 * It handles cleanup of plugin data, options, and database tables.
 *
 * @package MyPluginBoilerplate
 * @since   1.0.0
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Plugin-specific constants
define( 'MY_PLUGIN_BOILERPLATE_UNINSTALL', true );
