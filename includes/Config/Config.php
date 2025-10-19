<?php
/**
 * Plugin Configuration Manager
 *
 * Handles dynamic configuration loading based on environment and user settings.
 *
 * @package MyPluginBoilerplate
 *
 * @since 1.0.0
 */

namespace MyPluginBoilerplate\Config;

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
 * Class Config
 *
 * Manages plugin configuration with environment-aware settings.
 *
 * @since 1.0.0
 */
class Config {

	/**
	 * Configuration cache
	 *
	 * @since 1.0.0
	 *
	 * @var array $config Cached configuration values.
	 */
	private static $config = [];

	/**
	 * Default configuration values
	 *
	 * @since 1.0.0
	 *
	 * @var array $defaults Default configuration settings.
	 */
	private static $defaults = [
		'debug'       => [
			'enabled'       => false,
			'log_level'     => 'error', // error, warning, info, debug
			'log_to_file'   => true,
			'log_to_db'     => false,
			'max_log_files' => 10,
		],
		'performance' => [
			'cache_blocks'   => true,
			'cache_duration' => 3600, // 1 hour
			'minify_assets'  => true,
			'lazy_load'      => true,
		],
		'security'    => [
			'rate_limit'        => true,
			'max_requests'      => 100,
			'time_window'       => 3600, // 1 hour
			'strict_validation' => true,
		],
		'features'    => [
			'blocks_enabled'   => true,
			'rest_api_enabled' => true,
			'cli_enabled'      => true,
			'admin_dashboard'  => true,
		],
		'database'    => [
			'auto_optimize'      => false,
			'cleanup_logs'       => true,
			'log_retention_days' => 30,
		],
	];

	/**
	 * Prevent instantiation of the Config class
	 *
	 * @since 1.0.0
	 */
	private function __construct() {}

	/**
	 * Prevent cloning of the Config class
	 *
	 * @since 1.0.0
	 */
	private function __clone() {}

	/**
	 * Prevent unserialization of the Config class
	 *
	 * @since 1.0.0
	 *
	 * @throws \Exception Always throws exception to prevent unserialization.
	 */
	public function __wakeup() {
		throw new \Exception( 'Cannot unserialize a configuration instance.' );
	}

	/**
	 * Initialize configuration system
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function init(): void {
		self::load();
		self::setup();
	}

	/**
	 * Load configuration from various sources
	 *
	 * Priority: Environment Variables > wp-config.php > Database Options > Defaults
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private static function load(): void {
		// Start with defaults
		self::$config = self::$defaults;

		// Override with database options
		$db_config = get_option( 'my_plugin_boilerplate_config', [] );
		if ( ! empty( $db_config ) ) {
			self::$config = self::deep_merge( self::$config, $db_config );
		}

		// Override with constants
		self::const_override();

		// Override with environment variables
		self::env_override();
	}

	/**
	 * Apply constant overrides
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private static function const_override(): void {
		// Debug configuration
		if ( defined( 'MY_PLUGIN_BOILERPLATE_DEBUG' ) ) {
			self::$config['debug']['enabled'] = (bool) constant( 'MY_PLUGIN_BOILERPLATE_DEBUG' );
		}

		if ( defined( 'MY_PLUGIN_BOILERPLATE_LOG_LEVEL' ) ) {
			self::$config['debug']['log_level'] = constant( 'MY_PLUGIN_BOILERPLATE_LOG_LEVEL' );
		}

		// Performance configuration
		if ( defined( 'MY_PLUGIN_BOILERPLATE_CACHE_BLOCKS' ) ) {
			self::$config['performance']['cache_blocks'] = (bool) constant( 'MY_PLUGIN_BOILERPLATE_CACHE_BLOCKS' );
		}

		if ( defined( 'MY_PLUGIN_BOILERPLATE_CACHE_DURATION' ) ) {
			self::$config['performance']['cache_duration'] = (int) constant( 'MY_PLUGIN_BOILERPLATE_CACHE_DURATION' );
		}

		// Security configuration
		if ( defined( 'MY_PLUGIN_BOILERPLATE_RATE_LIMIT' ) ) {
			self::$config['security']['rate_limit'] = (bool) constant( 'MY_PLUGIN_BOILERPLATE_RATE_LIMIT' );
		}
	}

	/**
	 * Apply environment variable overrides
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private static function env_override(): void {
		$mapping = [
			'MY_PLUGIN_DEBUG'        => 'debug.enabled',
			'MY_PLUGIN_LOG_LEVEL'    => 'debug.log_level',
			'MY_PLUGIN_CACHE_BLOCKS' => 'performance.cache_blocks',
			'MY_PLUGIN_RATE_LIMIT'   => 'security.rate_limit',
		];

		foreach ( $mapping as $var => $path ) {
			$value = getenv( $var );

			if ( false !== $value ) {
				self::set_value( self::$config, $path, self::cast_value( $value ) );
			}
		}
	}

	/**
	 * Setup environment-specific overrides
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private static function setup(): void {
		// Development environment
		if ( self::is_development() ) {
			self::$config['debug']['enabled']             = true;
			self::$config['debug']['log_level']           = 'debug';
			self::$config['performance']['minify_assets'] = false;
		}

		// Production environment
		if ( self::is_production() ) {
			self::$config['debug']['enabled']             = false;
			self::$config['debug']['log_level']           = 'error';
			self::$config['performance']['cache_blocks']  = true;
			self::$config['performance']['minify_assets'] = true;
		}

		// Staging environment
		if ( self::is_staging() ) {
			self::$config['debug']['enabled']            = true;
			self::$config['debug']['log_level']          = 'warning';
			self::$config['performance']['cache_blocks'] = false;
		}
	}

	/**
	 * Get a configuration value
	 *
	 * @since 1.0.0
	 *
	 * @param string $key           Configuration key (dot notation supported).
	 * @param mixed  $default_value Default value if key not found.
	 *
	 * @return mixed Configuration value.
	 */
	public static function get( string $key, $default_value = null ) {
		if ( empty( self::$config ) ) {
			self::init();
		}

		return self::get_value( self::$config, $key, $default_value );
	}

	/**
	 * Set a configuration value
	 *
	 * @since 1.0.0
	 *
	 * @param string $key   Configuration key (dot notation supported).
	 * @param mixed  $value Value to set.
	 *
	 * @return void
	 */
	public static function set( string $key, $value ): void {
		if ( empty( self::$config ) ) {
			self::init();
		}

		self::set_value( self::$config, $key, $value );
	}

	/**
	 * Save configuration to database
	 *
	 * @since 1.0.0
	 *
	 * @return bool True on success, false on failure.
	 */
	public static function save(): bool {
		return update_option( 'my_plugin_boilerplate_config', self::$config );
	}

	/**
	 * Reset configuration to defaults
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function reset(): void {
		delete_option( 'my_plugin_boilerplate_config' );
		self::$config = [];
		self::init();
	}

	/**
	 * Get all configuration
	 *
	 * @since 1.0.0
	 *
	 * @return array Complete configuration array.
	 */
	public static function get_config(): array {
		if ( empty( self::$config ) ) {
			self::init();
		}

		return self::$config;
	}

	/**
	 * Check if running in development environment
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if development environment.
	 */
	public static function is_development(): bool {
		return defined( 'WP_DEBUG' ) && WP_DEBUG &&
				( defined( 'MY_PLUGIN_BOILERPLATE_DEVELOPMENT' ) ||
				'development' === wp_get_environment_type() ||
				false !== strpos( home_url(), 'localhost' ) ||
				false !== strpos( home_url(), '.local' ) );
	}

	/**
	 * Check if running in production environment
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if production environment.
	 */
	public static function is_production(): bool {
		return 'production' === wp_get_environment_type() &&
				( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG );
	}

	/**
	 * Check if running in staging environment
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if staging environment.
	 */
	public static function is_staging(): bool {
		return 'staging' === wp_get_environment_type() ||
				defined( 'MY_PLUGIN_BOILERPLATE_STAGING' );
	}

	/**
	 * Deep merge two arrays
	 *
	 * @since 1.0.0
	 *
	 * @param array $array1 First array.
	 * @param array $array2 Second array.
	 *
	 * @return array Merged array.
	 */
	private static function deep_merge( array $array1, array $array2 ): array {
		$merged = $array1;

		foreach ( $array2 as $key => $value ) {
			if ( is_array( $value ) && isset( $merged[ $key ] ) && is_array( $merged[ $key ] ) ) {
				$merged[ $key ] = self::deep_merge( $merged[ $key ], $value );
			} else {
				$merged[ $key ] = $value;
			}
		}

		return $merged;
	}

	/**
	 * Get nested array value using dot notation
	 *
	 * @since 1.0.0
	 *
	 * @param array  $data          Array to search.
	 * @param string $key           Dot notation key.
	 * @param mixed  $default_value Default value.
	 *
	 * @return mixed Found value or default.
	 */
	private static function get_value( array $data, string $key, $default_value = null ) {
		$keys  = explode( '.', $key );
		$value = $data;

		foreach ( $keys as $segment ) {
			if ( is_array( $value ) && array_key_exists( $segment, $value ) ) {
				$value = $value[ $segment ];
			} else {
				return $default_value;
			}
		}

		return $value;
	}

	/**
	 * Set nested array value using dot notation
	 *
	 * @since 1.0.0
	 *
	 * @param array  &$data Array to modify (by reference).
	 * @param string $key   Dot notation key.
	 * @param mixed  $value Value to set.
	 *
	 * @return void
	 */
	private static function set_value( array &$data, string $key, $value ): void {
		$keys    = explode( '.', $key );
		$current = &$data;

		foreach ( $keys as $segment ) {
			if ( ! is_array( $current ) ) {
				$current = [];
			}
			if ( ! array_key_exists( $segment, $current ) ) {
				$current[ $segment ] = [];
			}
			$current = &$current[ $segment ];
		}

		$current = $value;
	}

	/**
	 * Cast environment variable value to appropriate type
	 *
	 * @since 1.0.0
	 *
	 * @param string $value Environment variable value.
	 *
	 * @return mixed Casted value.
	 */
	private static function cast_value( string $value ) {
		// Boolean values
		if ( in_array( strtolower( $value ), [ 'true', '1', 'on', 'yes' ], true ) ) {
			return true;
		}
		if ( in_array( strtolower( $value ), [ 'false', '0', 'off', 'no' ], true ) ) {
			return false;
		}

		// Numeric values
		if ( is_numeric( $value ) ) {
			return false !== strpos( $value, '.' ) ? (float) $value : (int) $value;
		}

		// String values
		return $value;
	}
}
