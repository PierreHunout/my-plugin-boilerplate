<?php
/**
 * Deactivate Action Class
 *
 * @package MyPluginBoilerplate
 *
 * @since 1.0.0
 */

namespace MyPluginBoilerplate\Action;

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
 * Class Deactivate
 *
 * Handles Deactivate functionality for the plugin.
 *
 * @since 1.0.0
 */
final class Deactivate {

	/**
	 * Prevent instantiation of the Deactivate class
	 *
	 * @since 1.0.0
	 */
	private function __construct() {}

	/**
	 * Prevent cloning of the Deactivate class
	 *
	 * @since 1.0.0
	 */
	private function __clone() {}

	/**
	 * Prevent unserialization of the Deactivate class
	 *
	 * @since 1.0.0
	 *
	 * @throws \RuntimeException Always throws exception to prevent unserialization.
	 */
	public function __wakeup() {
		throw new \RuntimeException( 'Cannot unserialize a singleton.' );
	}

	/**
	 * Class Runner for the deactivate functionality.
	 *
	 * This function is currently empty and can be extended in the future
	 * to include deactivate-related features or hooks.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function deactivate(): void {
		// Do something here
	}
}
