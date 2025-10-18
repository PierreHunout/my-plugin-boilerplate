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
use RuntimeException;
use Throwable;
use MyPluginBoilerplate\Helpers;
use MyPluginBoilerplate\Debug\Log;

/**
 * This check prevents direct access to the plugin file,
 * ensuring that it can only be accessed through WordPress.
 *
 * @since 1.0.0
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class Uninstall
 *
 * Handles Uninstall functionality for the plugin.
 *
 * @since 1.0.0
 */
class Uninstall {



	/**
	 * Class Runner for the uninstall functionality.
	 *
	 * This function is currently empty and can be extended in the future
	 * to include uninstall-related features or hooks.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function uninstall(): void {
		if ( defined( 'WP_UNINSTALL_PLUGIN' ) ) {
			// Clean up plugin data
			self::options();
			self::user_meta();
			self::transients();
			self::custom_tables();
			self::files();
			self::cron();
			self::capabilities();

			// Clear any object cache
			if ( function_exists( 'wp_cache_flush' ) ) {
				wp_cache_flush();
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
	public static function options(): void {
		// List of plugin options to remove
		$options_to_remove = [
			'my_plugin_boilerplate_text',
			'my_plugin_boilerplate_email',
			'my_plugin_boilerplate_url',
			'my_plugin_boilerplate_number',
			'my_plugin_boilerplate_checkbox',
			'my_plugin_boilerplate_toggle',
			'my_plugin_boilerplate_textarea',
			'my_plugin_boilerplate_select',
			'my_plugin_boilerplate_radio',
		];

		// Remove each option
		foreach ( $options_to_remove as $option ) {
			delete_option( $option );

			// Also remove from multisite if applicable
			if ( is_multisite() ) {
				delete_site_option( $option );
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
	public static function user_meta(): void {
		// Get all users and remove plugin-specific meta data using WordPress functions
		$users = get_users( [ 'fields' => 'ID' ] );

		foreach ( $users as $user_id ) {
			// Get all meta keys for this user that start with our prefix
			$meta_keys = get_user_meta( $user_id );

			foreach ( $meta_keys as $key => $value ) {
				if ( strpos( $key, 'my_plugin_boilerplate_' ) === 0 ) {
					delete_user_meta( $user_id, $key );
				}
			}
		}
	}

	/**
	 * Clean up transients
	 *
	 * Remove plugin-specific transients.
	 *
	 * @since 1.0.0
	 *
	 * @global wpdb $wpdb WordPress database abstraction object.
	 *
	 * @return void
	 */
	public static function transients(): void {
		global $wpdb;

		if ( defined( 'WP_UNINSTALL_PLUGIN' ) ) {
			// Remove all transients that start with plugin prefix
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Acceptable during uninstall
			$wpdb->query(
				$wpdb->prepare(
					"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s",
					$wpdb->esc_like( '_transient_my_plugin_boilerplate_' ) . '%',
					$wpdb->esc_like( '_transient_timeout_my_plugin_boilerplate_' ) . '%'
				)
			);

			// For multisite
			if ( is_multisite() ) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Acceptable during uninstall
				$wpdb->query(
					$wpdb->prepare(
						"DELETE FROM {$wpdb->sitemeta} WHERE meta_key LIKE %s OR meta_key LIKE %s",
						$wpdb->esc_like( '_transient_my_plugin_boilerplate_' ) . '%',
						$wpdb->esc_like( '_transient_timeout_my_plugin_boilerplate_' ) . '%'
					)
				);
			}
		}
	}

	/**
	 * Clean up custom database tables
	 *
	 * Drop any custom tables created by the plugin.
	 *
	 * @since 1.0.0
	 *
	 * @global wpdb $wpdb WordPress database abstraction object.
	 *
	 * @return void
	 */
	public static function custom_tables(): void {
		// Only remove custom tables during actual plugin uninstall
		if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
			return;
		}

		global $wpdb;

		// Example: Drop custom table if it exists
		// Use proper WordPress naming conventions
		$table_name = $wpdb->prefix . 'my_plugin_boilerplate_data';

		// Check if table exists before dropping
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Acceptable during uninstall
		$table_exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table_name ) );

		if ( $table_exists ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.SchemaChange -- Acceptable during uninstall
			$wpdb->query( $wpdb->prepare( 'DROP TABLE IF EXISTS %s', $table_name ) );
		}

		// Add your custom table cleanup here if needed
	}

	/**
	 * Clean up uploaded files
	 *
	 * Remove any files uploaded by the plugin.
	 *
	 * @since 1.0.0
	 *
	 * @throws RuntimeException If filesystem cannot be initialized.
	 *
	 * @return void
	 */
	public static function files(): void {
		$upload_dir         = wp_upload_dir();
		$plugin_upload_path = $upload_dir['basedir'] . '/my-plugin-boilerplate';

		try {
			// Initialize WordPress filesystem
			$filesystem = Helpers::get_filesystem();

			if ( ! $filesystem ) {
				throw new RuntimeException( 'Failed to initialize WordPress filesystem' );
			}

			// Ensure the directory exists
			if ( ! $filesystem->is_dir( $plugin_upload_path ) ) {
				throw new RuntimeException( sprintf( 'Directory not found: %s', $plugin_upload_path ) );
			}

			self::remove_directory( $plugin_upload_path );
		} catch ( Throwable $e ) {
			// Log the error for debugging during uninstall
			Log::log(
				'uninstall_files_error',
				[
					'message' => $e->getMessage(),
					'file'    => $e->getFile(),
					'line'    => $e->getLine(),
				]
			);

			return;
		}
	}

	/**
	 * Recursively remove directory and its contents
	 *
	 * @since 1.0.0
	 *
	 * @param string $dir Directory path to remove.
	 *
	 * @throws RuntimeException If directory cannot be removed.
	 *
	 * @return void
	 */
	public static function remove_directory( $dir ): void {
		try {
			// Initialize WordPress filesystem
			$filesystem = Helpers::get_filesystem();

			if ( ! $filesystem ) {
				throw new RuntimeException( 'Failed to initialize WordPress filesystem' );
			}

			// Ensure the directory exists
			if ( ! $filesystem->is_dir( $dir ) ) {
				throw new RuntimeException( sprintf( 'Directory not found: %s', $dir ) );
			}

			$files = array_diff( scandir( $dir ), [ '.', '..' ] );

			foreach ( $files as $file ) {
				$path = $dir . '/' . $file;

				if ( $filesystem->is_dir( $path ) ) {
					self::remove_directory( $path );
				} else {
					$filesystem->delete( $path );
				}
			}

			$filesystem->rmdir( $dir );
		} catch ( \Throwable $e ) {
			// Log the error for debugging during uninstall
			Log::log(
				'uninstall_directory_error',
				[
					'directory' => $dir,
					'message'   => $e->getMessage(),
					'file'      => $e->getFile(),
					'line'      => $e->getLine(),
				]
			);

			return;
		}
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
	public static function cron(): void {
		// Remove scheduled hooks
		$cron_hooks = [
			'my_plugin_boilerplate_daily_cleanup',
			'my_plugin_boilerplate_weekly_maintenance',
			// Add your cron hooks here
		];

		foreach ( $cron_hooks as $hook ) {
			wp_clear_scheduled_hook( $hook );
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
	public static function capabilities(): void {
		// Use wp_roles() function instead of global variable
		if ( ! function_exists( 'wp_roles' ) ) {
			return;
		}

		$wp_roles = wp_roles();

		// List of custom capabilities to remove
		$capabilities = [
			'manage_my_plugin_boilerplate',
			'configure_my_plugin_boilerplate',
			// Add your custom capabilities here
		];

		// Remove capabilities from all roles
		foreach ( $wp_roles->roles as $role_name => $role_info ) {
			$role = get_role( $role_name );

			if ( $role ) {
				foreach ( $capabilities as $capability ) {
					$role->remove_cap( $capability );
				}
			}
		}
	}
}
