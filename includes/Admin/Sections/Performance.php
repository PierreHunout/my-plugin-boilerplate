<?php
/**
 * Performance Settings Section
 *
 * Handles performance configuration settings display and rendering.
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
 * Performance Settings Section class
 *
 * Manages performance configuration tab rendering and display.
 *
 * @since 1.0.0
 */
class Performance {

	/**
	 * Render performance settings tab
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function render(): void {
		?>
		<table class="form-table">
			<tr>
				<th scope="row"><?php esc_html_e( 'Cache Blocks', 'my-plugin-boilerplate' ); ?></th>
				<td>
					<?php
					FieldManager::render(
						'checkbox',
						[
							'id'             => 'performance_cache_blocks',
							'title'          => '',
							'checkbox_label' => __( 'Enable block registration caching', 'my-plugin-boilerplate' ),
							'value'          => Config::get( 'performance.cache_blocks' ) ? 1 : 0,
							'checkbox_value' => 1,
							'attributes'     => [
								'name' => 'my_plugin_boilerplate_config[performance][cache_blocks]',
							],
						]
					);
					?>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Cache Duration', 'my-plugin-boilerplate' ); ?></th>
				<td>
					<?php
					FieldManager::render(
						'number',
						[
							'id'          => 'performance_cache_duration',
							'title'       => '',
							'value'       => Config::get( 'performance.cache_duration', 3600 ),
							'min'         => 300,
							'max'         => 86400,
							'description' => __( 'Cache duration in seconds (5 minutes to 24 hours).', 'my-plugin-boilerplate' ),
							'attributes'  => [
								'name' => 'my_plugin_boilerplate_config[performance][cache_duration]',
							],
						]
					);
					?>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Minify Assets', 'my-plugin-boilerplate' ); ?></th>
				<td>
					<?php
					FieldManager::render(
						'checkbox',
						[
							'id'             => 'performance_minify_assets',
							'title'          => '',
							'checkbox_label' => __( 'Enable asset minification', 'my-plugin-boilerplate' ),
							'value'          => Config::get( 'performance.minify_assets' ) ? 1 : 0,
							'checkbox_value' => 1,
							'attributes'     => [
								'name' => 'my_plugin_boilerplate_config[performance][minify_assets]',
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
	 * Process performance section fields
	 *
	 * @since 1.0.0
	 *
	 * @return array Processed performance configuration
	 */
	public static function process(): array {
		$config = [];

		// Cache blocks (checkbox)
		$config['performance.cache_blocks'] = isset( $_POST['performance_cache_blocks'] ) && '1' === $_POST['performance_cache_blocks'];

		// Cache duration (number)
		if ( isset( $_POST['performance_cache_duration'] ) ) {
			$duration = absint( $_POST['performance_cache_duration'] );
			if ( $duration >= 60 && $duration <= 86400 ) { // 1 minute to 24 hours
				$config['performance.cache_duration'] = $duration;
			}
		}

		// Minify assets (checkbox)
		$config['performance.minify_assets'] = isset( $_POST['performance_minify_assets'] ) && '1' === $_POST['performance_minify_assets'];

		// Lazy load (checkbox)
		$config['performance.lazy_load'] = isset( $_POST['performance_lazy_load'] ) && '1' === $_POST['performance_lazy_load'];

		return $config;
	}
}