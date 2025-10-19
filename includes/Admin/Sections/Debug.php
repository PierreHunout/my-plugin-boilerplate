<?php
/**
 * Debug Settings Section
 *
 * Handles debug configuration settings display and rendering.
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
 * Debug Settings Section class
 *
 * Manages debug configuration tab rendering and display.
 *
 * @since 1.0.0
 */
class Debug {

	/**
	 * Render debug settings tab
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function render(): void {
		?>
		<table class="form-table">
			<tr>
				<th scope="row"><?php esc_html_e( 'Enable Debug Mode', 'my-plugin-boilerplate' ); ?></th>
				<td>
					<?php
					FieldManager::render(
						'checkbox',
						[
							'id'             => 'debug_enabled',
							'title'          => '',
							'checkbox_label' => __( 'Enable debug logging and output', 'my-plugin-boilerplate' ),
							'value'          => Config::get( 'debug.enabled' ) ? 1 : 0,
							'checkbox_value' => 1,
							'attributes'     => [
								'name' => 'my_plugin_boilerplate_config[debug][enabled]',
							],
						]
					);
					?>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Log Level', 'my-plugin-boilerplate' ); ?></th>
				<td>
					<?php
					FieldManager::render(
						'select',
						[
							'id'         => 'debug_log_level',
							'title'      => '',
							'value'      => Config::get( 'debug.log_level', 'error' ),
							'options'    => [
								'error'   => __( 'Error', 'my-plugin-boilerplate' ),
								'warning' => __( 'Warning', 'my-plugin-boilerplate' ),
								'info'    => __( 'Info', 'my-plugin-boilerplate' ),
								'debug'   => __( 'Debug', 'my-plugin-boilerplate' ),
							],
							'attributes' => [
								'name' => 'my_plugin_boilerplate_config[debug][log_level]',
							],
						]
					);
					?>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Max Log Files', 'my-plugin-boilerplate' ); ?></th>
				<td>
					<?php
					FieldManager::render(
						'number',
						[
							'id'          => 'debug_max_log_files',
							'title'       => '',
							'value'       => Config::get( 'debug.max_log_files', 10 ),
							'min'         => 1,
							'max'         => 100,
							'description' => __( 'Maximum number of log files to keep before cleanup.', 'my-plugin-boilerplate' ),
							'attributes'  => [
								'name' => 'my_plugin_boilerplate_config[debug][max_log_files]',
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
	 * Process debug section fields
	 *
	 * @since 1.0.0
	 *
	 * @return array Processed debug configuration
	 */
	public static function process(): array {
		$config = [];

		// Debug enabled (checkbox)
		$config['debug.enabled'] = isset( $_POST['debug_enabled'] ) && '1' === $_POST['debug_enabled'];

		// Log level (select)
		if ( isset( $_POST['debug_log_level'] ) ) {
			$log_level = sanitize_text_field( wp_unslash( $_POST['debug_log_level'] ) );
			if ( in_array( $log_level, [ 'error', 'warning', 'info', 'debug' ], true ) ) {
				$config['debug.log_level'] = $log_level;
			}
		}

		// Log to file (checkbox)
		$config['debug.log_to_file'] = isset( $_POST['debug_log_to_file'] ) && '1' === $_POST['debug_log_to_file'];

		// Log to database (checkbox)
		$config['debug.log_to_db'] = isset( $_POST['debug_log_to_db'] ) && '1' === $_POST['debug_log_to_db'];

		// Max log files (number)
		if ( isset( $_POST['debug_max_log_files'] ) ) {
			$max_files = absint( $_POST['debug_max_log_files'] );
			if ( $max_files > 0 && $max_files <= 100 ) {
				$config['debug.max_log_files'] = $max_files;
			}
		}

		return $config;
	}
}