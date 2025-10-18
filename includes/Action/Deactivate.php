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

class Deactivate {

	/**
     * Class Runner for the deactivate functionality.
	 *
	 * This function is currently empty and can be extended in the future
	 * to include deactivate-related features or hooks.
	 *
	 * @return void
	 */
	public static function deactivate(): void {
		// Do something here
	}
}
