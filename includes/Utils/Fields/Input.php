<?php
/**
 * Input Field Class
 *
 * Handles text-based input fields (text, email, url, password, etc.)
 *
 * @package MyPluginBoilerplate\Utils\Fields
 *
 * @since 1.0.0
 */

namespace MyPluginBoilerplate\Utils\Fields;

use MyPluginBoilerplate\Utils\Field;

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
 * Class Input
 *
 * Handles various input field types with proper sanitization and validation.
 *
 * @since 1.0.0
 */
class Input extends Field {

	/**
	 * Input type (text, email, url, password, etc.)
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected string $input_type;

	/**
	 * Setup field-specific properties
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function properties(): void {
		$this->input_type = $this->args['input_type'] ?? 'text';

		// Add default attributes based on input type
		$this->setup_attributes();
	}

	/**
	 * Setup input-specific attributes
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function setup_attributes(): void {
		// Set default attributes based on input type
		switch ( $this->input_type ) {
			case 'email':
				$this->attributes['type'] = 'email';
				break;
			case 'url':
				$this->attributes['type'] = 'url';
				break;
			case 'password':
				$this->attributes['type'] = 'password';
				break;
			case 'number':
				$this->attributes['type'] = 'number';
				if ( isset( $this->args['min'] ) ) {
					$this->attributes['min'] = $this->args['min'];
				}
				if ( isset( $this->args['max'] ) ) {
					$this->attributes['max'] = $this->args['max'];
				}
				if ( isset( $this->args['step'] ) ) {
					$this->attributes['step'] = $this->args['step'];
				}
				break;
			case 'tel':
				$this->attributes['type'] = 'tel';
				break;
			case 'search':
				$this->attributes['type'] = 'search';
				break;
			default:
				$this->attributes['type'] = 'text';
				break;
		}

		// Add common input attributes
		if ( isset( $this->args['placeholder'] ) ) {
			$this->attributes['placeholder'] = $this->args['placeholder'];
		}

		if ( isset( $this->args['maxlength'] ) ) {
			$this->attributes['maxlength'] = $this->args['maxlength'];
		}

		if ( isset( $this->args['pattern'] ) ) {
			$this->attributes['pattern'] = $this->args['pattern'];
		}

		$this->attributes['class'] = $this->attributes['class'] ?? 'regular-text';
	}

	/**
	 * Render the input field
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function render(): void {
		$field_id   = $this->get_field_id();
		$field_name = $this->get_name();
		$value      = esc_attr( $this->value );
		$attributes = $this->get_attributes();

		printf(
			'<input id="%s" name="%s" value="%s" %s />',
			esc_attr( $field_id ),
			esc_attr( $field_name ),
			esc_attr( $value ),
			esc_attr( $attributes )
		);

		$this->render_description();
	}

	/**
	 * Sanitize input field value
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $value Raw field value.
	 *
	 * @return mixed Sanitized field value.
	 */
	public function sanitize( $value ) {
		if ( empty( $value ) ) {
			return '';
		}

		switch ( $this->input_type ) {
			case 'email':
				return sanitize_email( $value );
			case 'url':
				return esc_url_raw( $value );
			case 'number':
				return is_numeric( $value ) ? floatval( $value ) : 0;
			case 'tel':
				// Remove non-numeric characters except +, -, (, ), and spaces
				return preg_replace( '/[^0-9+\-() ]/', '', $value );
			default:
				return sanitize_text_field( $value );
		}
	}

	/**
	 * Validate input field value
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $value Field value to validate.
	 *
	 * @return bool|string True if valid, error message if invalid.
	 */
	public function validate( $value ) {
		// Call parent validation first
		$parent_validation = parent::validate( $value );
		if ( true !== $parent_validation ) {
			return $parent_validation;
		}

		// Skip validation if field is empty and not required
		if ( empty( $value ) && empty( $this->args['required'] ) ) {
			return true;
		}

		// Type-specific validation
		switch ( $this->input_type ) {
			case 'email':
				if ( ! is_email( $value ) ) {
					return sprintf(
						/* translators: %s: field title */
						__( '%s must be a valid email address.', 'my-plugin-boilerplate' ),
						$this->title
					);
				}
				break;

			case 'url':
				if ( ! filter_var( $value, FILTER_VALIDATE_URL ) ) {
					return sprintf(
						/* translators: %s: field title */
						__( '%s must be a valid URL.', 'my-plugin-boilerplate' ),
						$this->title
					);
				}
				break;

			case 'number':
				if ( ! is_numeric( $value ) ) {
					return sprintf(
						/* translators: %s: field title */
						__( '%s must be a valid number.', 'my-plugin-boilerplate' ),
						$this->title
					);
				}

				// Check min/max constraints
				$num_value = floatval( $value );
				if ( isset( $this->args['min'] ) && $num_value < $this->args['min'] ) {
					return sprintf(
						/* translators: %1$s: field title, %2$s: minimum value */
						__( '%1$s must be at least %2$s.', 'my-plugin-boilerplate' ),
						$this->title,
						$this->args['min']
					);
				}

				if ( isset( $this->args['max'] ) && $num_value > $this->args['max'] ) {
					return sprintf(
						/* translators: %1$s: field title, %2$s: maximum value */
						__( '%1$s must be no more than %2$s.', 'my-plugin-boilerplate' ),
						$this->title,
						$this->args['max']
					);
				}
				break;
		}

		// Check maxlength
		if ( isset( $this->args['maxlength'] ) && strlen( $value ) > $this->args['maxlength'] ) {
			return sprintf(
				/* translators: %1$s: field title, %2$s: maximum length */
				__( '%1$s must be no more than %2$s characters.', 'my-plugin-boilerplate' ),
				$this->title,
				$this->args['maxlength']
			);
		}

		// Check pattern if provided
		if ( isset( $this->args['pattern'] ) && ! preg_match( '/' . $this->args['pattern'] . '/', $value ) ) {
			$pattern_message = $this->args['pattern_message'] ?? sprintf(
				/* translators: %s: field title */
				__( '%s format is invalid.', 'my-plugin-boilerplate' ),
				$this->title
			);
			return $pattern_message;
		}

		return true;
	}
}
