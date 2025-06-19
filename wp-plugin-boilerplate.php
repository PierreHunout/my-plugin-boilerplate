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
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Requires at least: 5.0
 * Requires PHP:      7.4
 * GitHub Plugin URI:  PierreHunout/wp-plugin-boilerplate
 */

namespace WpPluginBoilerplate;

/**
 * This check prevents direct access to the plugin file,
 * ensuring that it can only be accessed through WordPress.
 * 
 * @since 1.0.0
 */
if (!defined('WPINC')) {
	die;
}

/**
 * The plugin version.
 *
 * @since 1.0.0
 */
define('WP_PLUGIN_BOILERPLATE_VERSION', '1.0.0');

/**
 * The plugin file.
 *
 * @since 1.0.0
 */
define('WP_PLUGIN_BOILERPLATE_FILE', __FILE__);

/**
 * The plugin path.
 *
 * @since 1.0.0
 */
define('WP_PLUGIN_BOILERPLATE_PATH', plugin_dir_path(WP_PLUGIN_BOILERPLATE_FILE));

/**
 * The plugin URL.
 *
 * @since 1.0.0
 */
define('WP_PLUGIN_BOILERPLATE_BASENAME', plugin_basename(WP_PLUGIN_BOILERPLATE_FILE));

/**
 * The plugin slug.
 *
 * This is the directory name of the plugin, which is used in URLs and other references.
 *
 * @since 1.0.0
 */
define('WP_PLUGIN_BOILERPLATE_SLUG', dirname(WP_PLUGIN_BOILERPLATE_BASENAME));

/**
 * The plugin CSS URL.
 *
 * This constant is used to reference the CSS files of the plugin.
 *
 * @since 1.0.0
 */
define('WP_PLUGIN_BOILERPLATE_CSS', plugins_url('assets/css/', __FILE__));

/**
 * The plugin JS URL.
 *
 * This constant is used to reference the JavaScript files of the plugin.
 *
 * @since 1.0.0
 */
define('WP_PLUGIN_BOILERPLATE_JS', plugins_url('assets/js/', __FILE__));

/** 
 * If you don't want to use the autoloading feature, you can comment the following line.
 * 
 * It will include the autoload.php file from the lib directory.
 * Make sure that the autoload.php file exists in the lib directory.
 */
if (file_exists(__DIR__ . '/lib/autoload.php')) {
	require_once __DIR__ . '/lib/autoload.php';
}

class WpPluginBoilerplate
{

	/**
	 * @since 1.0.0
	 * 
	 * This variable is used to implement the Singleton pattern,
	 * ensuring that only one instance of the class exists.
	 */
	private static $instance = null;

	/**
	 * Get the instance of the WpPluginBoilerplate class.
	 * 
	 * This method implements the Singleton pattern to ensure that only one instance of the class exists.
	 * 
	 * @since 1.0.0
	 * 
	 * @return WpPluginBoilerplate
	 */
	public static function get_instance()
	{
		if (null === self::$instance) {
			self::$instance	= new self();
		}

		return self::$instance;
	}

	/**
	 * This method is called when the plugin is loaded.
	 * 
	 * It sets up the necessary actions and runs the enqueue and file loading methods.
	 * 
	 * @since 1.0.0
	 * 
	 * @return void
	 */
	public function plugin_loaded()
	{
		self::run_enqueue();
		self::run_files();
	}

	/**	
	 * Run all files in the includes directory.
	 * 
	 * This method scans the 'includes' directory for subdirectories,
	 * and then scans each subdirectory for PHP files.
	 * It instantiates each class found in the files
	 * and calls its `run` method.
	 * 
	 * @since 1.0.0
	 * 
	 * @return void
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
						$class	= 'WpPluginBoilerplate\\' . $directory . '\\' . $name;
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
	 * This method enqueues the styles and scripts for the admin area of the plugin.
	 * It uses WordPress functions to load the CSS and JS files with the appropriate versioning.
	 * 
	 * @since 1.0.0
	 */
	public static function admin_enqueue()
	{
		wp_enqueue_style('admin-styles', WP_PLUGIN_BOILERPLATE_CSS . 'admin.css', [], WP_PLUGIN_BOILERPLATE_VERSION, 'all');
		wp_enqueue_script('admin-scripts', WP_PLUGIN_BOILERPLATE_JS . 'admin.js', [], WP_PLUGIN_BOILERPLATE_VERSION, true);
	}

	/**
	 * This method enqueues the styles and scripts for the frontend of the plugin.
	 * It uses WordPress functions to load the CSS and JS files with the appropriate versioning.
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
	 * This method localizes a script by passing an array of parameters to it.
	 * The parameters can be accessed in the JavaScript file as a global variable.
	 * 
	 * @since 1.0.0
	 * 
	 * @return void
	 */
	public static function localize_jquery()
	{
		$parameters = [
			'test' => 'hello tout le monde'
		];
		wp_localize_script('frontend-scripts', 'parameters', $parameters);
	}
}

/**
 * This action hook is triggered when the plugin is loaded.
 * It calls the `plugin_loaded` method of the WpPluginBoilerplate class.
 * 
 * @since 1.0.0
 */
add_action(
	'plugin_loaded',
	function () {
		$WpPluginBoilerplate = WpPluginBoilerplate::get_instance();
		$WpPluginBoilerplate->plugin_loaded();
	}
);
