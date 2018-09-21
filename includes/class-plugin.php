<?php

namespace Ac_Geo_Redirect;

use Inpsyde\MultilingualPress;

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
	 * Enqueue the scripts
	 */
	public function enqueue_scripts() {
		wp_enqueue_script(
			$this->plugin_slug . '-script',
			$this->plugin_url . $this->main_js_file,
			[ 'jquery' ],
			$this->get_asset_last_modified_time( $this->main_js_file ),
			true
		);

		try {
			wp_localize_script( 'ac-geo-redirect-script', 'AcGeoRedirect', [
				'currentBlogData' => $this->get_current_blog_data(),
				'siteMap'         => $this->get_assigned_languages(),
				'defaultLocale'   => apply_filters( 'ac_geo_redirect_default_locale', 'us' ),
				'redirectLocale'  => Redirect::get_instance()->get_locale(),
			] );
		} catch ( MultilingualPress\Framework\Database\Exception\NonexistentTable $e ) {
			add_action( 'admin_notices', function() use ( $e ) {
				?>
				<div class="notice notice-error is-dismissible">
					<p><?php echo esc_html( $e->getMessage() ); ?></p>
				</div>
				<?php
			} );
		}
	}

	/**
	 * Get array of data for the current blog.

	 * @return array
	 * @throws MultilingualPress\Framework\Database\Exception\NonexistentTable
	 */
	public function get_current_blog_data() : array {
		try {
			$locale   = MultilingualPress\currentSiteLocale();
			$lng_code = $this->get_lang_code_from_locale( $locale );

			return apply_filters( 'ac_geo_redirect_current_blog_data', [
				'id'          => (int) get_current_blog_id(),
				'domain'      => $this->remove_protocoll( get_home_url( '' ) ),
				'lang'        => $lng_code,
				'flag'        => apply_filters( "ac_geo_redirect_{$lng_code}_flag", $this->plugin_url . '/assets/flags/' . $lng_code . '.svg' ),
				'countryCode' => $lng_code,
				'locale'      => $locale,
			] );
		} catch ( MultilingualPress\Framework\Database\Exception\NonexistentTable $e ) {
			throw $e;
		}
	}

	/**
	 * Get the 2 character lang. code from the locale.
	 *
	 * @param null|string $locale Locale.
	 *
	 * @return null|string
	 */
	protected function get_lang_code_from_locale( $locale = null ) {
		if ( null === $locale ) {
			return null;
		}

		$locale = explode( '_', $locale, 2 );
		return strtolower( end( $locale ) );
	}

	/**
	 * @return array
	 * @throws MultilingualPress\Framework\Database\Exception\NonexistentTable
	 */
	protected function get_assigned_languages() : array {
		try {
			$assigned_languages = [];
			foreach ( MultiLingualPress\assignedLanguages() as $site_id => $press_language ) {
				$lng_code = $this->get_lang_code_from_locale( $press_language->locale() );

				$assigned_languages[ $lng_code ] = [
					'locale'      => $press_language->locale(),
					'countryCode' => $lng_code,
					'region'      => \Locale::getDisplayRegion( $press_language->locale() ),
					'id'          => $site_id,
					'domain'      => $this->remove_protocoll( get_site_url( $site_id, '' ) ),
					'url'         => get_site_url( $site_id ),
					'flag'        => apply_filters( "ac_geo_redirect_{$lng_code}_flag", $this->plugin_url . '/assets/flags/' . $lng_code . '.svg' ),
					't10ns'       => get_option( 'agr_options' ) ?: [
						'header'    => esc_html__( 'Ship to', 'ac-geo-redirect' ),
						'subHeader' => esc_html__( 'Please select the region for where you want your purchases shipped.', 'ac-geo-redirect' ),
						'takeMeTo'  => esc_html__( 'Go to', 'ac-geo-redirect' ),
						'remainOn'  => esc_html__( 'Stay at', 'ac-geo-redirect' ),
					],
				];
			}

			return apply_filters( 'ac_geo_redirect_assigned_languages', $assigned_languages );
		} catch ( MultilingualPress\Framework\Database\Exception\NonexistentTable $e ) {
			throw $e;
		}
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
			$this->plugin_slug . '-style',
			$this->plugin_url . $this->main_css_file,
			[],
			$this->get_asset_last_modified_time( $this->main_css_file )
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
