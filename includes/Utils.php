<?php
/**
 * This file is responsible for handling the tools functionality in the WordPress plugin.
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
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class Utils
 *
 * Handles Helpers functionality for the plugin.
 *
 * @since 1.0.0
 */
class Utils {

	/**
	 * Prevent instantiation of the Helpers class
	 *
	 * @since 1.0.0
	 */
	private function __construct() {}

	/**
	 * Prevent cloning of the Helpers class
	 *
	 * @since 1.0.0
	 */
	private function __clone() {}

	/**
	 * Prevent unserialization of the Helpers class
	 *
	 * @since 1.0.0
	 *
	 * @throws \RuntimeException Always throws exception to prevent unserialization.
	 */
	public function __wakeup() {
		throw new \RuntimeException( 'Cannot unserialize a singleton.' );
	}

	/**
	 * Initialize the WordPress filesystem.
	 *
	 * @since 1.0.0
	 *
	 * @return WP_Filesystem_Base|false The filesystem instance on success, false on failure.
	 */
	public static function get_filesystem(): \WP_Filesystem_Base|false {
		global $wp_filesystem;

		if ( ! function_exists( 'WP_Filesystem' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		if ( ! WP_Filesystem() ) {
			return false;
		}

		return $wp_filesystem;
	}
}
