<?php

/**
 * Add Action Class
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

class Add {

	/**
     * Class Runner for the add functionality.
	 *
	 * This function is currently empty and can be extended in the future
	 * to include add-related features or hooks.
	 *
	 * @return void
	 */
	public function run(): void {
		// Do something here
	}
}
