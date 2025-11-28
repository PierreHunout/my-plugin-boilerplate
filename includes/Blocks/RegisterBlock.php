<?php

/**
 * This file is responsible for handling the block registration functionality in the WordPress plugin.
 * It automatically discovers and registers all WordPress blocks found in the src/ directory.
 *
 * @package MyPluginBoilerplate
 *
 * @since 1.0.0
 */

namespace MyPluginBoilerplate\Blocks;

use MyPluginBoilerplate\MyPluginBoilerplate;
use MyPluginBoilerplate\Utils;
use MyPluginBoilerplate\Utils\Debug;
use InvalidArgumentException;
use RuntimeException;
use Throwable;

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
 * Class RegisterBlock
 *
 * Handles RegisterBlock functionality for the plugin.
 *
 * @since 1.0.0
 */
class RegisterBlock
{

	/**
	 * Plugin slug for block registration.
	 *
	 * @var string
	 * @since 1.0.0
	 */
	private static string $slug = '';

	/**
	 * Class Runner for the block registration functionality.
	 *
	 * Hooks into WordPress 'init' action to register all blocks
	 * found in the src/ directory automatically.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function init(): void
	{
		// Ensure slug is assigned from main plugin class at runtime
		self::$slug = MyPluginBoilerplate::$slug;

		add_action('init', [__CLASS__, 'register_blocks']);
		add_action('enqueue_block_editor_assets', [__CLASS__, 'enqueue_block_assets']);
		add_action('enqueue_block_editor_assets', [__CLASS__, 'localize_settings']);
		add_action('enqueue_block_assets', [__CLASS__, 'enqueue_tailwind_styles']);
		add_action('wp_enqueue_scripts', [__CLASS__, 'enqueue_frontend_assets']);
	}

	/**
	 * Localize plugin settings for use in block editor.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function localize_settings(): void
	{
		$settings = get_option('my_plugin_boilerplate_settings', []);

		wp_localize_script(
			'wp-blocks',
			'myPluginBoilerplateSettings',
			[
				'customClass' => isset($settings['features']['custom_class']) ? $settings['features']['custom_class'] : '',
				'features' => isset($settings['features']) ? $settings['features'] : [],
				'debug' => isset($settings['debug']) ? $settings['debug'] : [],
				'performance' => isset($settings['performance']) ? $settings['performance'] : [],
				'security' => isset($settings['security']) ? $settings['security'] : [],
			]
		);
	}

	/**
	 * Registers custom blocks for the plugin.
	 *
	 * Automatically discovers and registers all blocks found in the build/ directory
	 * by scanning for block.json files in subdirectories.
	 *
	 * @since 1.0.0
	 *
	 * @throws RuntimeException If the filesystem cannot be initialized or build directory is missing.
	 * @throws InvalidArgumentException If a block directory is invalid or unreadable.
	 *
	 * @return void
	 */
	public static function register_blocks(): void
	{
		try {
			$build_dir = MY_PLUGIN_BOILERPLATE_PATH . 'build';

			// Initialize WordPress filesystem
			$filesystem = Utils::get_filesystem();

			if (! $filesystem) {
				throw new RuntimeException('Failed to initialize WordPress filesystem');
			}

			// Check if build directory exists
			if (! $filesystem->is_dir($build_dir)) {
				throw new RuntimeException(sprintf('Build directory not found: %s', $build_dir));
			}

			// Scan recursively for all block.json files
			$blocks_registered = 0;
			$iterator = new \RecursiveIteratorIterator(
				new \RecursiveDirectoryIterator($build_dir, \RecursiveDirectoryIterator::SKIP_DOTS),
				\RecursiveIteratorIterator::SELF_FIRST
			);

			foreach ($iterator as $file) {
				if ($file->isFile() && $file->getFilename() === 'block.json') {
					$block_dir = $file->getPath();

					try {
						// Register the block using the directory containing block.json
						$result = register_block_type($block_dir);

						if (! $result instanceof \WP_Block_Type) {
							Debug::log('RegisterBlock', sprintf('✗ Failed to register block from: %s', basename($block_dir)));
						}

						$blocks_registered++;
					} catch (Throwable $e) {
						Debug::log('RegisterBlock', sprintf('✗ Error registering %s: %s', basename($block_dir), $e->getMessage()));
					}
				}
			}

			if ($blocks_registered === 0) {
				Debug::log('RegisterBlock', sprintf('[%s] No blocks were registered. Check if build directory contains block.json files.', self::$slug));
			}
		} catch (Throwable $error) {
			// Log general errors
			Debug::log('RegisterBlock', sprintf('Error in register_blocks(): %s in %s on line %d', $error->getMessage(), basename($error->getFile()), $error->getLine()));
		}
	}

	/**
	 * Enqueue block editor assets.
	 * 
	 * @since 1.0.0
	 * @return void
	 */
	public static function enqueue_block_assets()
	{
		$build_path = MY_PLUGIN_BOILERPLATE_PATH . 'build';
		$build_url = MY_PLUGIN_BOILERPLATE_URL . 'build';
		$asset_file = include $build_path . '/editor-script.asset.php';

		wp_enqueue_script(
			'editor-script-js',
			$build_url . '/editor-script.js',
			$asset_file['dependencies'],
			$asset_file['version'],
			false
		);
	}

	/**
	 * Enqueue Tailwind CSS styles for blocks (editor and frontend)
	 * 
	 * @since 1.0.0
	 * @return void
	 */
	public static function enqueue_tailwind_styles()
	{
		$build_path = MY_PLUGIN_BOILERPLATE_PATH . 'build';
		$build_url = MY_PLUGIN_BOILERPLATE_URL . 'build';

		wp_enqueue_style(
			'my-plugin-boilerplate-tailwind',
			$build_url . '/tailwind-styles.css',
			[],
			filemtime($build_path . '/tailwind-styles.css')
		);
	}

	/**
	 * Enqueues the block assets for the frontend
	 * 
	 * @since 1.0.0
	 * @return void
	 */
	public static function enqueue_frontend_assets()
	{
		$build_path = MY_PLUGIN_BOILERPLATE_PATH . 'build';
		$build_url = MY_PLUGIN_BOILERPLATE_URL . 'build';
		$asset_file = include $build_path . '/frontend-script.asset.php';

		wp_enqueue_script(
			'frontend-script-js',
			$build_url . '/frontend-script.js',
			$asset_file['dependencies'],
			$asset_file['version'],
			true
		);
	}
}
