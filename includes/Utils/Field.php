<?php

/**
 * Abstract Field Base Class
 *
 * Provides a base structure for all WordPress admin fields.
 *
 * @package MyPluginBoilerplate\Admin
 *
 * @since 1.0.0
 */

namespace MyPluginBoilerplate\Utils;

use Exception;

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
 * Abstract Class Field
 *
 * Base class for all field types. Provides common functionality
 * and enforces consistent structure across all field implementations.
 *
 * @since 1.0.0
 */
abstract class Field
{

	/**
	 * Field ID
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected string $id;

	/**
	 * Field title/label
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected string $title;

	/**
	 * Field description
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected string $description;

	/**
	 * Field value
	 *
	 * @since 1.0.0
	 *
	 * @var mixed
	 */
	protected $value;

	/**
	 * Field attributes
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected array $attributes;

	/**
	 * Field arguments
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected array $args;

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Field configuration arguments.
	 */
	public function __construct(array $args = [])
	{
		$this->setup($args);
		$this->properties();
	}

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
	 * @throws Exception When attempting to unserialize utility class.
	 */
	public function __wakeup()
	{
		throw new Exception('Cannot unserialize a field instance.');
	}

	/**
	 * Setup field configuration
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Field configuration arguments.
	 *
	 * @return void
	 */
	protected function setup(array $args): void
	{
		$defaults = [
			'id'          => '',
			'title'       => '',
			'description' => '',
			'value'       => '',
			'attributes'  => [],
			'required'    => false,
			'readonly'    => false,
			'disabled'    => false,
		];

		$this->args        = wp_parse_args($args, $defaults);
		$this->id          = $this->args['id'];
		$this->title       = $this->args['title'];
		$this->description = $this->args['description'];
		$this->value       = $this->args['value'];
		$this->attributes  = $this->args['attributes'];
	}

	/**
	 * Setup field-specific properties
	 *
	 * Override this method in child classes to set up field-specific properties.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function properties(): void
	{
		// Override in child classes
	}

	/**
	 * Render the field
	 *
	 * Abstract method that must be implemented by all field types.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	abstract public function render(): void;

	/**
	 * Sanitize field value
	 *
	 * Abstract method for field-specific sanitization.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $value Raw field value.
	 *
	 * @return mixed Sanitized field value.
	 */
	abstract public function sanitize($value);

	/**
	 * Validate field value
	 *
	 * Override this method in child classes for field-specific validation.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $value Field value to validate.
	 *
	 * @return bool|string True if valid, error message if invalid.
	 */
	public function validate($value)
	{
		// Basic required field validation
		if (! empty($this->args['required']) && empty($value)) {
			return sprintf(
				/* translators: %s: field title */
				__('%s is required.', 'my-plugin-boilerplate'),
				$this->title
			);
		}

		return true;
	}

	/**
	 * Get field ID
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_id(): string
	{
		return $this->id;
	}

	/**
	 * Get field title
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_title(): string
	{
		return $this->title;
	}

	/**
	 * Get field description
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_description(): string
	{
		return $this->description;
	}

	/**
	 * Get field value
	 *
	 * @since 1.0.0
	 *
	 * @return mixed
	 */
	public function get_value()
	{
		return $this->value;
	}

	/**
	 * Set field value
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $value New field value.
	 *
	 * @return void
	 */
	public function set_value($value): void
	{
		$this->value = $value;
	}

	/**
	 * Get field attributes as HTML string
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected function get_attributes(): string
	{
		$attributes = [];

		// Add common attributes
		if (! empty($this->args['required'])) {
			$attributes[] = 'required="required"';
		}

		if (! empty($this->args['readonly'])) {
			$attributes[] = 'readonly="readonly"';
		}

		if (! empty($this->args['disabled'])) {
			$attributes[] = 'disabled="disabled"';
		}

		// Add custom attributes
		foreach ($this->attributes as $attr => $value) {
			if (is_bool($value)) {
				if ($value) {
					$attributes[] = esc_attr($attr) . '="' . esc_attr($attr) . '"';
				}
			} else {
				$attributes[] = esc_attr($attr) . '="' . esc_attr($value) . '"';
			}
		}

		return implode(' ', $attributes);
	}

	/**
	 * Render field description
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function render_description(): void
	{
		if (! empty($this->description)) {
			echo '<p class="description">' . wp_kses_post($this->description) . '</p>';
		}
	}

	/**
	 * Get field name attribute for forms
	 *
	 * @since 1.0.0
	 *
	 * @param string $option_name Optional. Option name prefix.
	 *
	 * @return string
	 */
	protected function get_name(string $option_name = 'my_plugin_boilerplate_settings'): string
	{
		// Check if custom name is provided in attributes
		if (isset($this->args['attributes']['name'])) {
			return $this->args['attributes']['name'];
		}

		return $option_name . '[' . $this->id . ']';
	}

	/**
	 * Get field ID attribute for forms
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected function get_field_id(): string
	{
		return 'my_plugin_boilerplate_' . $this->id;
	}
}
