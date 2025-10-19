<?php
/**
 * Select Field Class
 *
 * Handles select dropdown fields with single and multiple selection support.
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
 * Class Select
 *
 * Handles select dropdown fields with options for single/multiple selection.
 *
 * @since 1.0.0
 */
class Select extends Field {

	/**
	 * Select options
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected array $options;

	/**
	 * Multiple selection flag
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
		$this->multiple = $this->args['multiple'] ?? false;

		// Set default class
		$this->attributes['class'] = $this->attributes['class'] ?? 'regular-text';

		// Add multiple attribute if needed
		if ( $this->multiple ) {
			$this->attributes['multiple'] = 'multiple';
		}

		// Add size attribute for multiple selects
		if ( $this->multiple && ! isset( $this->attributes['size'] ) ) {
			$this->attributes['size'] = min( count( $this->options ), 5 );
		}
	}

	/**
	 * Render the select field
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function render(): void {
		$field_id   = $this->get_field_id();
		$field_name = $this->get_name();
		$attributes = $this->get_attributes();

		// For multiple selects, add [] to field name
		if ( $this->multiple ) {
			$field_name .= '[]';
		}

		printf(
			'<select id="%s" name="%s" %s>',
			esc_attr( $field_id ),
			esc_attr( $field_name ),
			esc_attr( $attributes ) // Already escaped in get_attributes_string()
		);

		$this->render_options();

		echo '</select>';

		$this->render_description();
	}

	/**
	 * Render select options
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function render_options(): void {
		// Add empty option if not multiple and no default is selected
		if ( ! $this->multiple && empty( $this->value ) && ! isset( $this->args['placeholder'] ) ) {
			$placeholder = $this->args['placeholder'] ?? __( '-- Select an option --', 'my-plugin-boilerplate' );
			printf(
				'<option value="">%s</option>',
				esc_html( $placeholder )
			);
		}

		// Ensure value is array for multiple selects
		$selected_values = $this->multiple ? (array) $this->value : [ $this->value ];

		foreach ( $this->options as $value => $label ) {
			// Handle optgroups
			if ( is_array( $label ) ) {
				printf( '<optgroup label="%s">', esc_attr( $value ) );
				foreach ( $label as $sub_value => $sub_label ) {
					printf(
						'<option value="%s" %s>%s</option>',
						esc_attr( $sub_value ),
						selected( in_array( (string) $sub_value, array_map( 'strval', $selected_values ), true ), true, false ),
						esc_html( $sub_label )
					);
				}
				echo '</optgroup>';
			} else {
				printf(
					'<option value="%s" %s>%s</option>',
					esc_attr( $value ),
					selected( in_array( (string) $value, array_map( 'strval', $selected_values ), true ), true, false ),
					esc_html( $label )
				);
			}
		}
	}

	/**
	 * Sanitize select field value
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $value Raw field value.
	 *
	 * @return mixed Sanitized field value.
	 */
	public function sanitize( $value ) {
		if ( empty( $value ) ) {
			return $this->multiple ? [] : '';
		}

		// Handle multiple selections
		if ( $this->multiple ) {
			if ( ! is_array( $value ) ) {
				$value = [ $value ];
			}

			$sanitized = [];
			foreach ( $value as $item ) {
				$sanitized_item = sanitize_text_field( $item );
				if ( $this->is_valid_option( $sanitized_item ) ) {
					$sanitized[] = $sanitized_item;
				}
			}

			return $sanitized;
		}

		// Handle single selection
		$sanitized = sanitize_text_field( $value );
		return $this->is_valid_option( $sanitized ) ? $sanitized : '';
	}

	/**
	 * Check if a value is a valid option
	 *
	 * @since 1.0.0
	 *
	 * @param string $value Value to check.
	 *
	 * @return bool
	 */
	protected function is_valid_option( string $value ): bool {
		// Get all valid option values including optgroups
		$valid_values = [];

		foreach ( $this->options as $option_value => $option_label ) {
			if ( is_array( $option_label ) ) {
				// Handle optgroups
				$valid_values = array_merge( $valid_values, array_keys( $option_label ) );
			} else {
				$valid_values[] = $option_value;
			}
		}

		return in_array( $value, array_map( 'strval', $valid_values ), true );
	}

	/**
	 * Validate select field value
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

		// For multiple selects
		if ( $this->multiple ) {
			if ( ! is_array( $value ) ) {
				$value = [ $value ];
			}

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
			foreach ( $value as $item ) {
				if ( ! $this->is_valid_option( $item ) ) {
					return sprintf(
						/* translators: %s: field title */
						__( '%s contains invalid selections.', 'my-plugin-boilerplate' ),
						$this->title
					);
				}
			}
		} else {
			if ( $this->is_valid_option( $value ) ) {
				return true;
			}

			// For single selects, validate the option
			return sprintf(
				/* translators: %s: field title */
				__( '%s has an invalid selection.', 'my-plugin-boilerplate' ),
				$this->title
			);
		}

		return true;
	}
}
