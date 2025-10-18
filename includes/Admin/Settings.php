<?php

/**
 * This file is responsible for setting default options in the WordPress installation.
 * It includes methods to run the settings and set various default options.
 *
 * @package MyPluginBoilerplate
 *
 * @since 1.0.0
 */

namespace MyPluginBoilerplate\Admin;

/**
 * This check prevents direct access to the plugin file,
 * ensuring that it can only be accessed through WordPress.
 *
 * @since 1.0.0
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Settings {


	/**
	 * Run the settings.
	 *
	 * This function hooks into the 'admin_init' action to set default options
	 * when the WordPress admin is initialized.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function run(): void {
		add_action( 'admin_init', array( static::class, 'set_defaults' ) );
	}


	/**
	 * Set default options.
	 *
	 * This function sets the default options for the WordPress installation.
	 * It updates various settings such as avatar defaults, comment settings,
	 * permalink structure, and more.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function set_defaults(): void {
		$options = array(
			'avatar_default'         => 'blank',
			'comment_max_links'      => 0,
			'comments_per_page'      => 0,
			'default_ping_status'    => 'closed',
			'default_post_edit_rows' => 50,
			'permalink_structure'    => '/%postname%/',
			'use_smilies'            => 0,
		);

		foreach ( $options as $key => $value ) {
			update_option( $key, $value );
		}

		$posts = array( 1, 2, 3 );

		foreach ( $posts as $id ) {
			wp_delete_post( $id, true );
		}

		wp_delete_comment( 1 );

		return;
	}
}
