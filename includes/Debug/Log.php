<?php
/**
 * This file is responsible for handling the logging functionality in the WordPress plugin.
 * It includes methods to run the logging functionality and can be extended in the future.
 *
 * @package MyPluginBoilerplate
 *
 * @since 1.0.0
 */

namespace MyPluginBoilerplate\Debug;

use MyPluginBoilerplate\MyPluginBoilerplate;
use MyPluginBoilerplate\Helpers;
use DateTime;
use DateTimeZone;

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
 * Class Log
 *
 * Handles Log functionality for the plugin.
 *
 * @since 1.0.0
 */
class Log {

	/**
	 * Logs data to a specified file.
	 * Only logs when MY_PLUGIN_BOILERPLATE_DEBUG is enabled.
	 *
	 * @since 1.0.0
	 *
	 * @param string $file The log file name (without extension).
	 * @param mixed  $data The data to log.
	 *
	 * @return void
	 */
	public static function log( $file, $data ) {
		// Only log when MY_PLUGIN_BOILERPLATE_DEBUG is enabled.
		if ( ! defined( 'MY_PLUGIN_BOILERPLATE_DEBUG' ) || ! MY_PLUGIN_BOILERPLATE_DEBUG ) {
			return;
		}

		// Store logs in wp-content/my-plugin-boilerplate-logs/ for persistence and security
		$slug = MyPluginBoilerplate::$slug;
		$path = WP_CONTENT_DIR . $slug . '/-logs/';

		$filesystem = Helpers::get_filesystem();

		if ( ! $filesystem->is_dir( $path ) ) {
			$filesystem->mkdir( $path, 0755 );

			// Add .htaccess for security (deny direct access to log files)
			$htaccess = "# Deny access to log files\n<Files \"*.json\">\n\tOrder allow,deny\n\tDeny from all\n</Files>\n\n# Deny access to directory listing\nOptions -Indexes";
			$filesystem->put_contents( $path . '.htaccess', $htaccess, 0644 );

			// Add index.php to prevent directory browsing
			$index = "<?php\n// Code so quiet, you can hear the silence.\n";
			$filesystem->put_contents( $path . 'index.php', $index, 0644 );
		}

		$file_path = $path . sanitize_file_name( $file ) . '-' . time() . '.json';
		$type      = gettype( $data );
		$date      = new DateTime( 'now', new DateTimeZone( 'UTC' ) );
		$date      = $date->format( 'Y-m-d H:i:sP' );

		$log = [
			'date' => $date,
			'type' => $type,
			'data' => $data,
		];

		$json_data = wp_json_encode( $log, JSON_PRETTY_PRINT );

		if ( false !== $json_data ) {
			// Attempt to write log file with error handling
			$filesystem->put_contents( $file_path, PHP_EOL . $json_data, 0644 );
		}
	}

	/**
	 * Prints data in a styled HTML block for debugging.
	 * Only outputs when MY_PLUGIN_BOILERPLATE_DEBUG is enabled.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $data The data to display.
	 * @param bool  $stop Whether to stop execution after output.
	 *
	 * @return void
	 */
	public static function print( $data, $stop = false ) {
		// Only output debug information when MY_PLUGIN_BOILERPLATE_DEBUG is enabled.
		if ( ! defined( 'MY_PLUGIN_BOILERPLATE_DEBUG' ) || ! MY_PLUGIN_BOILERPLATE_DEBUG ) {
			return;
		}

		echo '<div style="position: relative; margin: 24px 0; background: #2271b1; color: #fafafa; padding: 20px; border-radius: 3px; z-index: 9999;"><pre style="white-space: pre-wrap; word-wrap: break-word;">';

		// Get plugin name
		$name = MyPluginBoilerplate::$name;
		echo '<strong style="color: #b5ddfeff;">' . esc_html( $name ) . ' Debug Output:</strong>' . PHP_EOL . PHP_EOL;

		// Get $data type
		$data_type = gettype( $data );
		echo '<strong style="color: #b5ddfeff;">Type: ' . esc_html( $data_type ) . '</strong>' . PHP_EOL . PHP_EOL;

		// Use wp_json_encode for safe and readable output.
		$json_output = wp_json_encode( $data, JSON_PRETTY_PRINT );
		echo esc_html( $json_output ? $json_output : 'Unable to encode data' );

		echo '</pre></div>';

		if ( true === $stop ) {
			wp_die( 'Debug output terminated.' );
		}
	}
}
