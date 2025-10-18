<?php

/**
 * Uninstall Action Class
 * 
 * @package MyPluginBoilerplate
 *
 * @since 1.0.0
 */

namespace MyPluginBoilerplate\Action;

use WP_Roles;

/**
 * This check prevents direct access to the plugin file,
 * ensuring that it can only be accessed through WordPress.
 *
 * @since 1.0.0
 */
if (! defined('WPINC')) {
	die;
}

class Uninstall
{

	/**
	 * Class Runner for the uninstall functionality.
	 *
	 * This function is currently empty and can be extended in the future
	 * to include uninstall-related features or hooks.
	 *
	 * @return void
	 */
	public static function uninstall(): void
	{
		if (defined('WP_UNINSTALL_PLUGIN')) {
			// Clean up plugin data
			self::options();
			self::user_meta();
			self::transients();
			self::custom_tables();
			self::files();
			self::cron();
			self::capabilities();

			// Clear any object cache
			if (function_exists('wp_cache_flush')) {
				wp_cache_flush();
			}

			// Log the uninstall for debugging (optional)
			if (defined('WP_DEBUG_LOG') && WP_DEBUG_LOG) {
				error_log('My Plugin Boilerplate: Plugin uninstalled and data cleaned up.');
			}
		}
	}

	/**
	 * Clean up plugin options
	 *
	 * Remove all plugin-specific options from the database.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function options(): void
	{
		// List of plugin options to remove
		$options_to_remove = [
			'my_plugin_boilerplate_version',
			'my_plugin_boilerplate_settings',
			'my_plugin_boilerplate_options',
			'my_plugin_boilerplate_activation_date',
			'my_plugin_boilerplate_cache',
		];

		// Remove each option
		foreach ($options_to_remove as $option) {
			delete_option($option);

			// Also remove from multisite if applicable
			if (is_multisite()) {
				delete_site_option($option);
			}
		}
	}

	/**
	 * Clean up user meta
	 *
	 * Remove plugin-specific user meta data.
	 *
	 * @since 1.0.0
	 * 
	 * @return void
	 */
	public static function user_meta(): void
	{
		global $wpdb;

		// Remove user meta keys that start with plugin prefix
		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$wpdb->usermeta} WHERE meta_key LIKE %s",
				'my_plugin_boilerplate_%'
			)
		);
	}

	/**
	 * Clean up transients
	 *
	 * Remove plugin-specific transients.
	 *
	 * @since 1.0.0
	 * 
	 * @return void
	 */
	public static function transients(): void
	{
		global $wpdb;

		// Remove transients that start with plugin prefix
		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s",
				'_transient_my_plugin_boilerplate_%',
				'_transient_timeout_my_plugin_boilerplate_%'
			)
		);

		// For multisite
		if (is_multisite()) {
			$wpdb->query(
				$wpdb->prepare(
					"DELETE FROM {$wpdb->sitemeta} WHERE meta_key LIKE %s OR meta_key LIKE %s",
					'_transient_my_plugin_boilerplate_%',
					'_transient_timeout_my_plugin_boilerplate_%'
				)
			);
		}
	}

	/**
	 * Clean up custom database tables
	 *
	 * Drop any custom tables created by the plugin.
	 *
	 * @since 1.0.0
	 * 
	 * @return void
	 */
	public static function custom_tables(): void
	{
		global $wpdb;

		// Example: Drop custom table if it exists
		// $table_name = $wpdb->prefix . 'my_plugin_boilerplate_data';
		// $wpdb->query( "DROP TABLE IF EXISTS {$table_name}" );

		// Add your custom table cleanup here if needed
	}

	/**
	 * Clean up uploaded files
	 *
	 * Remove any files uploaded by the plugin.
	 *
	 * @since 1.0.0
	 * 
	 * @return void
	 */
	public static function files(): void
	{
		$upload_dir = wp_upload_dir();
		$plugin_upload_path = $upload_dir['basedir'] . '/my-plugin-boilerplate';

		// Remove plugin upload directory if it exists
		if (is_dir($plugin_upload_path)) {
			self::remove_directory($plugin_upload_path);
		}
	}

	/**
	 * Recursively remove directory and its contents
	 *
	 * @since 1.0.0
	 * 
	 * @param string $dir Directory path to remove.
	 * @return void
	 */
	public static function remove_directory($dir): void
	{
		if (! is_dir($dir)) {
			return;
		}

		$files = array_diff(scandir($dir), ['.', '..']);

		foreach ($files as $file) {
			$path = $dir . '/' . $file;

			if (is_dir($path)) {
				self::remove_directory($path);
			} else {
				unlink($path);
			}
		}

		rmdir($dir);
	}

	/**
	 * Clean up scheduled events
	 *
	 * Remove any cron jobs scheduled by the plugin.
	 *
	 * @since 1.0.0
	 * 
	 * @return void
	 */
	public static function cron(): void
	{
		// Remove scheduled hooks
		$cron_hooks = [
			'my_plugin_boilerplate_daily_cleanup',
			'my_plugin_boilerplate_weekly_maintenance',
			// Add your cron hooks here
		];

		foreach ($cron_hooks as $hook) {
			wp_clear_scheduled_hook($hook);
		}
	}

	/**
	 * Clean up capabilities
	 *
	 * Remove custom capabilities added by the plugin.
	 *
	 * @since 1.0.0
	 * 
	 * @return void
	 */
	public static function capabilities(): void
	{
		global $wp_roles;

		if (! isset($wp_roles)) {
			$wp_roles = new WP_Roles();
		}

		// List of custom capabilities to remove
		$capabilities = [
			'manage_my_plugin_boilerplate',
			'configure_my_plugin_boilerplate',
			// Add your custom capabilities here
		];

		// Remove capabilities from all roles
		foreach ($wp_roles->roles as $role_name => $role_info) {
			$role = get_role($role_name);

			if ($role) {
				foreach ($capabilities as $capability) {
					$role->remove_cap($capability);
				}
			}
		}
	}
}
