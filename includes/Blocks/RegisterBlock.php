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
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class RegisterBlock
 *
 * Handles RegisterBlock functionality for the plugin.
 *
 * @since 1.0.0
 */
class RegisterBlock {

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
	public function init(): void {
		// Ensure slug is assigned from main plugin class at runtime
		self::$slug = MyPluginBoilerplate::$slug;

		add_action( 'init', [ __CLASS__, 'register_blocks' ] );
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
	public static function register_blocks(): void {
		try {
			// Initialize WordPress filesystem
			$filesystem = Utils::get_filesystem();

			if ( ! $filesystem ) {
				throw new RuntimeException( 'Failed to initialize WordPress filesystem' );
			}

			// Get the plugin root path using the defined constant
			$path  = MY_PLUGIN_BOILERPLATE_PATH;
			$build = $path . 'build/';

			// Check if build directory exists using WP_Filesystem
			if ( ! $filesystem->is_dir( $build ) ) {
				throw new RuntimeException( sprintf( 'Blocks source directory not found: %s', $build ) );
			}

			// Find all subdirectories in build directory
			$blocks = self::get_blocks( $build, $filesystem );

			if ( empty( $blocks ) ) {
				throw new RuntimeException( sprintf( '[%1$s] No blocks found in: %2$s', self::$slug, $build ) );
			}

			// Register each block found
			foreach ( $blocks as $block ) {
				try {
					$dir = dirname( $block );

					if ( ! $filesystem->is_dir( $dir ) ) {
						throw new InvalidArgumentException( sprintf( 'Block parent path is not a directory: %s', $block ) );
					}

					// Validate block directory using WP_Filesystem
					if ( ! $filesystem->is_readable( $dir ) ) {
						throw new InvalidArgumentException( sprintf( 'Block directory is not readable: %s', $dir ) );
					}

					// Register the block using the directory (WordPress will automatically read block.json)
					$result = register_block_type( $dir );

					if ( $result instanceof \WP_Block_Type ) {
						Debug::log( 'RegisterBlock', sprintf( 'Successfully registered block: %s', $dir ) );
					} else {
						Debug::log( 'RegisterBlock', sprintf( 'Failed to register block: %s', $dir ) );
					}
				} catch ( Throwable $e ) {
					// Log individual block registration errors
					Debug::log( 'RegisterBlock', sprintf( 'Error registering block from %s: %s in %s on line %d', $dir, $e->getMessage(), basename( $e->getFile() ), $e->getLine() ) );

					// Continue with next block instead of stopping entirely
					continue;
				}
			}

			Debug::log( 'RegisterBlock', sprintf( 'Total blocks found and processed: %d', count( $blocks ) ) );
		} catch ( Throwable $error ) {
			// Log general errors
			Debug::log( 'RegisterBlock', sprintf( 'Error in register_blocks(): %s in %s on line %d', $error->getMessage(), basename( $error->getFile() ), $error->getLine() ) );
		}
	}

	/**
	 * Recursively finds all subdirectories in a given directory using WP_Filesystem.
	 *
	 * @since 1.0.0
	 *
	 * @param string              $directory The directory to search in.
	 * @param \WP_Filesystem_Base $filesystem The WordPress filesystem instance.
	 *
	 * @throws InvalidArgumentException If the directory parameter is empty.
	 * @throws RuntimeException If the directory does not exist or is not readable.
	 *
	 * @return array Array of subdirectory paths.
	 */
	private static function get_blocks( string $directory, $filesystem ): array {
		$dirs = [];

		try {
			// Validate directory parameter
			if ( empty( $directory ) ) {
				throw new InvalidArgumentException( 'Directory parameter cannot be empty' );
			}

			// Check if directory exists using WP_Filesystem
			if ( ! $filesystem->is_dir( $directory ) ) {
				throw new RuntimeException( sprintf( 'Directory does not exist: %s', $directory ) );
			}

			// Check if directory is readable using WP_Filesystem
			if ( ! $filesystem->is_readable( $directory ) ) {
				throw new RuntimeException( sprintf( 'Directory is not readable: %s', $directory ) );
			}

			// Recursively scan directories using WP_Filesystem
			$dirs = self::get_blocks_json( $directory, $filesystem );
		} catch ( Throwable $error ) {
			// Log directory scanning errors
			Debug::log( 'RegisterBlock', sprintf( 'Error scanning directory %s: %s in %s on line %d', $directory, $error->getMessage(), basename( $error->getFile() ), $error->getLine() ) );
		}

		return $dirs;
	}

	/**
	 * Recursively scans directories for block.json files using WP_Filesystem.
	 *
	 * @since 1.0.0
	 *
	 * @param string              $directory The directory to scan.
	 * @param \WP_Filesystem_Base $filesystem The WordPress filesystem instance.
	 *
	 * @throws Throwable For any errors encountered during directory scanning.
	 *
	 * @return array Array of block.json file paths.
	 */
	private static function get_blocks_json( string $directory, $filesystem ): array {
		$blocks = [];

		try {
			// Get directory listing using WP_Filesystem
			$files = $filesystem->dirlist( $directory, false, true );

			if ( ! is_array( $files ) ) {
				return $blocks;
			}

			foreach ( $files as $name => $info ) {
				$path = trailingslashit( $directory ) . $name;

				try {
					if ( $info['type'] === 'd' ) {
						// It's a directory, scan recursively
						$subdirectories = self::get_blocks_json( $path, $filesystem );
						$blocks         = array_merge( $blocks, $subdirectories );
					} elseif ( $info['type'] === 'f' && $name === 'block.json' ) {
						// It's a block.json file
						if ( $filesystem->is_readable( $path ) ) {
							$blocks[] = $path;
						} else {
							Debug::log( 'RegisterBlock', sprintf( 'Block file not readable: %s', $path ) );
						}
					}
				} catch ( Throwable $e ) {
					// Log file-specific errors but continue processing
					Debug::log( 'RegisterBlock', sprintf( 'Error processing %s: %s', $path, $e->getMessage() ) );

					continue;
				}
			}
		} catch ( Throwable $error ) {
			// Log scanning errors
			Debug::log( 'RegisterBlock', sprintf( 'Error scanning directory %s: %s', $directory, $error->getMessage() ) );
		}

		return $blocks;
	}
}
