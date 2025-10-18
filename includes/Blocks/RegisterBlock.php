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
use MyPluginBoilerplate\Helpers;
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
	 * @return void
	 */
	public function run(): void {
		// Ensure slug is assigned from main plugin class at runtime
		self::$slug = MyPluginBoilerplate::$slug;

		add_action( 'init', array( self::class, 'register_blocks' ) );
	}

	/**
	 * Registers custom blocks for the plugin.
	 *
	 * Automatically discovers and registers all blocks found in the build/ directory
	 * by scanning for block.json files in subdirectories.
	 *
	 * @return void
	 */
	public static function register_blocks(): void {
		try {
			// Initialize WordPress filesystem
			$filesystem = Helpers::get_filesystem();

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

					if ( defined( 'WP_DEBUG' ) && WP_DEBUG === true ) {
						if ( $result instanceof \WP_Block_Type ) {
							error_log( sprintf( '[%1$s] Successfully registered block: %2$s', self::$slug, $dir ) );
						} else {
							error_log( sprintf( '[%1$s] Failed to register block: %2$s', self::$slug, $dir ) );
						}
					}
				} catch ( Throwable $e ) {
					// Log individual block registration errors
					if ( defined( 'WP_DEBUG' ) && WP_DEBUG === true ) {
						error_log(
							sprintf(
								'[%1$s] Error registering block from %2$s: %3$s in %4$s on line %5$d',
								self::$slug,
								$dir,
								$e->getMessage(),
								basename( $e->getFile() ),
								$e->getLine()
							)
						);
					}
					// Continue with next block instead of stopping entirely
					continue;
				}
			}

			if ( defined( 'WP_DEBUG' ) && WP_DEBUG === true ) {
				error_log( sprintf( '[%1$s] Total blocks found and processed: %2$d', self::$slug, count( $blocks ) ) );
			}
		} catch ( Throwable $error ) {
			// Log general errors
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG === true ) {
				error_log(
					sprintf(
						'[%1$s] Error in register_blocks(): %2$s in %3$s on line %4$d',
						self::$slug,
						$error->getMessage(),
						basename( $error->getFile() ),
						$error->getLine()
					)
				);
			}
		}
	}

	/**
	 * Recursively finds all subdirectories in a given directory using WP_Filesystem.
	 *
	 * @since 1.0.0
	 *
	 * @param string              $directory The directory to search in.
	 * @param \WP_Filesystem_Base $filesystem The WordPress filesystem instance.
	 * @return array Array of subdirectory paths.
	 */
	private static function get_blocks( string $directory, $filesystem ): array {
		$dirs = array();

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
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG === true ) {
				error_log(
					sprintf(
						'[%1$s] Error scanning directory %2$s: %3$s in %4$s on line %5$d',
						self::$slug,
						$directory,
						$error->getMessage(),
						basename( $error->getFile() ),
						$error->getLine()
					)
				);
			}
		}

		return $dirs;
	}

	/**
	 * Recursively scans directories for block.json files using WP_Filesystem.
	 *
	 * @param string              $directory The directory to scan.
	 * @param \WP_Filesystem_Base $filesystem The WordPress filesystem instance.
	 * @return array Array of block.json file paths.
	 */
	private static function get_blocks_json( string $directory, $filesystem ): array {
		$blocks = array();

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
						} elseif ( defined( 'WP_DEBUG' ) && WP_DEBUG === true ) {
								error_log( sprintf( '[%s] Block file not readable: %s', self::$slug, $path ) );
						}
					}
				} catch ( Throwable $e ) {
					// Log file-specific errors but continue processing
					if ( defined( 'WP_DEBUG' ) && WP_DEBUG === true ) {
						error_log(
							sprintf(
								'[%s] Error processing %s: %s',
								self::$slug,
								$path,
								$e->getMessage()
							)
						);
					}
					continue;
				}
			}
		} catch ( Throwable $error ) {
			// Log scanning errors
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG === true ) {
				error_log(
					sprintf(
						'[%s] Error scanning directory %s: %s',
						self::$slug,
						$directory,
						$error->getMessage()
					)
				);
			}
		}

		return $blocks;
	}
}
