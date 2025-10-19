<?php
/**
 * Radio Field Class
 *
 * Handles radio button fields with proper grouping and validation.
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
 * Class Radio
 *
 * Handles radio button fields with proper grouping and accessibility.
 *
 * @since 1.0.0
 */
class Radio extends Field {

	/**
	 * Radio options
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected array $options;

	/**
	 * Display layout (vertical or horizontal)
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected string $layout;

	/**
	 * Setup field-specific properties
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function properties(): void {
		$this->options = $this->args['options'] ?? [];
		$this->layout  = $this->args['layout'] ?? 'vertical';

		// Ensure we have options
		if ( empty( $this->options ) ) {
			$this->options = [
				'yes' => __( 'Yes', 'my-plugin-boilerplate' ),
				'no'  => __( 'No', 'my-plugin-boilerplate' ),
			];
		}
	}

	/**
	 * Render the radio field
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function render(): void {
		$field_name    = $this->get_name();
		$current_value = (string) $this->value;

		echo '<fieldset>';

		if ( ! empty( $this->title ) ) {
			printf( '<legend class="screen-reader-text">%s</legend>', esc_html( $this->title ) );
		}

		$container_class = 'horizontal' === $this->layout ? 'radio-horizontal' : 'radio-vertical';
		printf( '<div class="%s">', esc_attr( $container_class ) );

		foreach ( $this->options as $value => $label ) {
			$this->render_option( $field_name, $value, $label, $current_value );

			// Add line break for vertical layout
			if ( 'vertical' === $this->layout ) {
				echo '<br />';
			}
		}

		echo '</div>';
		echo '</fieldset>';

		$this->render_description();
	}

	/**
	 * Render individual radio option
	 *
	 * @since 1.0.0
	 *
	 * @param string $field_name    Field name.
	 * @param string $value         Option value.
	 * @param string $label         Option label.
	 * @param string $current_value Current selected value.
	 *
	 * @return void
	 */
	protected function render_option( string $field_name, string $value, string $label, string $current_value ): void {
		$radio_id   = $this->get_field_id() . '_' . sanitize_title( $value );
		$checked    = checked( $current_value, (string) $value, false );
		$attributes = $this->get_attributes();

		printf(
			'<label for="%s" class="radio-label">',
			esc_attr( $radio_id )
		);

		printf(
			'<input type="radio" id="%s" name="%s" value="%s" %s %s />',
			esc_attr( $radio_id ),
			esc_attr( $field_name ),
			esc_attr( $value ),
			esc_attr( $checked ),
			esc_attr( $attributes )
		);

		echo ' ' . esc_html( $label );
		echo '</label>';

		// Add spacing for horizontal layout
		if ( 'horizontal' === $this->layout ) {
			echo ' &nbsp;&nbsp; ';
		}
	}

	/**
	 * Sanitize radio field value
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $value Raw field value.
	 *
	 * @return string Sanitized field value.
	 */
	public function sanitize( $value ) {
		if ( empty( $value ) ) {
			return '';
		}

		$sanitized     = sanitize_text_field( $value );
		$valid_options = array_keys( $this->options );

		// Return the value only if it's a valid option
		return in_array( $sanitized, array_map( 'strval', $valid_options ), true ) ? $sanitized : '';
	}

	/**
	 * Validate radio field value
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

		// Check if the value is a valid option
		$valid_options = array_keys( $this->options );
		if ( ! in_array( $value, array_map( 'strval', $valid_options ), true ) ) {
			return sprintf(
				/* translators: %s: field title */
				__( '%s has an invalid selection.', 'my-plugin-boilerplate' ),
				$this->title
			);
		}

		return true;
	}

	/**
	 * Get field attributes string (override to exclude type attribute)
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected function get_attributes(): string {
		$attributes = [];

		// Add common attributes
		if ( ! empty( $this->args['required'] ) ) {
			$attributes[] = 'required="required"';
		}

		if ( ! empty( $this->args['readonly'] ) ) {
			$attributes[] = 'readonly="readonly"';
		}

		if ( ! empty( $this->args['disabled'] ) ) {
			$attributes[] = 'disabled="disabled"';
		}

		// Add custom attributes (excluding type since it's set to radio)
		foreach ( $this->attributes as $attr => $value ) {
			if ( 'type' === $attr ) {
				continue; // Skip type attribute for radio buttons
			}

			if ( is_bool( $value ) ) {
				if ( $value ) {
					$attributes[] = esc_attr( $attr ) . '="' . esc_attr( $attr ) . '"';
				}
			} else {
				$attributes[] = esc_attr( $attr ) . '="' . esc_attr( $value ) . '"';
			}
		}

		return implode( ' ', $attributes );
	}
}
