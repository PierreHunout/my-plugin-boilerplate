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

use MyPluginBoilerplate\Config\Config;
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
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class Settings
 *
 * Handles configuration settings admin interface with proper WordPress Settings API integration.
 *
 * @since 1.0.0
 */
class Settings {

	/**
	 * Option group name for WordPress Settings API
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private const OPTION_GROUP = 'my_plugin_boilerplate_config_group';

	/**
	 * Option name for storing configuration in database
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private const OPTION_NAME = 'my_plugin_boilerplate_config';

	/**
	 * Initialize the class
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function init(): void {
		self::setup();
	}

	/**
	 * Initialize settings page
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function setup(): void {
		// Initialize field manager
		FieldManager::init();

		// Initialize admin settings if in admin and feature is enabled
		if ( is_admin() && Config::get( 'features.admin_dashboard' ) ) {
			add_action( 'admin_menu', [ __CLASS__, 'add_page' ] );
			add_action( 'admin_init', [ __CLASS__, 'register_settings' ] );
			add_action( 'admin_init', [ __CLASS__, 'submit' ] );
			add_action( 'admin_init', [ __CLASS__, 'reset' ] );
		}
	}

	/**
	 * Register WordPress Settings API
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function register_settings(): void {
		register_setting(
			self::OPTION_GROUP,
			self::OPTION_NAME,
			[
				'type'              => 'array',
				'description'       => __( 'My Plugin Boilerplate Configuration', 'my-plugin-boilerplate' ),
				'sanitize_callback' => [ __CLASS__, 'sanitize_config' ],
				'show_in_rest'      => false,
				'default'           => [],
			]
		);

		// Register settings sections
		add_settings_section(
			'debug_section',
			__( 'Debug Settings', 'my-plugin-boilerplate' ),
			[ __CLASS__, 'section_callback' ],
			MY_PLUGIN_BOILERPLATE_SLUG
		);

		add_settings_section(
			'performance_section',
			__( 'Performance Settings', 'my-plugin-boilerplate' ),
			[ __CLASS__, 'section_callback' ],
			MY_PLUGIN_BOILERPLATE_SLUG
		);

		add_settings_section(
			'security_section',
			__( 'Security Settings', 'my-plugin-boilerplate' ),
			[ __CLASS__, 'section_callback' ],
			MY_PLUGIN_BOILERPLATE_SLUG
		);

		add_settings_section(
			'features_section',
			__( 'Features Settings', 'my-plugin-boilerplate' ),
			[ __CLASS__, 'section_callback' ],
			MY_PLUGIN_BOILERPLATE_SLUG
		);
	}

	/**
	 * Settings section callback
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Section arguments.
	 *
	 * @return void
	 */
	public static function section_callback( array $args ): void {
		// Section descriptions are handled by individual section classes
		// This callback is required by WordPress Settings API
		unset( $args ); // Prevent unused variable warning
	}

	/**
	 * Handle form submission and save configuration
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function submit(): void {
		// Check if this is a settings form submission
		if ( ! isset( $_POST['submit'] ) || ! isset( $_POST['_wpnonce'] ) ) {
			return;
		}

		// Verify nonce
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'update-options' ) ) {
			wp_die( esc_html__( 'Security check failed.', 'my-plugin-boilerplate' ) );
		}

		// Check user permissions
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to perform this action.', 'my-plugin-boilerplate' ) );
		}

		// Check if this is our settings page
		if ( ! isset( $_GET['page'] ) || MY_PLUGIN_BOILERPLATE_SLUG !== sanitize_text_field( wp_unslash( $_GET['page'] ) ) ) {
			return;
		}

		// Get current tab
		$active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'debug';

		// Process form data based on active tab
		$config = [];

		switch ( $active_tab ) {
			case 'debug':
				$config = Debug::process();
				break;
			case 'performance':
				$config = Performance::process();
				break;
			case 'security':
				$config = Security::process();
				break;
			case 'features':
				$config = Features::process();
				break;
		}

		// Apply configuration updates with proper overwrite rules
		foreach ( $config as $key => $value ) {
			Config::set( $key, $value );
		}

		// Save configuration to database
		if ( Config::save() ) {
			// Redirect with success message
			$redirect_url = add_query_arg(
				[
					'page'     => MY_PLUGIN_BOILERPLATE_SLUG,
					'tab'      => $active_tab,
					'message'  => 'saved',
					'_wpnonce' => wp_create_nonce( 'config_saved' ),
				],
				admin_url( 'admin.php' )
			);
			wp_safe_redirect( $redirect_url );
			exit;
		} else {
			// Redirect with error message
			$redirect_url = add_query_arg(
				[
					'page'     => MY_PLUGIN_BOILERPLATE_SLUG,
					'tab'      => $active_tab,
					'message'  => 'error',
					'_wpnonce' => wp_create_nonce( 'config_error' ),
				],
				admin_url( 'admin.php' )
			);
			wp_safe_redirect( $redirect_url );
			exit;
		}
	}

	/**
	 * Handle configuration reset action
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function reset(): void {
		// Check if reset action is requested
		if ( ! isset( $_GET['action'] ) || 'reset' !== $_GET['action'] ) {
			return;
		}

		// Verify nonce
		if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'reset_config' ) ) {
			wp_die( esc_html__( 'Security check failed.', 'my-plugin-boilerplate' ) );
		}

		// Check user permissions
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to perform this action.', 'my-plugin-boilerplate' ) );
		}

		// Reset configuration
		Config::reset();

		// Redirect with success message
		$redirect_url = add_query_arg(
			[
				'page'     => MY_PLUGIN_BOILERPLATE_SLUG,
				'message'  => 'reset',
				'_wpnonce' => wp_create_nonce( 'config_reset' ),
			],
			admin_url( 'admin.php' )
		);
		wp_safe_redirect( $redirect_url );
		exit;
	}

	/**
	 * Sanitize configuration data
	 *
	 * @since 1.0.0
	 *
	 * @param array $input Raw input data.
	 *
	 * @return array Sanitized configuration data
	 */
	public static function sanitize_config( array $input ): array {
		// Configuration is handled by individual field processors
		// This callback is required by WordPress Settings API but actual
		// sanitization happens in the specific field processing methods
		return $input;
	}

	/**
	 * Add admin menu page
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function add_page(): void {
		add_menu_page(
			__( 'My Plugin Boilerplate Configuration', 'my-plugin-boilerplate' ),
			__( 'Plugin Config', 'my-plugin-boilerplate' ),
			'manage_options',
			MY_PLUGIN_BOILERPLATE_SLUG,
			[ __CLASS__, 'render_page' ],
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
	public static function render_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'my-plugin-boilerplate' ) );
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Tab navigation doesn't modify data
		$active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'debug';
		?>
		<div class="wrap plugin-config">
			<h1 class="plugin-config__title"><?php esc_html_e( 'My Plugin Boilerplate Configuration', 'my-plugin-boilerplate' ); ?></h1>
			
			<?php self::display_admin_notices(); ?>
			
			<nav class="plugin-config__navigation nav-tab-wrapper">
				<a href="?page=<?php echo esc_attr( MY_PLUGIN_BOILERPLATE_SLUG ); ?>&tab=debug" 
					class="plugin-config__tab nav-tab <?php echo 'debug' === $active_tab ? 'plugin-config__tab--active nav-tab-active' : ''; ?>">
					<?php esc_html_e( 'Debug', 'my-plugin-boilerplate' ); ?>
				</a>
				<a href="?page=<?php echo esc_attr( MY_PLUGIN_BOILERPLATE_SLUG ); ?>&tab=performance" 
					class="plugin-config__tab nav-tab <?php echo 'performance' === $active_tab ? 'plugin-config__tab--active nav-tab-active' : ''; ?>">
					<?php esc_html_e( 'Performance', 'my-plugin-boilerplate' ); ?>
				</a>
				<a href="?page=<?php echo esc_attr( MY_PLUGIN_BOILERPLATE_SLUG ); ?>&tab=security" 
					class="plugin-config__tab nav-tab <?php echo 'security' === $active_tab ? 'plugin-config__tab--active nav-tab-active' : ''; ?>">
					<?php esc_html_e( 'Security', 'my-plugin-boilerplate' ); ?>
				</a>
				<a href="?page=<?php echo esc_attr( MY_PLUGIN_BOILERPLATE_SLUG ); ?>&tab=features" 
					class="plugin-config__tab nav-tab <?php echo 'features' === $active_tab ? 'plugin-config__tab--active nav-tab-active' : ''; ?>">
					<?php esc_html_e( 'Features', 'my-plugin-boilerplate' ); ?>
				</a>
			</nav>

			<form class="plugin-config__form" method="post" action="" novalidate="novalidate">
				<?php
				// WordPress nonce for security
				wp_nonce_field( 'update-options' );

				// Render active tab content
				?>
				<div class="plugin-config__content">
					<?php
					switch ( $active_tab ) {
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

				<?php submit_button( __( 'Save Configuration', 'my-plugin-boilerplate' ), 'primary', 'submit', true, [ 'class' => 'plugin-config__submit button button-primary' ] ); ?>
			</form>
			
			<div class="plugin-config__actions config-actions">
				<h3 class="plugin-config__actions-title"><?php esc_html_e( 'Actions', 'my-plugin-boilerplate' ); ?></h3>
				<p class="plugin-config__actions-description description">
					<?php esc_html_e( 'Reset all configuration settings to their default values. This action cannot be undone.', 'my-plugin-boilerplate' ); ?>
				</p>
				<p class="plugin-config__actions-controls">
					<a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=' . MY_PLUGIN_BOILERPLATE_SLUG . '&action=reset' ), 'reset_config' ) ); ?>" 
						class="plugin-config__reset-button button button-secondary"
						onclick="return confirm('<?php esc_attr_e( 'Are you sure you want to reset all configuration to defaults? This action cannot be undone.', 'my-plugin-boilerplate' ); ?>')">
						<?php esc_html_e( 'Reset to Defaults', 'my-plugin-boilerplate' ); ?>
					</a>
				</p>
			</div>

			<div class="plugin-config__info config-info">
				<h3 class="plugin-config__info-title"><?php esc_html_e( 'Configuration Information', 'my-plugin-boilerplate' ); ?></h3>
				<p class="plugin-config__info-description description">
					<?php esc_html_e( 'Current configuration is automatically saved when you submit any form. Changes take effect immediately.', 'my-plugin-boilerplate' ); ?>
				</p>
				<?php if ( Config::get( 'debug.enabled' ) ) : ?>
					<p class="plugin-config__info-debug description">
						<strong class="plugin-config__debug-label"><?php esc_html_e( 'Debug Mode Active:', 'my-plugin-boilerplate' ); ?></strong>
						<span class="plugin-config__debug-text"><?php esc_html_e( 'Additional logging and debug information is being recorded.', 'my-plugin-boilerplate' ); ?></span>
					</p>
				<?php endif; ?>
			</div>
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
	private static function display_admin_notices(): void {
		// Check if we have a message to display
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Message display doesn't modify data
		if ( ! isset( $_GET['message'] ) ) {
			return;
		}

		$message      = sanitize_text_field( wp_unslash( $_GET['message'] ) );
		$notice_class = 'plugin-config__notice notice notice-success is-dismissible';
		$notice_text  = '';

		switch ( $message ) {
			case 'saved':
				// Verify nonce for saved message
				if ( isset( $_GET['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'config_saved' ) ) {
					$notice_class = 'plugin-config__notice plugin-config__notice--success notice notice-success is-dismissible';
					$notice_text  = __( 'Configuration has been saved successfully.', 'my-plugin-boilerplate' );
				}
				break;

			case 'reset':
				// Verify nonce for reset message
				if ( isset( $_GET['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'config_reset' ) ) {
					$notice_class = 'plugin-config__notice plugin-config__notice--success notice notice-success is-dismissible';
					$notice_text  = __( 'Configuration has been reset to defaults.', 'my-plugin-boilerplate' );
				}
				break;

			case 'error':
				// Verify nonce for error message
				if ( isset( $_GET['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'config_error' ) ) {
					$notice_class = 'plugin-config__notice plugin-config__notice--error notice notice-error is-dismissible';
					$notice_text  = __( 'An error occurred while saving the configuration. Please try again.', 'my-plugin-boilerplate' );
				}
				break;
		}

		if ( ! empty( $notice_text ) ) {
			?>
			<div class="<?php echo esc_attr( $notice_class ); ?>">
				<p class="plugin-config__notice-text"><?php echo esc_html( $notice_text ); ?></p>
			</div>
			<?php
		}
	}
}