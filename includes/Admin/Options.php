<?php
/**
 * Manages the options page and admin actions for your plugin.
 *
 * @package MyPluginBoilerplate\Admin
 *
 * @since 1.0.0
 */

namespace MyPluginBoilerplate\Admin;

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
 * Class Options
 *
 * Handles Options functionality for the plugin.
 *
 * @since 1.0.0
 */
class Options {


	/**
	 * Class Runner for the WebP conversion options.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function run(): void {
		add_action( 'admin_menu', [ self::class, 'add_options' ] );
		add_action( 'admin_init', [ self::class, 'save_options' ] );
	}

	/**
	 * Adds the plugin options page to the WordPress admin menu.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function add_options(): void {
		// Verify user capabilities first
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		add_menu_page(
			__( 'My plugin', 'my-plugin-boilerplate' ),
			__( 'My plugin', 'my-plugin-boilerplate' ),
			'manage_options',
			MY_PLUGIN_BOILERPLATE_SLUG,
			[ self::class, 'render_page' ],
			'dashicons-admin-generic',
			99
		);
	}

	/**
	 * Renders the plugin options page in the WordPress admin.
	 * Displays forms for options, conversion, deletion, and comparison UI.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function render_page(): void {
		$option_text     = get_option( 'my_plugin_boilerplate_text', '' );
		$option_email    = get_option( 'my_plugin_boilerplate_email', '' );
		$option_url      = get_option( 'my_plugin_boilerplate_url', '' );
		$option_number   = get_option( 'my_plugin_boilerplate_number', 10 );
		$option_checkbox = get_option( 'my_plugin_boilerplate_checkbox', 0 );
		$option_toggle   = get_option( 'my_plugin_boilerplate_toggle', 0 );
		$option_textarea = get_option( 'my_plugin_boilerplate_textarea', '' );
		$option_select   = get_option( 'my_plugin_boilerplate_select', 'option1' );
		$option_radio    = get_option( 'my_plugin_boilerplate_radio', 'option1' );
		?>
		<div class="wrap my-plugin-boilerplate">
			<h1 class="my_plugin_option__title"><?php esc_html_e( 'Manage Options', 'my-plugin-boilerplate' ); ?></h1>
			<div class="my_plugin_option__table">
				<!-- Options form -->
				<form method="post" action="" class="my_plugin_option__form"> 
						<?php wp_nonce_field( 'my_plugin_save_options' ); ?>
							<div class="my_plugin_option__row">
								<h2 class="my_plugin_option__subtitle"><?php esc_html_e( 'My options input', 'my-plugin-boilerplate' ); ?></h2>
								<div class="my_plugin_option__fields">
									<div class="my-plugin-boilerplate__field">
										<label for="my_plugin_boilerplate_text" class="my-plugin-boilerplate__label"><?php esc_html_e( 'Text input', 'my-plugin-boilerplate' ); ?></label>
										<input type="text" id="my_plugin_boilerplate_text" class="my-plugin-boilerplate__input my-plugin-boilerplate__input--text" name="my_plugin_boilerplate_text" value="<?php echo esc_attr( $option_text ); ?>" />
									</div>
									<div class="my-plugin-boilerplate__field">
										<label for="my_plugin_boilerplate_email" class="my-plugin-boilerplate__label"><?php esc_html_e( 'Email input', 'my-plugin-boilerplate' ); ?></label>
										<input type="email" id="my_plugin_boilerplate_email" class="my-plugin-boilerplate__input my-plugin-boilerplate__input--email" name="my_plugin_boilerplate_email" value="<?php echo esc_attr( $option_email ); ?>" />
									</div>
									<div class="my-plugin-boilerplate__field">
										<label for="my_plugin_boilerplate_url" class="my-plugin-boilerplate__label"><?php esc_html_e( 'URL input', 'my-plugin-boilerplate' ); ?></label>
										<input type="url" id="my_plugin_boilerplate_url" class="my-plugin-boilerplate__input my-plugin-boilerplate__input--url" name="my_plugin_boilerplate_url" value="<?php echo esc_attr( $option_url ); ?>" />
									</div>
									<div class="my-plugin-boilerplate__field">
										<label for="my_plugin_boilerplate_number" class="my-plugin-boilerplate__label"><?php esc_html_e( 'Number input', 'my-plugin-boilerplate' ); ?></label>
										<input type="number" id="my_plugin_boilerplate_number" class="my-plugin-boilerplate__input my-plugin-boilerplate__input--number" name="my_plugin_boilerplate_number" min="0" max="100" value="<?php echo esc_attr( $option_number ); ?>" />
									</div>
									<div class="my-plugin-boilerplate__field">
										<label class="my-plugin-boilerplate__label"><?php esc_html_e( 'Checkbox input', 'my-plugin-boilerplate' ); ?></label>
										<input type="checkbox" class="my-plugin-boilerplate__input my-plugin-boilerplate__input--checkbox" name="my_plugin_boilerplate_checkbox" value="1" <?php checked( $option_checkbox, 1 ); ?> />
									</div>
									<div class="my-plugin-boilerplate__field">
										<label class="my-plugin-boilerplate__label"><?php esc_html_e( 'Toggle input', 'my-plugin-boilerplate' ); ?></label>
										<input type="checkbox" class="my-plugin-boilerplate__input my-plugin-boilerplate__input--toggle" name="my_plugin_boilerplate_toggle" value="1" <?php checked( $option_toggle, 1 ); ?> />
									</div>
									<div class="my-plugin-boilerplate__field">
										<label for="my_plugin_boilerplate_textarea" class="my-plugin-boilerplate__label"><?php esc_html_e( 'Textarea input', 'my-plugin-boilerplate' ); ?></label>
										<textarea id="my_plugin_boilerplate_textarea" class="my-plugin-boilerplate__input my-plugin-boilerplate__input--textarea" name="my_plugin_boilerplate_textarea" rows="5"><?php echo esc_textarea( $option_textarea ); ?></textarea>
									</div>
									<div class="my-plugin-boilerplate__field">
										<label for="my_plugin_boilerplate_select" class="my-plugin-boilerplate__label"><?php esc_html_e( 'Select input', 'my-plugin-boilerplate' ); ?></label>
										<select id="my_plugin_boilerplate_select" class="my-plugin-boilerplate__input my-plugin-boilerplate__input--select" name="my_plugin_boilerplate_select">
											<option value="option1" <?php selected( $option_select, 'option1' ); ?>><?php esc_html_e( 'Option 1', 'my-plugin-boilerplate' ); ?></option>
											<option value="option2" <?php selected( $option_select, 'option2' ); ?>><?php esc_html_e( 'Option 2', 'my-plugin-boilerplate' ); ?></option>
											<option value="option3" <?php selected( $option_select, 'option3' ); ?>><?php esc_html_e( 'Option 3', 'my-plugin-boilerplate' ); ?></option>
										</select>
									</div>
									<div class="my-plugin-boilerplate__field">
										<span class="my-plugin-boilerplate__label"><?php esc_html_e( 'Radio input', 'my-plugin-boilerplate' ); ?></span>
										<label class="my-plugin-boilerplate__radio-label">
											<input type="radio" class="my-plugin-boilerplate__input my-plugin-boilerplate__input--radio" name="my_plugin_boilerplate_radio" value="option1" <?php checked( $option_radio, 'option1' ); ?> />
											<?php esc_html_e( 'Option 1', 'my-plugin-boilerplate' ); ?>
										</label>
										<label class="my-plugin-boilerplate__radio-label">
											<input type="radio" class="my-plugin-boilerplate__input my-plugin-boilerplate__input--radio" name="my_plugin_boilerplate_radio" value="option2" <?php checked( $option_radio, 'option2' ); ?> />
											<?php esc_html_e( 'Option 2', 'my-plugin-boilerplate' ); ?>
										</label>
										<label class="my-plugin-boilerplate__radio-label">
											<input type="radio" class="my-plugin-boilerplate__input my-plugin-boilerplate__input--radio" name="my_plugin_boilerplate_radio" value="option3" <?php checked( $option_radio, 'option3' ); ?> />
											<?php esc_html_e( 'Option 3', 'my-plugin-boilerplate' ); ?>
										</label>
									</div>
								</div>
								<p class="my-plugin-boilerplate__description">
									<?php esc_html_e( 'Higher means better quality but larger files.', 'my-plugin-boilerplate' ); ?>
								</p>
							</div>
						</div>
						<div class="my-plugin-boilerplate__submit">
							<input type="hidden" name="action" value="save_options">
							<button type="submit" class="button button-primary my-plugin-boilerplate__button my-plugin-boilerplate__button--primary"><?php esc_html_e( 'Save options', 'my-plugin-boilerplate' ); ?></button>
						</div>
					</form>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Handles saving the plugin options from the admin form.
	 * Validates and saves quality and deletion options.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function save_options(): void {
		// Verify user capabilities first
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if (
			isset( $_POST['action'] ) && sanitize_text_field( wp_unslash( $_POST['action'] ) ) === 'save_options'
			&& check_admin_referer( 'my_plugin_save_options' )
		) {
			$text     = isset( $_POST['my_plugin_boilerplate_text'] ) ? sanitize_text_field( wp_unslash( $_POST['my_plugin_boilerplate_text'] ) ) : '';
			$email    = isset( $_POST['my_plugin_boilerplate_email'] ) ? sanitize_email( sanitize_text_field( wp_unslash( $_POST['my_plugin_boilerplate_email'] ) ) ) : '';
			$url      = isset( $_POST['my_plugin_boilerplate_url'] ) ? esc_url_raw( sanitize_text_field( wp_unslash( $_POST['my_plugin_boilerplate_url'] ) ) ) : '';
			$number   = isset( $_POST['my_plugin_boilerplate_number'] ) ? intval( sanitize_text_field( wp_unslash( $_POST['my_plugin_boilerplate_number'] ) ) ) : 10;
			$checkbox = isset( $_POST['my_plugin_boilerplate_checkbox'] ) && sanitize_text_field( wp_unslash( $_POST['my_plugin_boilerplate_checkbox'] ) ) === '1' ? 1 : 0;
			$toggle   = isset( $_POST['my_plugin_boilerplate_toggle'] ) && sanitize_text_field( wp_unslash( $_POST['my_plugin_boilerplate_toggle'] ) ) === '1' ? 1 : 0;
			$textarea = isset( $_POST['my_plugin_boilerplate_textarea'] ) ? sanitize_textarea_field( wp_unslash( $_POST['my_plugin_boilerplate_textarea'] ) ) : '';
			$select   = isset( $_POST['my_plugin_boilerplate_select'] ) ? sanitize_text_field( wp_unslash( $_POST['my_plugin_boilerplate_select'] ) ) : 'option1';
			$radio    = isset( $_POST['my_plugin_boilerplate_radio'] ) ? sanitize_text_field( wp_unslash( $_POST['my_plugin_boilerplate_radio'] ) ) : 'option1';

			update_option( 'my_plugin_boilerplate_text', $text );
			update_option( 'my_plugin_boilerplate_email', $email );
			update_option( 'my_plugin_boilerplate_url', $url );
			update_option( 'my_plugin_boilerplate_number', $number );
			update_option( 'my_plugin_boilerplate_checkbox', $checkbox );
			update_option( 'my_plugin_boilerplate_toggle', $toggle );
			update_option( 'my_plugin_boilerplate_textarea', $textarea );
			update_option( 'my_plugin_boilerplate_select', $select );
			update_option( 'my_plugin_boilerplate_radio', $radio );

			// Add admin notice for successful save
			add_action(
				'admin_notices',
				function () {
					echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Options saved successfully.', 'my-plugin-boilerplate' ) . '</p></div>';
				}
			);
		}
	}
}
