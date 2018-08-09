<?php

namespace Ac_Geo_Redirect;

/**
 * Class Plugin
 *
 * @package Ac_Geo_Redirect
 */
final class Plugin {

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	protected const VERSION = '0.0.1';

	/**
	 * Plugin instance.
	 *
	 * @var null|self
	 */
	protected static $instance = null;

	/**
	 * URL to the plugin.
	 *
	 * @var string
	 */
	protected $plugin_url;

	/**
	 * Path to the plugin directory.
	 *
	 * @var string
	 */
	protected $plugin_path;

	/**
	 * Plugin slug (used as ID for the enqueued assets).
	 *
	 * @var string
	 */
	protected $plugin_slug = 'ac-geo-redirect';

	/**
	 * The path to the plugin JavaScript file.
	 *
	 * @var string
	 */
	protected $main_js_file = '/assets/javascript/ac-geo-redirect.js';

	/**
	 * * The path to the plugin CSS file.
	 *
	 * @var string
	 */
	protected $main_css_file = '/assets/css/ac-geo-redirect.css';

	/**
	 * Plugin constructor.
	 */
	private function __construct() {
		spl_autoload_register( [ $this, 'autoload' ] );

		$this->plugin_url  = dirname( untrailingslashit( plugins_url( '/', __FILE__ ) ) );
		$this->plugin_path = dirname( untrailingslashit( plugin_dir_path( __FILE__ ) ) );

		add_action( 'init', [ $this, 'init' ] );

		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_styles' ] );

		/**
         * If you use other hooks, add them here.
         *
         * add_action( 'some_action', [ $this, 'do_some_action' ] );
         */

		/**
		 * If you have other singletons to load in, do so here.
		 *
		 * Options_Page::get_instance();
		 * Some_Other_Class::get_instance();
		 */
	}

	/**
	 * Get class instance
	 *
	 * @return Plugin
	 */
	public static function get_instance() : Plugin {
		if ( null === self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Autoload class files function
	 *
	 * @param string $classname Name of class.
	 */
	public function autoload( $classname ) {
		$classname = explode( '\\', $classname );
		$classfile = sprintf( '%sclass-%s.php',
			plugin_dir_path( __FILE__ ),
			str_replace( '_', '-', strtolower( end( $classname ) ) )
		);

		if ( file_exists( $classfile ) ) {
			require $classfile;
		}
	}

	/**
	 * Add translations. You can call other hooks here.
	 */
	public function init() {
		load_plugin_textdomain( $this->plugin_slug, false, basename( $this->plugin_path ) . '/languages/' );
	}

	/**
	 * The path to the main directory.
	 *
	 * @return string
	 */
	public function get_path() : string {
		return $this->plugin_path;
	}

	/**
	 * The URL to the plugin directory.
	 *
	 * @return string
	 */
	public function get_url() : string {
		return $this->plugin_url;
	}

	/**
	 * The plugin slug.
	 *
	 * @return string
	 */
	public function get_plugin_slug() : string {
		return $this->plugin_slug;
	}

	/**
	 * Register and enqueue scripts.
	 */
	public function enqueue_scripts() {
		wp_enqueue_script(
			$this->plugin_slug . '-script',                             // ID of the script
			$this->plugin_url . '/' . $this->main_js_file,              // URL to the file
			[ 'jquery' ],                                               // JS dependencies
			$this->get_asset_last_modified_time( $this->main_js_file ), // Query string (for cache invalidation)
			true                                                        // Enquque in footer
		);

		/**
		 * Make PHP variables available to JS.
		 *
		 * The JS Namespace can be changed to whatever you need!
		 */

		/** @var string $namespace The namespace for the JS variables */
		$namespace = 'Ac_Geo_Redirect';

		wp_localize_script( $this->plugin_slug . '-script', $namespace, [
			'awesome' => true,
		]);
	}

	/**
	 * Register and enqueue stylesheets.
	 */
	public function enqueue_styles() {
		wp_enqueue_style(
			$this->plugin_slug . '-style',                              // ID of the style
			$this->plugin_url . '/' . $this->main_css_file,             // URL to the file
			[],                                                         // CSS dependencies
			$this->get_asset_last_modified_time( $this->main_css_file ) // Query string (for cache invalidation)
		);
	}

	/**
	 * Get the last modified time for a file in the format YmdHi.
	 *
	 * This is to avoid to specific timestamps that could potentially
	 * differ over different servers.
	 *
	 * @param string $relative_path The relative path to the asset to the plugins directory.
	 *
	 * @return string
	 */
	protected function get_asset_last_modified_time( $relative_path ) : string {
		return date( 'YmdHi', filemtime( $this->plugin_path . $relative_path ) );
	}

}