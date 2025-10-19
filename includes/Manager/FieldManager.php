<?php
/**
 * Field Manager Class
 *
 * Manages field creation, registration, and factory methods.
 *
 * @package MyPluginBoilerplate\Admin
 *
 * @since 1.0.0
 */

namespace MyPluginBoilerplate\Manager;

use MyPluginBoilerplate\Utils\Field;
use MyPluginBoilerplate\Utils\Fields\Input;
use MyPluginBoilerplate\Utils\Fields\Textarea;
use MyPluginBoilerplate\Utils\Fields\Select;
use MyPluginBoilerplate\Utils\Fields\Checkbox;
use MyPluginBoilerplate\Utils\Fields\Radio;

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
 * Class FieldManager
 *
 * Factory and utility class for creating and managing field instances.
 *
 * @since 1.0.0
 */
class FieldManager {

	/**
	 * Registered field types
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected static array $field_types = [];

	/**
	 * Created field instances
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected static array $field_instances = [];

	/**
	 * Prevent object instantiation
	 *
	 * @since 1.0.0
	 */
	private function __construct() {}

	/**
	 * Prevent object cloning
	 *
	 * @since 1.0.0
	 */
	private function __clone() {}

	/**
	 * Prevent object unserialization
	 *
	 * @since 1.0.0
	 *
	 * @throws \Exception When attempting to unserialize utility class.
	 */
	public function __wakeup() {
		throw new \Exception( 'Cannot unserialize a utility class.' );
	}

	/**
	 * Initialize field manager and register default field types
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function init(): void {
		self::register_default_types();
	}

	/**
	 * Register default field types
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected static function register_default_types(): void {
		self::register_type( 'text', Input::class );
		self::register_type( 'email', Input::class );
		self::register_type( 'url', Input::class );
		self::register_type( 'password', Input::class );
		self::register_type( 'number', Input::class );
		self::register_type( 'tel', Input::class );
		self::register_type( 'search', Input::class );
		self::register_type( 'textarea', Textarea::class );
		self::register_type( 'select', Select::class );
		self::register_type( 'checkbox', Checkbox::class );
		self::register_type( 'radio', Radio::class );
	}

	/**
	 * Register a field type
	 *
	 * @since 1.0.0
	 *
	 * @param string $type       Field type identifier.
	 * @param string $class_name Field class name.
	 *
	 * @return void
	 */
	public static function register_type( string $type, string $class_name ): void {
		if ( ! class_exists( $class_name ) ) {
			return;
		}

		self::$field_types[ $type ] = $class_name;
	}

	/**
	 * Create a field instance
	 *
	 * @since 1.0.0
	 *
	 * @param string $type Field type.
	 * @param array  $args Field arguments.
	 *
	 * @return Field|null Field instance or null if type not found.
	 */
	public static function create( string $type, array $args = [] ): ?Field {
		if ( ! isset( self::$field_types[ $type ] ) ) {
			return null;
		}

		$class_name = self::$field_types[ $type ];

		// Add input_type for Input fields
		if ( Input::class === $class_name ) {
			$args['input_type'] = $type;
		}

		try {
			$field = new $class_name( $args );

			// Store instance if ID is provided
			if ( ! empty( $args['id'] ) ) {
				self::$field_instances[ $args['id'] ] = $field;
			}

			return $field;
		} catch ( \Exception $e ) {
			// Log error or handle exception
			return null;
		}
	}

	/**
	 * Get a field instance by ID
	 *
	 * @since 1.0.0
	 *
	 * @param string $field_id Field ID.
	 *
	 * @return Field|null Field instance or null if not found.
	 */
	public static function get( string $field_id ): ?Field {
		return self::$field_instances[ $field_id ] ?? null;
	}

	/**
	 * Render a field by type and arguments
	 *
	 * @since 1.0.0
	 *
	 * @param string $type Field type.
	 * @param array  $args Field arguments.
	 *
	 * @return void
	 */
	public static function render( string $type, array $args = [] ): void {
		$field = self::create( $type, $args );

		if ( $field ) {
			$field->render();
		} else {
			printf(
				'<!-- Field type "%s" not found -->',
				esc_html( $type )
			);
		}
	}

	/**
	 * Sanitize field value by type
	 *
	 * @since 1.0.0
	 *
	 * @param string $type  Field type.
	 * @param mixed  $value Field value.
	 * @param array  $args  Field arguments.
	 *
	 * @return mixed Sanitized value.
	 */
	public static function sanitize( string $type, $value, array $args = [] ) {
		$field = self::create( $type, $args );

		if ( $field ) {
			return $field->sanitize( $value );
		}

		// Fallback sanitization
		return sanitize_text_field( $value );
	}
}
