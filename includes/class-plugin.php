<?php

namespace Ac_Geo_Redirect;

use Inpsyde\MultilingualPress as multilingual;
use Ac_Geo_Redirect\AdminSettings;

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
	private const VERSION = '0.0.1';

	/**
	 * Plugin instance.
	 *
	 * @var null|self
	 */
	private static $instance = null;

	/**
	 * URL to the plugin.
	 *
	 * @var string
	 */
	private $plugin_url;

	/**
	 * Path to the plugin directory.
	 *
	 * @var string
	 */
	private $plugin_path;

	/**
	 * Plugin slug (used as ID for the enqueued assets).
	 *
	 * @var string
	 */
	private $plugin_slug = 'ac-geo-redirect';

	/**
	 * The path to the plugin JavaScript file.
	 *
	 * @var string
	 */
	private $main_js_file = '/assets/javascript/ac-geo-redirect.js';

	/**
	 * * The path to the plugin CSS file.
	 *
	 * @var string
	 */
	private $main_css_file = '/assets/css/ac-geo-redirect.css';

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
	public function autoload( $classname ) : void {
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

		Redirect::get_instance();

		if ( is_admin() ) {
			AdminSettings::get_instance();
		}

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
			$this->plugin_url . $this->main_js_file,              // URL to the file
			[ 'jquery' ],                                               // JS dependencies
			$this->get_asset_last_modified_time( $this->main_js_file ), // Query string (for cache invalidation)
			true                                                        // Enquque in footer
		);

		wp_localize_script( 'ac-geo-redirect-script', 'AcGeoRedirect', [
			//'sku'				=> $this->sku,
			//'query_string'      => $query_string,
			'currentBlogData' => $this->get_current_blog_data(),
			'siteMap'         => $this->get_assigned_languages(),
		] );
	}

	/**
	 * Get array of data for the current blog.
	 *
	 * @return array
	 */
	public function get_current_blog_data() {
		$blog_id = (int) get_current_blog_id();

		$locale = multilingual\currentSiteLocale();

		$country_code = $this->get_lang_code_from_locale( $locale, $blog_id );

		return [
			'id'          => $blog_id,
			'domain'      => $this->remove_protocoll( get_home_url( '' ) ),
			'lang'        => $country_code,
			'countryCode' => $country_code,
			'locale'      => $locale,
		];
	}

	/**
	 * Get the 2 character lang. code from the locale.
	 *
	 * @param null|string $locale Locale.
	 *
	 * @return null|string
	 */
	protected function get_lang_code_from_locale( $locale = null, $blogg_id = 1 ) {
		if ( null === $locale ) {
			return null;
		}

		return strtolower( @end( ( explode( '_', $locale, 2 ) ) ) );
	}

	/**
	 * Get the current list of that site assigned.
	 */
	public function get_assigned_languages() {
		return $this->_simple_assigned_languages();
	}

	protected function _simple_assigned_languages() {
		$assigned_languages = [];

		foreach ( multilingual\assignedLanguages() as $site_id => $press_language ) {
			$assigned_languages[ $press_language->isoCode() ] = [
				'locale'      => $press_language->locale(),
				'countryCode' => $press_language->isoCode(),
				'region'      => \Locale::getDisplayRegion( $press_language->locale() ),
				'id'          => $site_id,
				'domain'      => $this->remove_protocoll( get_site_url( $site_id, '' ) ),
				'url'         => get_site_url( $site_id ),
				't10ns'       => get_option( 'agr_option' ) ?: [
					'header'    => esc_html__( 'Ship to' ),
					'subHeader' => esc_html__( 'Please select the region for where you want your purchases shipped.' ),
					'takeMeTo'  => esc_html__( 'Go to' ),
					'remainOn'  => esc_html__( 'Stay at' ),
				],
			];
		}

		return $assigned_languages;
	}

	public function get_county_site_map() {

		if ( ! is_multisite() ) {
			return [];
		}

		$sites = get_sites();

		$map = [];

		foreach ( $sites as $site ) {
			// Hide draft:wtf domain.
			$tld = strtolower( substr( $site->domain, strripos( $site->domain, '.' ) + 1 ) );
			if ( 'wtf' === $tld ) {
				continue;
			}

			$blog_data = $this->get_redirect_blog_data( (int) $site->blog_id );

			if ( ! empty( $blog_data ) ) {
				$map[ $blog_data['countryCode'] ] = $blog_data;
			}
		}

		return $map;
	}

	/**
	 * Get the redirect Blog data.
	 *
	 * @return array.
	 */
	public function get_redirect_blog_data( $blog_id ) : array {

		var_dump( multilingual\assignedLanguageTags() );

		return [
			'locale'      => $locale,
			'countryCode' => $country_code,
			'region'      => $region,
			'id'          => $blog_id,
			'domain'      => $this->remove_protocoll( get_site_url( $blog_id, '' ) ),
			'url'         => get_site_url( $blog_id ),
		];
	}

	/**
	 * Get the blog ID from a country code.
	 *
	 * @param string $country_code The 2 character country code.
	 *
	 * @return bool|int
	 */
	protected function get_blog_id_from_country_code( $country_code = '' ) {

	}

	/**
	 * Remove the protocoll from a URL.
	 *
	 * @param string $url URL.
	 *
	 * @return string
	 */
	protected function remove_protocoll( $url ) {
		return preg_replace( '/http(s)?:\/\//', '', $url );
	}

	/**
	 * Register and enqueue stylesheets.
	 */
	public function enqueue_styles() {
		wp_enqueue_style(
			$this->plugin_slug . '-style',                              // ID of the style
			$this->plugin_url . $this->main_css_file,             // URL to the file
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
