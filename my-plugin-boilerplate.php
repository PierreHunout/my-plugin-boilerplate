<?php

/**
 * @since             1.0.0
 * @package           MyPluginBoilerplate
 *
 * Plugin Name:       My Plugin Boilerplate
 * Plugin URI:        https://github.com/PierreHunout/my-plugin-boilerplate
 * Description:       Basic Plugin with PSR-4 autoload.
 * Version:           1.0.0
 * Author:            Pierre Hunout
 * Author URI:        https://pierrehunout.com/
 * Text Domain:       my-plugin-boilerplate
 * License:           GPL-3.0
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Requires at least: 5.0
 * Requires PHP:      7.4
 * GitHub Plugin URI:  PierreHunout/my-plugin-boilerplate
 */

namespace MyPluginBoilerplate;

use RuntimeException;
use Throwable;

/**
 * This check prevents direct access to the plugin file,
 * ensuring that it can only be accessed through WordPress.
 * 
 * @since 1.0.0
 */
if (!defined('WPINC')) {
	die;
}

define('MY_PLUGIN_BOILERPLATE_VERSION', '1.0.0');
define('MY_PLUGIN_BOILERPLATE_NAME', 'My Plugin Boilerplate');
define('MY_PLUGIN_BOILERPLATE_NAMESPACE', 'MyPluginBoilerplate');
define('MY_PLUGIN_BOILERPLATE_FILE', __FILE__);
define('MY_PLUGIN_BOILERPLATE_PATH', plugin_dir_path(MY_PLUGIN_BOILERPLATE_FILE));
define('MY_PLUGIN_BOILERPLATE_BASENAME', plugin_basename(MY_PLUGIN_BOILERPLATE_FILE));
define('MY_PLUGIN_BOILERPLATE_SLUG', dirname(MY_PLUGIN_BOILERPLATE_BASENAME));
define('MY_PLUGIN_BOILERPLATE_CSS', plugins_url('assets/css/', __FILE__));
define('MY_PLUGIN_BOILERPLATE_JS', plugins_url('assets/js/', __FILE__));

/**
 * If you don't want to use the autoloading feature, you can comment the following line.
 * 
 * It will include the autoload.php file from the lib directory.
 * Make sure that the autoload.php file exists in the lib directory.
 */
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
	require_once __DIR__ . '/vendor/autoload.php';
}

class MyPluginBoilerplate
{

	/**
	 * This variable is used to implement the Singleton pattern,
	 * ensuring that only one instance of the class exists.
	 * 
	 * @since 1.0.0
	 * @var MyPluginBoilerplate|null $instance The single instance of the class.
	 */
	private static ?MyPluginBoilerplate $instance	= null;

	/**
	 * The name of the plugin.
	 * 
	 * @since 1.0.0
	 * @var string $name The name of the plugin.
	 */
	public static string $name    					= 'My Plugin Boilerplate';

	/**
	 * The text domain for the plugin, used for localization.
	 * 
	 * @since 1.0.0
	 * @var string $slug The text domain for the plugin.
	 */
	public static string $slug						= 'my-plugin-boilerplate';

	/**
	 * The namespace for the plugin, used for autoloading classes.
	 * 
	 * @since 1.0.0
	 * @var string $namespace The namespace for the plugin.
	 */
	public static string $namespace   				= 'MyPluginBoilerplate';

	/**
	 * Get the instance of the MyPluginBoilerplate class.
	 * 
	 * This method implements the Singleton pattern to ensure that only one instance of the class exists.
	 * 
	 * @since 1.0.0
	 * 
	 * @return MyPluginBoilerplate
	 */
	public static function get_instance() : MyPluginBoilerplate
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
	public function plugin_loaded(): void
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
	public static function run_files() : void
	{
		try {
			$path 			= MY_PLUGIN_BOILERPLATE_PATH . 'includes/';

			// Check if the includes directory exists
			if (!is_dir($path)) {
				// translators: %s is the folder path
				throw new RuntimeException(sprintf(__('The folder at %s does not exist', 'my-plugin-boilerplate'), $path));
				return;
			}

			// Get all subdirectories in the includes folder
			$directories	= array_diff(scandir($path), ['.', '..']);

			foreach ($directories as $directory) {
				if (!preg_match('/^[a-zA-Z0-9_-]+$/', $directory)) {
					continue;
				}

				$dir 		= $path . $directory;

				// Only process if it's a directory
				if (!is_dir($dir)) {
					continue;
				}

				// Get all files in the subdirectory
				$files		= array_diff(scandir($dir), ['.', '..']);

				foreach ($files as $file) {
					// Only process files with .php extension
					if (!preg_match('/^[a-zA-Z0-9_-]+\.php$/', $file)) {
						continue;
					}

					$filepath	= $dir . DIRECTORY_SEPARATOR . $file;

					if (!is_readable($filepath)) {
						continue;
					}

					// Get the class name based on folder and file name
					$name 		= basename($file, '.php');
					$class		= self::$namespace . '\\' . $directory . '\\' . $name;

					if (!class_exists($class) || strpos($class, self::$namespace . '\\') !== 0) {
						continue;
					}

					try {
						$instance	= new $class;
						if (method_exists($instance, 'run')) {
							$instance->run();
						}
					} catch (Throwable $innerError) {
						if (defined('WP_DEBUG') && WP_DEBUG === true) {
							error_log(
								sprintf(
									// translators: %1$s is the plugin name, %2$s is the class name, %3$s is the error message, %4$s is the filename, %5$d is the line number
									__('[%1$s] Error running %2$s: %3$s in %4$s on line %5$d', 'my-plugin-boilerplate'),
									self::$name,
									$class,
									$innerError->getMessage(),
									basename($innerError->getFile()),
									$innerError->getLine()
								)
							);
						}
					}
				}
			}
		} catch (Throwable $error) {
			// Log error if WP_DEBUG is enabled
			if (defined('WP_DEBUG') && WP_DEBUG === true) {
				error_log(
					sprintf(
						// translators: %1$s is the plugin name, %2$s is the error message, %3$s is the filename, %4$d is the line number
						__('[%1$s] Error in run_files(): %2$s in %3$s on line %4$d', 'my-plugin-boilerplate'),
						self::$name,
						$error->getMessage(),
						basename($error->getFile()),
						$error->getLine()
					)
				);
			}
		}
	}

	/**
	 * Enqueue CSS & JS
	 * 
	 * @since 1.0.0
	 * 
	 * @return void
	 */
	public static function run_enqueue() : void
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
	 * 
	 * @return void
	 */
	public static function admin_enqueue() : void
	{
		wp_enqueue_style(self::$slug . '-admin', MY_PLUGIN_BOILERPLATE_CSS . 'admin.css', [], MY_PLUGIN_BOILERPLATE_VERSION, 'all');
		wp_enqueue_script(self::$slug . '-admin', MY_PLUGIN_BOILERPLATE_JS . 'admin.js', [], MY_PLUGIN_BOILERPLATE_VERSION, true);
	}

	/**
	 * This method enqueues the styles and scripts for the frontend of the plugin.
	 * It uses WordPress functions to load the CSS and JS files with the appropriate versioning.
	 * 
	 * @since 1.0.0
	 * 
	 * @return void
	 */
	public static function frontend_enqueue() : void
	{
		wp_enqueue_style(self::$slug, MY_PLUGIN_BOILERPLATE_CSS . 'styles.css', [], MY_PLUGIN_BOILERPLATE_VERSION, 'all');
		wp_enqueue_script(self::$slug, MY_PLUGIN_BOILERPLATE_JS . 'scripts.js', [], MY_PLUGIN_BOILERPLATE_VERSION, true);
		wp_enqueue_script(self::$slug . '-ajax', MY_PLUGIN_BOILERPLATE_JS . 'ajax.js', ['jquery'], MY_PLUGIN_BOILERPLATE_VERSION, true);
	}

	/**
	 * This method localizes a script by passing an array of parameters to it.
	 * The parameters can be accessed in the JavaScript file as a global variable.
	 * 
	 * @since 1.0.0
	 * 
	 * @return void
	 */
	public static function localize_jquery() : void
	{
		$parameters = ['test' => 'hello world!'];

		wp_localize_script(self::$slug . '-jquery', 'parameters', $parameters);
	}
}

/**
 * This action hook is triggered when the plugin is loaded.
 * It calls the `plugin_loaded` method of the MyPluginBoilerplate class.
 * 
 * @since 1.0.0
 */
add_action(
	'plugin_loaded',
	function () {
		$MyPluginBoilerplate = MyPluginBoilerplate::get_instance();
		$MyPluginBoilerplate->plugin_loaded();
	}
);
