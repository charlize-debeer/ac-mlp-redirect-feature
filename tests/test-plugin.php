<?php
/**
 * Contains the test class PluginTest.
 *
 * Tests the Plugin class.
 *
 * @package Ac_Geo_Redirect
 */

namespace Ac_Geo_Redirect;

use PHPUnit\Framework\Constraint\IsType;
/**
 * Plugin class test case.
 *
 * @package Ac_Geo_Redirect
 */
class PluginTest extends \WP_UnitTestCase {

	/**
	 * The instance of the class being tested.
	 *
	 * @var Ac_Geo_Redirect\Plugin
	 */
	private $test_class;

	/**
	 * Setting up the data for testing.
	 */
	public function setUp() {
		$this->test_class = Plugin::get_instance();

		parent::setUp();
	}

	/**
	* Tests that the init function sets up the hooks correctly.
	*/
	public function test_init() {
		$this->test_class->init();

		$is_enqueuing_scripts = has_action( 'enqueue_scripts', [ $this->test_class, 'enqueue_scripts' ] );
		$this->assertInternalType( IsType::TYPE_INT, $is_enqueuing_scripts, 'Scripts not set to be enqueued' );
		$is_enqueuing_styles = has_action( 'enqueue_scripts', [ $this->test_class, 'enqueue_styles' ] );
		$this->assertInternalType( IsType::TYPE_INT, $is_enqueuing_styles, 'Styles not set to be enqueued' );

		/**
		 * Test for checking if a filter is set
		 *
		 * $added_filter_some_filter = has_filter( 'some_filter', [ $this->test_class, 'some_filter' ] );
		 * $this->assertInternalType( IsType::TYPE_INT, $added_filter_some_filter, 'Filter "some filter" not set' );
		 */
	}

	/**
	* Tests that the path is returned correctly.
	*/
	public function test_get_path() {
		$plugin_path = dirname( untrailingslashit( plugin_dir_path( __FILE__ ) ) );
		$this->assertEquals( $plugin_path, $this->test_class->get_path() );
	}

	/**
	* Tests that the class path is returned correctly.
	*/
	public function test_get_class_path() {
		$class_path = dirname( untrailingslashit( plugin_dir_path( __FILE__ ) ) ) . '/includes';
		$this->assertEquals( $class_path, $this->test_class->get_class_path() );
	}

	/**
	* Tests that the url is returned correctly.
	*/
	public function test_get_url() {
		$plugin_url = dirname( untrailingslashit( plugins_url( '/', __FILE__ ) ) );
		$this->assertEquals( $plugin_url, $this->test_class->get_url() );
	}

	/**
	* Tests that the slug is returned correctly.
	*/
	public function test_get_plugin_slug() {
		$this->assertEquals( 'ac-geo-redirect', $this->test_class->get_plugin_slug() );
	}

	/**
	 * Tests that the script(s) are enqueued properly.
	 */
	public function test_enqueue_scripts() {
		$this->test_class->enqueue_scripts();
		$script_enqueued = wp_script_is( $this->test_class->get_plugin_slug() . '-script' );
		$this->assertTrue( $script_enqueued, 'Core scripts not enqueued' );
	}
	/**
	 * Tests that the style(s) are enqueued properly.
	 */
	public function test_enqueue_styles() {
		$this->test_class->enqueue_styles();
		$styles_enqueued = wp_style_is( $this->test_class->get_plugin_slug() . '-style' );
		$this->assertTrue( $styles_enqueued );
	}
}
