<?php
/**
 * PHPUnit Bootstrap for My Plugin Boilerplate
 *
 * @package MyPluginBoilerplate
 */

// Prevent direct access.
if ( ! defined( 'PHPUNIT_COMPOSER_INSTALL' ) && ! defined( 'PHPUNIT_TESTSUITE' ) ) {
	die( 'This file should only be accessed during PHPUnit testing.' );
}

// Define plugin constants for testing.
if ( ! defined( 'MY_PLUGIN_BOILERPLATE_TESTING' ) ) {
	define( 'MY_PLUGIN_BOILERPLATE_TESTING', true );
}

// Load Composer autoloader.
if ( file_exists( dirname( __DIR__ ) . '/vendor/autoload.php' ) ) {
	require_once dirname( __DIR__ ) . '/vendor/autoload.php';
} else {
	die( 'Composer autoloader not found. Please run "composer install".' );
}

// Set up test environment constants first.
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', '/tmp/wordpress/' );
}

if ( ! defined( 'WP_CONTENT_DIR' ) ) {
	define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
}

if ( ! defined( 'WP_PLUGIN_DIR' ) ) {
	define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );
}

// Initialize test hooks system for unit tests.
global $test_filters, $test_actions;
$test_filters = [];
$test_actions = [];

// Add mock function for tests_add_filter.
if ( ! function_exists( 'tests_add_filter' ) ) {
	function tests_add_filter( $tag, $function_to_add, $priority = 10, $accepted_args = 1 ) {
		global $test_filters;
		
		if ( ! isset( $test_filters[ $tag ] ) ) {
			$test_filters[ $tag ] = [];
		}
		
		$test_filters[ $tag ][] = [
			'function' => $function_to_add,
			'priority' => $priority,
			'accepted_args' => $accepted_args
        ];
		
		return true;
	}
}

// Load WordPress test environment (if available).
$_tests_dir = getenv( 'WP_TESTS_DIR' );

// If WP_TESTS_DIR is not set, try to find it.
if ( ! $_tests_dir ) {
	$_tests_dir = rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress-tests-lib';
}

// Check if WordPress test suite is available.
if ( file_exists( $_tests_dir . '/includes/functions.php' ) ) {
	// Load WordPress test functions
	require_once $_tests_dir . '/includes/functions.php';

	/**
	 * Manually load the plugin being tested.
	 */
	function _manually_load_plugin() {
		require dirname( __DIR__ ) . '/my-plugin-boilerplate.php';
	}
	tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

	// Start up the WP testing environment
	require $_tests_dir . '/includes/bootstrap.php';
} else {
	// For unit tests that don't require WordPress
	echo "WordPress test suite not found. Running tests without WordPress environment.\n";
	
	// Load the main plugin file for unit testing
	if ( file_exists( dirname( __DIR__ ) . '/my-plugin-boilerplate.php' ) ) {
		// Mock WordPress functions if not available
		if ( ! function_exists( 'plugin_dir_path' ) ) {
			function plugin_dir_path( $file ) {
				return dirname( $file ) . '/';
			}
		}
		
		if ( ! function_exists( 'plugin_dir_url' ) ) {
			function plugin_dir_url( $file ) {
				return 'http://example.com/wp-content/plugins/' . basename( dirname( $file ) ) . '/';
			}
		}
		
		if ( ! function_exists( '__' ) ) {
			function __( $text, $domain = 'default' ) {
				return $text;
			}
		}
		
		if ( ! function_exists( 'esc_html__' ) ) {
			function esc_html__( $text, $domain = 'default' ) {
				return htmlspecialchars( $text );
			}
		}
		
		if ( ! function_exists( 'add_action' ) ) {
			function add_action( $hook, $callback, $priority = 10, $accepted_args = 1 ) {
				// Mock implementation
			}
		}
		
		if ( ! function_exists( 'add_filter' ) ) {
			function add_filter( $hook, $callback, $priority = 10, $accepted_args = 1 ) {
				global $test_filters;
				
				if ( ! isset( $test_filters[ $hook ] ) ) {
					$test_filters[ $hook ] = [];
				}
				
				$test_filters[ $hook ][] = [
					'function' => $callback,
					'priority' => $priority,
					'accepted_args' => $accepted_args
				];
				
				return true;
			}
		}
		
		// Additional WordPress functions commonly used in plugins
		if ( ! function_exists( 'wp_die' ) ) {
			function wp_die( $message = '', $title = '', $args = [] ) {
				throw new Exception( esc_html( $message ) );
			}
		}
		
		if ( ! function_exists( 'is_admin' ) ) {
			function is_admin() {
				return false; // Default for unit tests
			}
		}
		
		if ( ! function_exists( 'current_user_can' ) ) {
			function current_user_can( $capability ) {
				return true; // Allow all capabilities in unit tests
			}
		}
		
		if ( ! function_exists( 'get_option' ) ) {
			function get_option( $option, $default = false ) {
				return $default;
			}
		}
		
		if ( ! function_exists( 'update_option' ) ) {
			function update_option( $option, $value, $autoload = null ) {
				return true;
			}
		}
		
		if ( ! function_exists( 'register_block_type' ) ) {
			function register_block_type( $block_type, $args = [] ) {
				return true;
			}
		}
		
		if ( ! function_exists( 'WP_Filesystem' ) ) {
			function WP_Filesystem() {
				return false;
			}
		}
		
		// Load the plugin after setting up mocks
		try {
			require_once dirname( __DIR__ ) . '/my-plugin-boilerplate.php';
		} catch ( Exception $e ) {
			echo "Warning: Could not load main plugin file: " . esc_html( $e->getMessage() ) . "\n";
		}
	}
}

echo "PHPUnit Bootstrap loaded successfully.\n";

// Helper function to apply test filters (for unit tests).
if ( ! function_exists( 'apply_test_filters' ) ) {
	function apply_test_filters( $tag, $value = '', ...$args ) {
		global $test_filters;
		
		if ( ! isset( $test_filters[ $tag ] ) ) {
			return $value;
		}
		
		foreach ( $test_filters[ $tag ] as $filter ) {
			if ( is_callable( $filter['function'] ) ) {
				$value = call_user_func_array( $filter['function'], array_merge( [ $value ], $args ) );
			}
		}
		
		return $value;
	}
}

// Helper function to check if a filter exists.
if ( ! function_exists( 'has_filter' ) ) {
	function has_filter( $tag, $function_to_check = false ) {
		global $test_filters;
		
		if ( ! isset( $test_filters[ $tag ] ) ) {
			return false;
		}
		
		if ( $function_to_check === false ) {
			return ! empty( $test_filters[ $tag ] );
		}
		
		foreach ( $test_filters[ $tag ] as $filter ) {
			if ( $filter['function'] === $function_to_check ) {
				return $filter['priority'];
			}
		}
		
		return false;
	}
}

$test_filters = [];

if ( ! function_exists( 'tests_add_filter' ) ) {
    function tests_add_filter( $tag, $function_to_add, $priority = 10, $accepted_args = 1 ) {
        global $test_filters;
        
        if ( ! isset( $test_filters[ $tag ] ) ) {
            $test_filters[ $tag ] = [];
        }
        
        $test_filters[ $tag ][] = [
            'function' => $function_to_add,
            'priority' => $priority,
            'accepted_args' => $accepted_args
        ];
        
        return true;
    }
}