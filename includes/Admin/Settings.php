<?php

/**
 * Configuration Settings Page
 *
 * Provides a WordPress admin interface for managing plugin configuration.
 *
 * @package MyPluginBoilerplate
 *
 * @since 1.0.0
 */

namespace MyPluginBoilerplate\Admin;

use MyPluginBoilerplate\Manager\FieldManager;
use MyPluginBoilerplate\Admin\Sections\Debug;
use MyPluginBoilerplate\Admin\Sections\Performance;
use MyPluginBoilerplate\Admin\Sections\Security;
use MyPluginBoilerplate\Admin\Sections\Features;

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
 * Class Settings
 *
 * Handles configuration settings admin interface with proper WordPress Settings API integration.
 *
 * @since 1.0.0
 */
class Settings
{

	/**
	 * Option group name for WordPress Settings API
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private const OPTION_GROUP = 'my_plugin_boilerplate_settings_group';

	/**
	 * Option name for storing configuration in database
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private const OPTION_NAME = 'my_plugin_boilerplate_settings';

	/**
	 * Initialize the class
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function init(): void
	{
		self::setup();
	}

	/**
	 * Initialize settings page
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function setup(): void
	{
		// Initialize field manager
		FieldManager::init();

		// Initialize admin settings if in admin
		if (is_admin()) {
			add_action('admin_menu', [__CLASS__, 'add_page']);
			add_action('admin_init', [__CLASS__, 'register_settings']);
		}
	}

	/**
	 * Register WordPress Settings API
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function register_settings(): void
	{
		// Register the main settings option
		register_setting(
			self::OPTION_GROUP,
			self::OPTION_NAME,
			[
				'type'              => 'array',
				'description'       => __('My Plugin Boilerplate Configuration', 'my-plugin-boilerplate'),
				'sanitize_callback' => [__CLASS__, 'sanitize_config'],
				'show_in_rest'      => false,
				'default'           => [],
			]
		);
	}

	/**
	 * Sanitize configuration data
	 *
	 * @since 1.0.0
	 *
	 * @param array|null $input Raw input data.
	 *
	 * @return array Sanitized configuration data
	 */
	public static function sanitize_config($input): array
	{
		// Handle null input - WordPress sends null when no checkboxes are checked
		if (null === $input) {
			return [];
		}

		if (! is_array($input)) {
			return get_option(self::OPTION_NAME, []);
		}

		$sanitized = [];		// Sanitize debug settings
		if (isset($input['debug'])) {
			$sanitized['debug'] = [
				'enabled' => ! empty($input['debug']['enabled']),
				'log_level' => isset($input['debug']['log_level']) ? sanitize_text_field($input['debug']['log_level']) : 'error',
				'log_to_file' => ! empty($input['debug']['log_to_file']),
				'log_to_db' => ! empty($input['debug']['log_to_db']),
				'max_log_files' => isset($input['debug']['max_log_files']) ? absint($input['debug']['max_log_files']) : 10,
			];
		}

		// Sanitize performance settings
		if (isset($input['performance'])) {
			$sanitized['performance'] = [
				'cache_blocks' => ! empty($input['performance']['cache_blocks']),
				'cache_duration' => isset($input['performance']['cache_duration']) ? absint($input['performance']['cache_duration']) : 3600,
				'minify_assets' => ! empty($input['performance']['minify_assets']),
				'lazy_load' => ! empty($input['performance']['lazy_load']),
			];
		}

		// Sanitize security settings
		if (isset($input['security'])) {
			$sanitized['security'] = [
				'rate_limit' => ! empty($input['security']['rate_limit']),
				'max_requests' => isset($input['security']['max_requests']) ? absint($input['security']['max_requests']) : 100,
				'time_window' => isset($input['security']['time_window']) ? absint($input['security']['time_window']) : 3600,
				'strict_validation' => ! empty($input['security']['strict_validation']),
			];
		}

		// Sanitize features settings
		if (isset($input['features'])) {
			$sanitized['features'] = [
				'blocks_enabled' => ! empty($input['features']['blocks_enabled']),
				'rest_api_enabled' => ! empty($input['features']['rest_api_enabled']),
				'cli_enabled' => ! empty($input['features']['cli_enabled']),
				'admin_dashboard' => ! empty($input['features']['admin_dashboard']),
				'custom_class' => isset($input['features']['custom_class']) ? sanitize_html_class($input['features']['custom_class']) : '',
			];
		}

		// Merge with existing settings to preserve other tabs
		$existing = get_option(self::OPTION_NAME, []);
		if (is_array($existing)) {
			$sanitized = array_merge($existing, $sanitized);
		}

		return $sanitized;
	}
	/**
	 * Add admin menu page
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function add_page(): void
	{
		add_menu_page(
			__('My Plugin Boilerplate Configuration', 'my-plugin-boilerplate'),
			__('Plugin Config', 'my-plugin-boilerplate'),
			'manage_options',
			MY_PLUGIN_BOILERPLATE_SLUG,
			[__CLASS__, 'render_page'],
			'dashicons-admin-generic',
			99
		);
	}

	/**
	 * Render settings page
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function render_page(): void
	{
		if (! current_user_can('manage_options')) {
			wp_die(esc_html__('You do not have sufficient permissions to access this page.', 'my-plugin-boilerplate'));
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Tab navigation doesn't modify data
		$active_tab = isset($_GET['tab']) ? sanitize_text_field(wp_unslash($_GET['tab'])) : 'debug';
?>
		<div class="wrap plugin-config">
			<h1 class="plugin-config__title"><?php esc_html_e('My Plugin Boilerplate Configuration', 'my-plugin-boilerplate'); ?></h1>

			<?php self::display_admin_notices(); ?>

			<nav class="plugin-config__navigation nav-tab-wrapper">
				<a href="?page=<?php echo esc_attr(MY_PLUGIN_BOILERPLATE_SLUG); ?>&tab=debug"
					class="plugin-config__tab nav-tab <?php echo 'debug' === $active_tab ? 'plugin-config__tab--active nav-tab-active' : ''; ?>">
					<?php esc_html_e('Debug', 'my-plugin-boilerplate'); ?>
				</a>
				<a href="?page=<?php echo esc_attr(MY_PLUGIN_BOILERPLATE_SLUG); ?>&tab=performance"
					class="plugin-config__tab nav-tab <?php echo 'performance' === $active_tab ? 'plugin-config__tab--active nav-tab-active' : ''; ?>">
					<?php esc_html_e('Performance', 'my-plugin-boilerplate'); ?>
				</a>
				<a href="?page=<?php echo esc_attr(MY_PLUGIN_BOILERPLATE_SLUG); ?>&tab=security"
					class="plugin-config__tab nav-tab <?php echo 'security' === $active_tab ? 'plugin-config__tab--active nav-tab-active' : ''; ?>">
					<?php esc_html_e('Security', 'my-plugin-boilerplate'); ?>
				</a>
				<a href="?page=<?php echo esc_attr(MY_PLUGIN_BOILERPLATE_SLUG); ?>&tab=features"
					class="plugin-config__tab nav-tab <?php echo 'features' === $active_tab ? 'plugin-config__tab--active nav-tab-active' : ''; ?>">
					<?php esc_html_e('Features', 'my-plugin-boilerplate'); ?>
				</a>
			</nav>

			<form class="plugin-config__form" method="post" action="options.php" novalidate="novalidate">
				<?php
				// WordPress settings fields
				settings_fields(self::OPTION_GROUP);

				// Render active tab content
				?>
				<div class="plugin-config__content">
					<?php
					switch ($active_tab) {
						case 'debug':
							Debug::render();
							break;
						case 'performance':
							Performance::render();
							break;
						case 'security':
							Security::render();
							break;
						case 'features':
							Features::render();
							break;
					}
					?>
				</div>

				<?php submit_button(__('Save Configuration', 'my-plugin-boilerplate'), 'primary', 'submit', true, ['class' => 'plugin-config__submit button button-primary']); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Display admin notices based on URL parameters
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private static function display_admin_notices(): void
	{
		// Check if we have a message to display
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Message display doesn't modify data
		if (! isset($_GET['message'])) {
			return;
		}

		$message      = sanitize_text_field(wp_unslash($_GET['message']));
		$notice_class = 'plugin-config__notice notice notice-success is-dismissible';
		$notice_text  = '';

		switch ($message) {
			case 'saved':
				// Verify nonce for saved message
				if (isset($_GET['_wpnonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_wpnonce'])), 'config_saved')) {
					$notice_class = 'plugin-config__notice plugin-config__notice--success notice notice-success is-dismissible';
					$notice_text  = __('Configuration has been saved successfully.', 'my-plugin-boilerplate');
				}
				break;

			case 'reset':
				// Verify nonce for reset message
				if (isset($_GET['_wpnonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_wpnonce'])), 'config_reset')) {
					$notice_class = 'plugin-config__notice plugin-config__notice--success notice notice-success is-dismissible';
					$notice_text  = __('Configuration has been reset to defaults.', 'my-plugin-boilerplate');
				}
				break;

			case 'error':
				// Verify nonce for error message
				if (isset($_GET['_wpnonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_wpnonce'])), 'config_error')) {
					$notice_class = 'plugin-config__notice plugin-config__notice--error notice notice-error is-dismissible';
					$notice_text  = __('An error occurred while saving the configuration. Please try again.', 'my-plugin-boilerplate');
				}
				break;
		}

		if (! empty($notice_text)) {
		?>
			<div class="<?php echo esc_attr($notice_class); ?>">
				<p class="plugin-config__notice-text"><?php echo esc_html($notice_text); ?></p>
			</div>
<?php
		}
	}
}
