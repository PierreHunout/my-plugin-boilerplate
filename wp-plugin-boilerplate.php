<?php

/**
 * @since             1.0.0
 * @package           WpPluginBoilerplate
 *
 * Plugin Name:       Plugin Boilerplate
 * Plugin URI:        https://github.com/PierreHunout/wp-plugin-boilerplate
 * Description:       Basic Plugin with PSR-4 autoload.
 * Version:           1.0.0
 * Author:            Pierre Hunout
 * Author URI:        https://pierrehunout.com/
 * Text Domain:       wp-plugin-boilerplate
 */

namespace WpPluginBoilerplate;

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

define('WP_PLUGIN_BOILERPLATE_VERSION', '1.0.0');
define('WP_PLUGIN_BOILERPLATE_FILE', __FILE__);
define('WP_PLUGIN_BOILERPLATE_PATH', plugin_dir_path(WP_PLUGIN_BOILERPLATE_FILE));
define('WP_PLUGIN_BOILERPLATE_BASENAME', plugin_basename(WP_PLUGIN_BOILERPLATE_FILE));
define('WP_PLUGIN_BOILERPLATE_SLUG', dirname(WP_PLUGIN_BOILERPLATE_BASENAME));
define('WP_PLUGIN_BOILERPLATE_CSS', plugins_url('assets/css/', __FILE__));
define('WP_PLUGIN_BOILERPLATE_JS', plugins_url('assets/js/', __FILE__));

// Support for site-level autoloading.
if (file_exists(__DIR__ . '/lib/autoload.php')) {
	require_once __DIR__ . '/lib/autoload.php';
}

class WpPluginBoilerplate
{
	/**
	 * Holds the class instance
	 * 
	 * @var WpPluginBoilerplate $instance
	 */
	private static $instance = null;


	/**
	 * Return an instance of the WpPluginBoilerplate Class.
	 * 
	 * @since 1.0.0
	 * 
	 * @return WpPluginBoilerplate class instance
	 */
	public static function get_instance()
	{
		if (null === self::$instance) {
			self::$instance	= new self();
		}

		return self::$instance;
	}

	/**
	 * Class initializer.
	 * 
	 */
	public function plugin_loaded()
	{
		Admin\Settings::run();

		self::run_enqueue();
		self::run_files();
	}

	/**
	 * Run Files
	 */
	public static function run_files()
	{
		$path 			= WP_PLUGIN_BOILERPLATE_PATH . 'includes/';
		$directories	=  array_diff(scandir($path), ['..'], ['.']);
		foreach ($directories as $directory) {
			$dir 		= WP_PLUGIN_BOILERPLATE_PATH . 'includes/' . $directory;
			if (is_dir($dir)) {
				$files 	= array_diff(scandir($dir), ['..'], ['.']);
				foreach ($files as $file) {
					if (pathinfo($file, PATHINFO_EXTENSION)) {
						$name 	= basename($file, '.php');
						$class	= 'WpPluginBoilerplate\\Front\\' . $name;
						$new 	= new $class;
						$new->run();
					}
				}
			}
		}
	}

	/**
	 * Enqueue CSS & JS
	 * 
	 */
	public static function run_enqueue()
	{
		add_action('admin_enqueue_scripts', [self::class, 'admin_enqueue'], 1);
		add_action('enqueue_scripts', [self::class, 'frontend_enqueue'], 1);
		add_action('enqueue_scripts', [self::class, 'localize_jquery'], 1);
	}

	/**
	 * Enqueue Admin styles & scripts
	 * 
	 * @since 1.0.0
	 */
	public static function admin_enqueue()
	{
		wp_enqueue_style('admin-styles', WP_PLUGIN_BOILERPLATE_CSS . 'admin.css', [], WP_PLUGIN_BOILERPLATE_VERSION, 'all');
		wp_enqueue_script('admin-scripts', WP_PLUGIN_BOILERPLATE_JS . 'admin.js', [], WP_PLUGIN_BOILERPLATE_VERSION, true);
	}

	/**
	 * Enqueue Frontend styles & scripts
	 * 
	 * @since 1.0.0
	 */
	public static function frontend_enqueue()
	{
		wp_enqueue_style('frontend-styles', WP_PLUGIN_BOILERPLATE_CSS . 'styles.css', [], WP_PLUGIN_BOILERPLATE_VERSION, 'all');
		wp_enqueue_script('frontend-scripts', WP_PLUGIN_BOILERPLATE_JS . 'scripts.js', [], WP_PLUGIN_BOILERPLATE_VERSION, true);
		wp_enqueue_script('frontend-jquery', WP_PLUGIN_BOILERPLATE_JS . 'jquery.js', ['jquery'], WP_PLUGIN_BOILERPLATE_VERSION, true);
	}

	/**
	 * Make data available in jQuery
	 * 
	 * @since 1.0.0
	 */
	public static function localize_jquery()
	{
		$parameters = [
			'test' => 'hello tout le monde'
		];
		wp_localize_script('frontend-scripts', 'parameters', $parameters);
	}
}

add_action(
	'plugin_loaded',
	function () {
		$WpPluginBoilerplate = WpPluginBoilerplate::get_instance();
		$WpPluginBoilerplate->plugin_loaded();
	}
);
