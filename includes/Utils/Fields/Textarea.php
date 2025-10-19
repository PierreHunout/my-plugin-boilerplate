<?php
/**
 * Textarea Field Class
 *
 * Handles textarea fields with rich text options and validation.
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
 * Class Textarea
 *
 * Handles textarea fields with customizable rows, columns, and rich text support.
 *
 * @since 1.0.0
 */
class Textarea extends Field {

	/**
	 * Setup field-specific properties
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function properties(): void {
		// Set default attributes for textarea
		$this->attributes['rows']  = $this->args['rows'] ?? 5;
		$this->attributes['cols']  = $this->args['cols'] ?? 50;
		$this->attributes['class'] = $this->attributes['class'] ?? 'large-text';

		// Add placeholder if provided
		if ( isset( $this->args['placeholder'] ) ) {
			$this->attributes['placeholder'] = $this->args['placeholder'];
		}

		// Add maxlength if provided
		if ( isset( $this->args['maxlength'] ) ) {
			$this->attributes['maxlength'] = $this->args['maxlength'];
		}
	}

	/**
	 * Render the textarea field
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function render(): void {
		$field_id   = $this->get_field_id();
		$field_name = $this->get_name();
		$value      = esc_textarea( $this->value );
		$attributes = $this->get_attributes();

		// Check if this should be a rich text editor
		if ( ! empty( $this->args['rich_text'] ) ) {
			$this->render_rich_text_editor( $field_id, $field_name, $value );
		} else {
			printf(
				'<textarea id="%s" name="%s" %s>%s</textarea>',
				esc_attr( $field_id ),
				esc_attr( $field_name ),
				esc_attr( $attributes ),
				esc_attr( $value )
			);
		}

		$this->render_description();
	}

	/**
	 * Render rich text editor
	 *
	 * @since 1.0.0
	 *
	 * @param string $field_id   Field ID.
	 * @param string $field_name Field name.
	 * @param string $value      Field value.
	 *
	 * @return void
	 */
	protected function render_rich_text_editor( string $field_id, string $field_name, string $value ): void {
		$editor_settings = [
			'textarea_name' => $field_name,
			'textarea_rows' => $this->attributes['rows'] ?? 5,
			'media_buttons' => $this->args['media_buttons'] ?? false,
			'teeny'         => $this->args['teeny'] ?? true,
			'quicktags'     => $this->args['quicktags'] ?? true,
			'tinymce'       => $this->args['tinymce'] ?? [
				'toolbar1' => 'bold,italic,underline,link,unlink,undo,redo',
				'toolbar2' => '',
				'toolbar3' => '',
			],
		];

		wp_editor( $value, $field_id, $editor_settings );
	}

	/**
	 * Sanitize textarea field value
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

		// If rich text is enabled, use wp_kses_post for HTML sanitization
		if ( ! empty( $this->args['rich_text'] ) ) {
			return wp_kses_post( $value );
		}

		// For plain text, use sanitize_textarea_field
		return sanitize_textarea_field( $value );
	}

	/**
	 * Validate textarea field value
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

		// Check minimum length
		if ( isset( $this->args['minlength'] ) && strlen( $value ) < $this->args['minlength'] ) {
			return sprintf(
				/* translators: %1$s: field title, %2$s: minimum length */
				__( '%1$s must be at least %2$s characters.', 'my-plugin-boilerplate' ),
				$this->title,
				$this->args['minlength']
			);
		}

		// Check maximum length
		if ( isset( $this->args['maxlength'] ) && strlen( $value ) > $this->args['maxlength'] ) {
			return sprintf(
				/* translators: %1$s: field title, %2$s: maximum length */
				__( '%1$s must be no more than %2$s characters.', 'my-plugin-boilerplate' ),
				$this->title,
				$this->args['maxlength']
			);
		}

		// Word count validation
		if ( isset( $this->args['max_words'] ) ) {
			$word_count = str_word_count( wp_strip_all_tags( $value ) );
			if ( $word_count > $this->args['max_words'] ) {
				return sprintf(
					/* translators: %1$s: field title, %2$s: maximum words */
					__( '%1$s must be no more than %2$s words.', 'my-plugin-boilerplate' ),
					$this->title,
					$this->args['max_words']
				);
			}
		}

		if ( isset( $this->args['min_words'] ) ) {
			$word_count = str_word_count( wp_strip_all_tags( $value ) );
			if ( $word_count < $this->args['min_words'] ) {
				return sprintf(
					/* translators: %1$s: field title, %2$s: minimum words */
					__( '%1$s must be at least %2$s words.', 'my-plugin-boilerplate' ),
					$this->title,
					$this->args['min_words']
				);
			}
		}

		return true;
	}
}
