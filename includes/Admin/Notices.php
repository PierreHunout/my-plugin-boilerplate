<?php
/**
 * Displays admin notices for the plugin.
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
 * Class Notices
 *
 * Handles Notices functionality for the plugin.
 *
 * @since 1.0.0
 */
class Notices {


	/**
	 * Class Runner for the WebP conversion notices.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function run(): void {
		// Display admin notices
		add_action( 'admin_notices', [ self::class, 'display_notices' ] );
	}

	/**
	 * Displays admin notices for deletion results.
	 * Shows details for each processed file.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function display_notices(): void {
		// Only show notices on our plugin's admin page
		if ( ! isset( $_GET['page'] ) || sanitize_text_field( wp_unslash( $_GET['page'] ) ) !== 'my-plugin-boilerplate' ) {
			return;
		}

		// Verify user capabilities
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Verify nonce for notice parameters to prevent tampering
		if (
			( isset( $_GET['no_files'] ) || isset( $_GET['deleted'] ) ) &&
			( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'my_plugin_boilerplate_notice' ) )
		) {
			return;
		}

		$title = esc_html__( 'No files found to process.', 'my-plugin-boilerplate' );

		// No files found notice
		if ( isset( $_GET['no_files'] ) && sanitize_text_field( wp_unslash( $_GET['no_files'] ) ) === '1' ) {
			echo '<div class="notice is-dismissible my-plugin-boilerplate__notice my-plugin-boilerplate__notice--nofiles">
                <p>' . esc_html( $title ) . '</p>
            </div>';
		}

		// Deletion notice
		if ( isset( $_GET['deleted'] ) && sanitize_text_field( wp_unslash( $_GET['deleted'] ) ) === '1' ) {
			$title = esc_html__( 'Deleted WebP files', 'my-plugin-boilerplate' );
			$data  = get_transient( 'my_plugin_boilerplate_deletion_data' );
			delete_transient( 'my_plugin_boilerplate_deletion_data' );

			// Display notice if there is data
			if ( isset( $data ) && is_array( $data ) ) {
				$count = count( $data );
				echo '<div class="notice is-dismissible my-plugin-boilerplate__notice my-plugin-boilerplate__notice--deletion">
                    <p class="my-plugin-boilerplate__subtitle">' . esc_html( $title ) . ': <strong>' . esc_html( $count ) . '</strong></p>
                    <div class="my-plugin-boilerplate__container my-plugin-boilerplate__container--notice">
                        <div class="my-plugin-boilerplate__inner my-plugin-boilerplate__inner--notice">
                ';

				foreach ( $data as $images ) {
					echo '<ul class="my-plugin-boilerplate__messages">';

					foreach ( $images as $image ) {
						$message = $image['message'];
						$classes = $image['classes'];

						$class_list = [];
						foreach ( $classes as $class ) {
							$class        = 'my-plugin-boilerplate__message--' . sanitize_html_class( $class );
							$class_list[] = $class;
						}

						$classes = implode( ' ', $class_list );

						$allowed_html = [ 'span' => [] ];
						echo '<li class="my-plugin-boilerplate__message ' . esc_attr( $classes ) . '">' . wp_kses( $message, $allowed_html ) . '</li>';
					}

					echo '</ul>';
				}

				echo '</div></div></div>';
			}
		}
	}
}
