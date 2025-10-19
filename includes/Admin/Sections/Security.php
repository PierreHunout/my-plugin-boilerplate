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

use MyPluginBoilerplate\Config\Config;
use MyPluginBoilerplate\Manager\FieldManager;

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
 * Security Settings Section class
 *
 * Manages security configuration tab rendering and display.
 *
 * @since 1.0.0
 */
class Security {

	/**
	 * Render security settings tab
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function render(): void {
		?>
		<table class="form-table">
			<tr>
				<th scope="row"><?php esc_html_e( 'Rate Limiting', 'my-plugin-boilerplate' ); ?></th>
				<td>
					<?php
					FieldManager::render(
						'checkbox',
						[
							'id'             => 'security_rate_limit',
							'title'          => '',
							'checkbox_label' => __( 'Enable API rate limiting', 'my-plugin-boilerplate' ),
							'value'          => Config::get( 'security.rate_limit' ) ? 1 : 0,
							'checkbox_value' => 1,
							'attributes'     => [
								'name' => 'my_plugin_boilerplate_config[security][rate_limit]',
							],
						]
					);
					?>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Max Requests', 'my-plugin-boilerplate' ); ?></th>
				<td>
					<?php
					FieldManager::render(
						'number',
						[
							'id'          => 'security_max_requests',
							'title'       => '',
							'value'       => Config::get( 'security.max_requests' ),
							'min'         => 10,
							'max'         => 1000,
							'description' => __( 'Maximum requests per time window.', 'my-plugin-boilerplate' ),
							'attributes'  => [
								'name' => 'my_plugin_boilerplate_config[security][max_requests]',
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
	public static function process(): array {
		$config = [];

		// Rate limit (checkbox)
		$config['security.rate_limit'] = isset( $_POST['security_rate_limit'] ) && '1' === $_POST['security_rate_limit'];

		// Max requests (number)
		if ( isset( $_POST['security_max_requests'] ) ) {
			$max_requests = absint( $_POST['security_max_requests'] );
			if ( $max_requests >= 10 && $max_requests <= 1000 ) {
				$config['security.max_requests'] = $max_requests;
			}
		}

		// Time window (number)
		if ( isset( $_POST['security_time_window'] ) ) {
			$time_window = absint( $_POST['security_time_window'] );
			if ( $time_window >= 60 && $time_window <= 86400 ) { // 1 minute to 24 hours
				$config['security.time_window'] = $time_window;
			}
		}

		// Strict validation (checkbox)
		$config['security.strict_validation'] = isset( $_POST['security_strict_validation'] ) && '1' === $_POST['security_strict_validation'];

		return $config;
	}
}