<?php

/**
 * Features Settings Section
 *
 * Handles features configuration settings display and rendering.
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
 * Features Settings Section class
 *
 * Manages features configuration tab rendering and display.
 *
 * @since 1.0.0
 */
class Features
{

	/**
	 * Render features settings tab
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function render(): void
	{
		$settings = get_option('my_plugin_boilerplate_settings', []);
		$features = isset($settings['features']) ? $settings['features'] : [];
?>
		<table class="form-table">
			<tr>
				<th scope="row"><?php esc_html_e('WordPress Blocks', 'my-plugin-boilerplate'); ?></th>
				<td>
					<?php
					FieldManager::render(
						'checkbox',
						[
							'id'             => 'features_blocks_enabled',
							'title'          => '',
							'checkbox_label' => __('Enable WordPress blocks functionality', 'my-plugin-boilerplate'),
							'value'          => isset($features['blocks_enabled']) && $features['blocks_enabled'] ? 1 : 0,
							'checkbox_value' => 1,
							'attributes'     => [
								'name' => 'my_plugin_boilerplate_settings[features][blocks_enabled]',
							],
						]
					);
					?>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e('REST API', 'my-plugin-boilerplate'); ?></th>
				<td>
					<?php
					FieldManager::render(
						'checkbox',
						[
							'id'             => 'features_rest_api_enabled',
							'title'          => '',
							'checkbox_label' => __('Enable REST API endpoints', 'my-plugin-boilerplate'),
							'value'          => isset($features['rest_api_enabled']) && $features['rest_api_enabled'] ? 1 : 0,
							'checkbox_value' => 1,
							'attributes'     => [
								'name' => 'my_plugin_boilerplate_settings[features][rest_api_enabled]',
							],
						]
					);
					?>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e('Admin Dashboard', 'my-plugin-boilerplate'); ?></th>
				<td>
					<?php
					FieldManager::render(
						'checkbox',
						[
							'id'             => 'features_admin_dashboard',
							'title'          => '',
							'checkbox_label' => __('Enable admin dashboard features', 'my-plugin-boilerplate'),
							'value'          => isset($features['admin_dashboard']) && $features['admin_dashboard'] ? 1 : 0,
							'checkbox_value' => 1,
							'attributes'     => [
								'name' => 'my_plugin_boilerplate_settings[features][admin_dashboard]',
							],
						]
					);
					?>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e('Custom Class Name', 'my-plugin-boilerplate'); ?></th>
				<td>
					<?php
					FieldManager::render(
						'text',
						[
							'id'          => 'features_custom_class',
							'title'       => '',
							'value'       => isset($features['custom_class']) ? $features['custom_class'] : '',
							'placeholder' => __('Enter custom CSS class name', 'my-plugin-boilerplate'),
							'description' => __('Custom CSS class to add to plugin elements.', 'my-plugin-boilerplate'),
							'attributes'  => [
								'name' => 'my_plugin_boilerplate_settings[features][custom_class]',
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
	 * Process features section fields
	 *
	 * @since 1.0.0
	 *
	 * @return array Processed features configuration
	 */
	public static function process(): array
	{
		$config = [];

		// Check if we have the correct POST structure
		$post_config = isset($_POST['my_plugin_boilerplate_settings']) ? $_POST['my_plugin_boilerplate_settings'] : [];

		// Blocks enabled (checkbox)
		$config['features.blocks_enabled'] = isset($post_config['features']['blocks_enabled']) && '1' === $post_config['features']['blocks_enabled'];

		// REST API enabled (checkbox)
		$config['features.rest_api_enabled'] = isset($post_config['features']['rest_api_enabled']) && '1' === $post_config['features']['rest_api_enabled'];

		// CLI enabled (checkbox)
		$config['features.cli_enabled'] = isset($post_config['features']['cli_enabled']) && '1' === $post_config['features']['cli_enabled'];

		// Admin dashboard (checkbox)
		$config['features.admin_dashboard'] = isset($post_config['features']['admin_dashboard']) && '1' === $post_config['features']['admin_dashboard'];

		return $config;
	}
}
