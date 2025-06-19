<?php
/**
 * This file is responsible for setting default options in the WordPress installation.
 * It includes methods to run the settings and set various default options.
 * 
 * @package WpPluginBoilerplate
 * @since 1.0.0
 * @version 1.0.0
 * @author Pierre Hunout <https://pierrehunout.com/>
 */

namespace WpPluginBoilerplate\Admin;

/**
 * This check prevents direct access to the plugin file,
 * ensuring that it can only be accessed through WordPress.
 * 
 * @since 1.0.0
 */
if (!defined('WPINC')) {
	die;
}

class Settings
{

	/**
	 * Run the settings.
	 * 
	 * This function hooks into the 'admin_init' action to set default options
	 * when the WordPress admin is initialized.
	 * 
	 * @return void
	 */
	public function run()
	{
		add_action('admin_init', [static::class, 'set_defaults']);
	}


	/**
	 * Set default options.
	 * 
	 * This function sets the default options for the WordPress installation.
	 * It updates various settings such as avatar defaults, comment settings,
	 * permalink structure, and more.
	 * 
	 * @global \wpdb $wpdb WordPress database object.
	 * 
	 * @return void
	 */
	public static function set_defaults()
	{
		global $wpdb;

		$options = [
			'avatar_default'            => 'blank',
			'comment_max_links'         => 0,
			'comments_per_page'         => 0,
			'default_ping_status'       => 'closed',
			'default_post_edit_rows'    => 50,
			'permalink_structure'       => '/%postname%/',
			'use_smilies'               => 0
		];

		foreach ($options as $key => $value) {
			update_option($key, $value);
		}

		$posts = [1, 2, 3];

		foreach ($posts as $id) {
			wp_delete_post($id, TRUE);
		}

		wp_delete_comment(1);

		$wpdb->query("DELETE FROM $wpdb->links WHERE link_id != ''");

		return;
	}
}
