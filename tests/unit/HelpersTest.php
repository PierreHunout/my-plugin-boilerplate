<?php
/**
 * Unit Tests for Helpers Class
 *
 * @package MyPluginBoilerplate
 */

namespace MyPluginBoilerplate\Tests\Unit;

use MyPluginBoilerplate\Helpers;
use PHPUnit\Framework\TestCase;
use Yoast\PHPUnitPolyfills\TestCases\TestCase as PolyfillTestCase;

/**
 * Test the Helpers class
 */
class HelpersTest extends PolyfillTestCase {

	/**
	 * Test that the Helpers class can be instantiated
	 */
	public function test_helpers_class_exists() {
		$this->assertTrue( class_exists( Helpers::class ) );
	}

	/**
	 * Test get_filesystem method exists
	 */
	public function test_get_filesystem_method_exists() {
		$this->assertTrue( method_exists( Helpers::class, 'get_filesystem' ) );
	}

	/**
	 * Test get_filesystem returns something when WordPress is not available
	 */
	public function test_get_filesystem_without_wordpress() {
		// This test runs when WordPress test environment is not available
		if ( ! function_exists( 'WP_Filesystem' ) ) {
			$result = Helpers::get_filesystem();
			// Should return false when WordPress filesystem is not available
			$this->assertFalse( $result );
		} else {
			$this->markTestSkipped( 'WordPress environment available, skipping unit test.' );
		}
	}

	/**
	 * Example test for string manipulation
	 */
	public function test_string_manipulation_example() {
		$input = 'my-plugin-boilerplate';
		$expected = 'My Plugin Boilerplate';
		
		// Convert kebab-case to title case
		$result = ucwords( str_replace( '-', ' ', $input ) );
		
		$this->assertEquals( $expected, $result );
	}

	/**
	 * Test data provider example
	 * 
	 * @dataProvider string_conversion_provider
	 */
	public function test_string_conversions( $input, $expected ) {
		$result = ucwords( str_replace( '-', ' ', $input ) );
		$this->assertEquals( $expected, $result );
	}

	/**
	 * Data provider for string conversion tests
	 * 
	 * @return array
	 */
	public function string_conversion_provider() {
		return [
			'plugin name' => [ 'my-plugin-boilerplate', 'My Plugin Boilerplate' ],
			'single word' => [ 'wordpress', 'Wordpress' ],
			'multiple dashes' => [ 'test-case-example', 'Test Case Example' ],
			'empty string' => [ '', '' ],
		];
	}
}