<?php

/**
 * Security Settings Section
 *
 * Handles security configuration settings display and rendering.
 *
 * @package MyPluginBoilerplate\Admin\Sections
 *
 * @since 1.0.0
 */

namespace MyPluginBoilerplate\Admin\Sections;

use MyPluginBoilerplate\Manager\FieldManager;

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
 * Security Settings Section class
 *
 * Manages security configuration tab rendering and display.
 *
 * @since 1.0.0
 */
class Security
{

	/**
	 * Render security settings tab
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function render(): void
	{
		$settings = get_option('my_plugin_boilerplate_settings', []);
		$security = isset($settings['security']) ? $settings['security'] : [];
?>
		<table class="form-table">
			<tr>
				<th scope="row"><?php esc_html_e('Rate Limiting', 'my-plugin-boilerplate'); ?></th>
				<td>
					<?php
					FieldManager::render(
						'checkbox',
						[
							'id'             => 'security_rate_limit',
							'title'          => '',
							'checkbox_label' => __('Enable API rate limiting', 'my-plugin-boilerplate'),
							'value'          => isset($security['rate_limit']) && $security['rate_limit'] ? 1 : 0,
							'checkbox_value' => 1,
							'attributes'     => [
								'name' => 'my_plugin_boilerplate_settings[security][rate_limit]',
							],
						]
					);
					?>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e('Max Requests', 'my-plugin-boilerplate'); ?></th>
				<td>
					<?php
					FieldManager::render(
						'number',
						[
							'id'          => 'security_max_requests',
							'title'       => '',
							'value'       => isset($security['max_requests']) ? $security['max_requests'] : 100,
							'min'         => 10,
							'max'         => 1000,
							'description' => __('Maximum requests per time window.', 'my-plugin-boilerplate'),
							'attributes'  => [
								'name' => 'my_plugin_boilerplate_settings[security][max_requests]',
							],
						]
					);
					?>
				</td>
			</tr>
		</table>
<?php
	}

	/**
	 * Process security section fields
	 *
	 * @since 1.0.0
	 *
	 * @return array Processed security configuration
	 */
	public static function process(): array
	{
		$config = [];

		// Check if we have the correct POST structure
		$post_config = isset($_POST['my_plugin_boilerplate_settings']) ? $_POST['my_plugin_boilerplate_settings'] : [];

		// Rate limit (checkbox)
		$config['security.rate_limit'] = isset($post_config['security']['rate_limit']) && '1' === $post_config['security']['rate_limit'];

		// Max requests (number)
		if (isset($post_config['security']['max_requests'])) {
			$max_requests = absint($post_config['security']['max_requests']);
			if ($max_requests >= 10 && $max_requests <= 1000) {
				$config['security.max_requests'] = $max_requests;
			}
		}

		// Time window (number)
		if (isset($post_config['security']['time_window'])) {
			$time_window = absint($post_config['security']['time_window']);
			if ($time_window >= 60 && $time_window <= 86400) { // 1 minute to 24 hours
				$config['security.time_window'] = $time_window;
			}
		}

		// Strict validation (checkbox)
		$config['security.strict_validation'] = isset($post_config['security']['strict_validation']) && '1' === $post_config['security']['strict_validation'];

		return $config;
	}
}
