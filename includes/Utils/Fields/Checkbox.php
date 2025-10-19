<?php
/**
 * Checkbox Field Class
 *
 * Handles checkbox fields with single and multiple checkbox support.
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
 * Class Checkbox
 *
 * Handles checkbox fields with support for single checkboxes and checkbox groups.
 *
 * @since 1.0.0
 */
class Checkbox extends Field {

	/**
	 * Checkbox options (for multiple checkboxes)
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected array $options;

	/**
	 * Multiple checkboxes flag
	 *
	 * @since 1.0.0
	 *
	 * @var bool
	 */
	protected bool $multiple;

	/**
	 * Setup field-specific properties
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function properties(): void {
		$this->options  = $this->args['options'] ?? [];
		$this->multiple = ! empty( $this->options );

		// Set checkbox value (1 for single checkbox, array for multiple)
		if ( ! $this->multiple && ! isset( $this->args['checkbox_value'] ) ) {
			$this->args['checkbox_value'] = 1;
		}
	}

	/**
	 * Render the checkbox field
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function render(): void {
		if ( $this->multiple ) {
			$this->render_group();
		} else {
			$this->render_single();
		}

		$this->render_description();
	}

	/**
	 * Render single checkbox
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function render_single(): void {
		$field_id       = $this->get_field_id();
		$field_name     = $this->get_name();
		$checkbox_value = $this->args['checkbox_value'] ?? 1;
		$checked        = checked( $this->value, $checkbox_value, false );
		$attributes     = $this->get_attributes();

		printf(
			'<label for="%s">',
			esc_attr( $field_id )
		);

		printf(
			'<input type="checkbox" id="%s" name="%s" value="%s" %s %s />',
			esc_attr( $field_id ),
			esc_attr( $field_name ),
			esc_attr( $checkbox_value ),
			esc_attr( $checked ),
			esc_attr( $attributes )
		);

		if ( ! empty( $this->args['checkbox_label'] ) ) {
			echo ' ' . esc_html( $this->args['checkbox_label'] );
		}

		echo '</label>';
	}

	/**
	 * Render checkbox group
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function render_group(): void {
		$field_name      = $this->get_name() . '[]';
		$selected_values = (array) $this->value;

		echo '<fieldset>';

		if ( ! empty( $this->title ) ) {
			printf( '<legend class="screen-reader-text">%s</legend>', esc_html( $this->title ) );
		}

		foreach ( $this->options as $value => $label ) {
			$checkbox_id = $this->get_field_id() . '_' . sanitize_title( $value );
			$checked     = in_array( (string) $value, array_map( 'strval', $selected_values ), true ) ? 'checked="checked"' : '';
			$attributes  = $this->get_attributes();

			printf(
				'<label for="%s">',
				esc_attr( $checkbox_id )
			);

			printf(
				'<input type="checkbox" id="%s" name="%s" value="%s" %s %s />',
				esc_attr( $checkbox_id ),
				esc_attr( $field_name ),
				esc_attr( $value ),
				esc_attr( $checked ),
				esc_attr( $attributes )
			);

			echo ' ' . esc_html( $label ) . '</label><br />';
		}

		echo '</fieldset>';
	}

	/**
	 * Sanitize checkbox field value
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $value Raw field value.
	 *
	 * @return mixed Sanitized field value.
	 */
	public function sanitize( $value ) {
		// Handle multiple checkboxes
		if ( $this->multiple ) {
			if ( empty( $value ) || ! is_array( $value ) ) {
				return [];
			}

			$sanitized     = [];
			$valid_options = array_keys( $this->options );

			foreach ( $value as $item ) {
				$sanitized_item = sanitize_text_field( $item );
				if ( in_array( $sanitized_item, array_map( 'strval', $valid_options ), true ) ) {
					$sanitized[] = $sanitized_item;
				}
			}

			return $sanitized;
		}

		// Handle single checkbox
		$checkbox_value = $this->args['checkbox_value'] ?? 1;
		return ! empty( $value ) ? $checkbox_value : 0;
	}

	/**
	 * Validate checkbox field value
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

		// For required checkboxes
		if ( ! empty( $this->args['required'] ) ) {
			if ( $this->multiple ) {
				if ( empty( $value ) || ! is_array( $value ) || count( $value ) === 0 ) {
					return sprintf(
						/* translators: %s: field title */
						__( '%s requires at least one selection.', 'my-plugin-boilerplate' ),
						$this->title
					);
				}
			} elseif ( empty( $value ) ) {
					return sprintf(
						/* translators: %s: field title */
						__( '%s must be checked.', 'my-plugin-boilerplate' ),
						$this->title
					);
			}
		}

		// For multiple checkboxes, validate selection counts
		if ( $this->multiple && ! empty( $value ) && is_array( $value ) ) {
			// Check minimum selections
			if ( isset( $this->args['min_selections'] ) && count( $value ) < $this->args['min_selections'] ) {
				return sprintf(
					/* translators: %1$s: field title, %2$s: minimum selections */
					__( '%1$s requires at least %2$s selections.', 'my-plugin-boilerplate' ),
					$this->title,
					$this->args['min_selections']
				);
			}

			// Check maximum selections
			if ( isset( $this->args['max_selections'] ) && count( $value ) > $this->args['max_selections'] ) {
				return sprintf(
					/* translators: %1$s: field title, %2$s: maximum selections */
					__( '%1$s allows no more than %2$s selections.', 'my-plugin-boilerplate' ),
					$this->title,
					$this->args['max_selections']
				);
			}

			// Validate each selected value
			$valid_options = array_keys( $this->options );
			foreach ( $value as $item ) {
				if ( ! in_array( $item, array_map( 'strval', $valid_options ), true ) ) {
					return sprintf(
						/* translators: %s: field title */
						__( '%s contains invalid selections.', 'my-plugin-boilerplate' ),
						$this->title
					);
				}
			}
		}

		return true;
	}
}
