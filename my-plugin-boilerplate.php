<?php

/**
 * My Plugin Boilerplate
 *
 * A modern WordPress plugin boilerplate with PSR-4 autoloading, Composer dependencies, and development tools.
 *
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
if (! defined('WPINC')) {
	die;
}

/**
 * Define plugin constants for version, file, path, slug, CSS, JS.
 *
 * @since 1.0.0
 */
define('MY_PLUGIN_BOILERPLATE_VERSION', '1.0.0');
define('MY_PLUGIN_BOILERPLATE_NAME', 'My Plugin Boilerplate');
define('MY_PLUGIN_BOILERPLATE_NAMESPACE', 'MyPluginBoilerplate');
define('MY_PLUGIN_BOILERPLATE_FILE', __FILE__);
define('MY_PLUGIN_BOILERPLATE_PATH', plugin_dir_path(MY_PLUGIN_BOILERPLATE_FILE));
define('MY_PLUGIN_BOILERPLATE_URL', plugin_dir_url(MY_PLUGIN_BOILERPLATE_FILE));
define('MY_PLUGIN_BOILERPLATE_BASENAME', plugin_basename(MY_PLUGIN_BOILERPLATE_FILE));
define('MY_PLUGIN_BOILERPLATE_SLUG', dirname(MY_PLUGIN_BOILERPLATE_BASENAME));
define('MY_PLUGIN_BOILERPLATE_CSS', plugins_url('assets/css/', __FILE__));
define('MY_PLUGIN_BOILERPLATE_JS', plugins_url('assets/js/', __FILE__));

/**
 * If you don't want to use the autoloading feature, you can comment the following line.
 *
 * It will include the autoload.php file from the vendor directory.
 * Make sure that the autoload.php file exists in the vendor directory.
 */
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
	require_once __DIR__ . '/vendor/autoload.php';
}

/**
 * Main plugin class
 *
 * Handles plugin initialization, dependency loading, and core functionality.
 *
 * @since 1.0.0
 */
final class MyPluginBoilerplate
{

	/**
	 * This variable is used to implement the Singleton pattern,
	 * ensuring that only one instance of the class exists.
	 *
	 * @since 1.0.0
	 *
	 * @var MyPluginBoilerplate|null $instance The single instance of the class.
	 */
	protected static ?MyPluginBoilerplate $instance = null;

	/**
	 * The name of the plugin.
	 *
	 * @since 1.0.0
	 *
	 * @var string $name The name of the plugin.
	 */
	public static string $name = 'My Plugin Boilerplate';

	/**
	 * The text domain for the plugin, used for localization.
	 *
	 * @since 1.0.0
	 *
	 * @var string $slug The text domain for the plugin.
	 */
	public static string $slug = 'my-plugin-boilerplate';

	/**
	 * The namespace for the plugin, used for autoloading classes.
	 *
	 * @since 1.0.0
	 *
	 * @var string $namespace The namespace for the plugin.
	 */
	public static string $namespace = 'MyPluginBoilerplate';

	/**
	 * Private constructor to prevent direct instantiation.
	 *
	 * @since 1.0.0
	 */
	private function __construct()
	{
		$this->init();
	}

	/**
	 * Prevent cloning of the class
	 *
	 * @since 1.0.0
	 */
	private function __clone() {}

	/**
	 * Prevent unserialization of the class
	 *
	 * @since 1.0.0
	 *
	 * @throws RuntimeException Always throws exception to prevent unserialization.
	 */
	public function __wakeup()
	{
		throw new RuntimeException('Cannot unserialize a singleton.');
	}

	/**
	 * Get the instance of the MyPluginBoilerplate class.
	 *
	 * This method implements the Singleton pattern to ensure that only one instance of the class exists.
	 *
	 * @since 1.0.0
	 *
	 * @return MyPluginBoilerplate
	 */
	public static function get_instance(): MyPluginBoilerplate
	{
		if (null === self::$instance) {
			self::$instance = new self();
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
	public function init(): void
	{
		self::init_enqueue();
		self::autoload();

		// Register hooks for deactivation and uninstall.
		register_deactivation_hook(__FILE__, ['\MyPluginBoilerplate\Action\Deactivate', 'deactivate']);
		register_uninstall_hook(__FILE__, ['\MyPluginBoilerplate\Action\Uninstall', 'uninstall']);
	}

	/**
	 * Run all files in the includes directory.
	 *
	 * This method scans the 'includes' directory for subdirectories,
	 * and then scans each subdirectory for PHP files.
	 * It instantiates each class found in the files
	 * and calls its `init` method.
	 *
	 * @since 1.0.0
	 *
	 * @throws RuntimeException If the includes directory is not found.
	 *
	 * @return void
	 */
	public static function autoload(): void
	{
		try {
			$base_path = (string) MY_PLUGIN_BOILERPLATE_PATH . 'includes/';

			// Check if the includes directory exists.
			if (! is_dir($base_path)) {
				// translators: %s is the folder path.
				throw new RuntimeException(sprintf(__('The folder at %s does not exist', 'my-plugin-boilerplate'), $base_path));
			}

			// Normalize the base path for security checks.
			$normalized_base = (string) realpath($base_path);
			if (false === $normalized_base) {
				throw new RuntimeException(__('Unable to resolve includes directory path', 'my-plugin-boilerplate'));
			}

			// Get all subdirectories in the includes folder.
			$directories = (array) array_diff(scandir($base_path), ['.', '..']);

			foreach ($directories as $directory) {
				$dir = (string) $base_path . $directory;

				// Only process if it's a directory.
				if (! is_dir($dir)) {
					continue;
				}

				// Security check: ensure we're still within the plugin directory.
				$normalized_dir = (string) realpath($dir);
				if (false === $normalized_dir || 0 !== strpos($normalized_dir, $normalized_base)) {
					continue;
				}

				// Get all files in the subdirectory.
				$files = (array) array_diff(scandir($dir), ['.', '..']);

				foreach ($files as $file) {
					// Enhanced validation: only allow proper PHP class files.
					if (! preg_match('/^[A-Z][a-zA-Z0-9]*\.php$/', $file)) {
						continue;
					}

					$filepath = (string) $dir . DIRECTORY_SEPARATOR . $file;

					// Security check: ensure file is within expected directory.
					$normalized_file = (string) realpath($filepath);
					if (false === $normalized_file || 0 !== strpos($normalized_file, $normalized_dir)) {
						continue;
					}

					if (! is_readable($filepath)) {
						continue;
					}

					// Get the class name based on folder and file name.
					$name  = (string) basename($file, '.php');
					$class = (string) self::$namespace . '\\' . $directory . '\\' . $name;

					// Enhanced class validation.
					if (! class_exists($class) || 0 !== strpos($class, self::$namespace . '\\')) {
						continue;
					}

					// Check if class is safe to instantiate (avoid utility classes).
					$reflection = new \ReflectionClass($class);
					if ($reflection->isAbstract() || $reflection->isTrait() || $reflection->isInterface()) {
						continue;
					}

					// Check if constructor is private (utility classes).
					$constructor = $reflection->getConstructor();
					if (null !== $constructor && ! $constructor->isPublic()) {
						continue;
					}

					try {
						$instance = (object) new $class();
						if (method_exists($instance, 'init') && is_callable([$instance, 'init'])) {
							// Call the init method of the class.
							$instance->init();
						}
					} catch (Throwable $inner_error) {
						if (defined('WP_DEBUG') && WP_DEBUG === true) {
							// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
							error_log(
								sprintf(
									// translators: %1$s is the plugin name, %2$s is the class name, %3$s is the error message, %4$s is the filename, %5$d is the line number.
									__('[%1$s] Error running %2$s: %3$s in %4$s on line %5$d', 'my-plugin-boilerplate'),
									self::$name,
									$class,
									$inner_error->getMessage(),
									basename($inner_error->getFile()),
									$inner_error->getLine()
								)
							);
						}
					}
				}
			}
		} catch (Throwable $error) {
			// Log error if WP_DEBUG is enabled.
			if (defined('WP_DEBUG') && WP_DEBUG === true) {
				// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
				error_log(
					sprintf(
						// translators: %1$s is the plugin name, %2$s is the error message, %3$s is the filename, %4$d is the line number.
						__('[%1$s] Error in autoload(): %2$s in %3$s on line %4$d', 'my-plugin-boilerplate'),
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
	public static function init_enqueue(): void
	{
		add_action('admin_enqueue_scripts', [__CLASS__, 'admin_enqueue'], 1);
		add_action('enqueue_scripts', [__CLASS__, 'frontend_enqueue'], 1);
		add_action('enqueue_scripts', [__CLASS__, 'localize_jquery'], 1);
	}

	/**
	 * This method enqueues the styles and scripts for the admin area of the plugin.
	 * It uses WordPress functions to load the CSS and JS files with the appropriate versioning.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function admin_enqueue(): void
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
	public static function frontend_enqueue(): void
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
	public static function localize_jquery(): void
	{
		$parameters = (array) ['test' => 'hello world!'];

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
		return MyPluginBoilerplate::get_instance();
	}
);
