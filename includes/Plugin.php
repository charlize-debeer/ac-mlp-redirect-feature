<?php

namespace Ac_Geo_Redirect;

use Inpsyde\MultilingualPress;
use Inpsyde\MultilingualPress\Framework\Database\Exception\NonexistentTable;
use Locale;
use Throwable;

final class Plugin {

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
	 * @var T10ns
	 */
	protected $t10ns;

	/**
	 * @var CountryCode
	 */
	protected $country_code;

	/**
	 * @var API
	 */
	protected $api;

	/**
	 * Plugin constructor.
	 *
	 * @param SettingsPage $settings_page
	 * @param T10ns $t10ns
	 * @param API $api
	 * @param Template $template
	 * @param CountryCode $country_code
	 */
	public function __construct(
		SettingsPage $settings_page,
		T10ns $t10ns,
		API $api,
		Template $template,
		CountryCode $country_code
	) {
		$settings_page->init();
		$api->init();
		$template->init();

		$this->api = $api;
		$this->t10ns = $t10ns;
		$this->country_code = $country_code;
		$this->plugin_url  = dirname( untrailingslashit( plugins_url( '/', __FILE__ ) ) );
		$this->plugin_path = dirname( untrailingslashit( plugin_dir_path( __FILE__ ) ) );

		$this->init();
	}

	/*
	 * Register actions
	 */
	protected function init() : void {
		add_action( 'init', [ $this, 'maybe_show_notices' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_styles' ] );
	}

	/**
	 * @return CountryCode
	 */
	public function get_country_code() : CountryCode {
		return $this->country_code;
	}

	/**
	 * Show a notice is MLP V.3 is not active on the site.
	 */
	public function show_mlp_required_notice() : void {
		?>
		<div class="notice notice-error is-dismissible">
			<p><?php echo esc_html_e( 'MultilingualPress V.3 is a depenency of the AC Geo Redirect plugin. Please make sure it\'s installed and activated.', 'ac-geo-redirect' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Add translations. You can call other hooks here.
	 */
	public function maybe_show_notices() : void {
		if ( ! function_exists( 'Inpsyde\MultilingualPress\currentSiteLocale' ) ) {
			add_action( 'network_admin_notices', [ $this, 'show_mlp_required_notice' ] );
			add_action( 'admin_notices', [ $this, 'show_mlp_required_notice' ] );

			return;
		}

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
	 * Get the default locale for a site (blog) on a network.
	 *
	 * This uses MLP's hreflang: X-default setting that is found
	 * in the network setting for a site (blog) as the default.
	 *
	 * @return string
	 * @throws NonexistentTable
	 */
	protected function get_default_locale() : string {
		$mlp_settings   = get_site_option( 'multilingualpress_site_settings', true );
		$blog_id        = get_current_blog_id();
		$default_locale = apply_filters( 'ac_geo_redirect_default_locale', 'us' );

		if ( ! array_key_exists( $blog_id, $mlp_settings ) ) {
			return $default_locale;
		}

		$mlp_data = $mlp_settings[ $blog_id ];
		if ( empty( $mlp_data['multilingualpress_xdefault'] ) ) {
			return $default_locale;
		}

		$locale = MultilingualPress\siteLocale( $mlp_data['multilingualpress_xdefault'] );
		if ( empty( $locale ) ) {
			return $default_locale;
		}

		return apply_filters( 'ac_geo_redirect_default_locale', $this->get_lang_code_from_locale( $locale ) );
	}

	/**
	 * Enqueue the scripts
	 */
	public function enqueue_scripts() : void {
		wp_enqueue_script(
			$this->plugin_slug . '-script',
			$this->plugin_url . $this->main_js_file,
			[ 'jquery' ],
			$this->get_asset_last_modified_time( $this->main_js_file ),
			true
		);

		try {
			wp_localize_script(
				'ac-geo-redirect-script',
				'AcGeoRedirect',
				[
					'APIURL'          => '/wp-json/' . $this->api->get_namespace(),
					'currentBlogData' => $this->get_current_blog_data(),
					'siteMap'         => $this->get_assigned_languages(),
					'defaultLocale'   => $this->get_default_locale(),
					'redirectLocale'  => $this->country_code->get_locale(),
					't10ns'           => $this->t10ns->get_t10ns(),
					'defaultT10ns'    => $this->t10ns->get_t10ns( apply_filters( 'ac_geo_redirect_default_t10n_locale', 'en_US' ) ),
				]
			);
		} catch ( NonexistentTable $e ) {
			add_action(
				'admin_notices',
				function() use ( $e ) {
					$this->show_admin_notices( $e );
				}
			);
		}
	}

	/**
     * Show an admin notice if an error is thrown.
     *
	 * @param Throwable $e
	 */
	protected function show_admin_notices( Throwable $e ) : void {
		?>
		<div class="notice notice-error is-dismissible">
			<p><?php echo esc_html( $e->getMessage() ); ?></p>
		</div>
		<?php
	}

	/**
	 * Get array of data for the current blog.
	 *
	 * @return array
	 * @throws NonexistentTable
	 */
	public function get_current_blog_data() : array {
		$locale   = MultilingualPress\currentSiteLocale();
		$lng_code = $this->get_lang_code_from_locale( $locale );

		return apply_filters(
			'ac_geo_redirect_current_blog_data',
			[
				'id'          => (int) get_current_blog_id(),
				'domain'      => $this->remove_protocol( get_home_url( '' ) ),
				'lang'        => $lng_code,
				'countryCode' => $lng_code,
				'locale'      => $locale,
				'region'      => Locale::getDisplayRegion( $locale ),
			]
		);
	}

	/**
	 * Get the 2 character lang. code from the locale.
	 *
	 * @param null|string $locale Locale.
	 *
	 * @return null|string
	 */
	protected function get_lang_code_from_locale( string $locale = null ) :? string {
		if ( null === $locale ) {
			return null;
		}

		$locale = explode( '_', $locale, 2 );
		return strtolower( end( $locale ) );
	}

	/**
	 * @return array
	 * @throws NonexistentTable
	 */
	protected function get_assigned_languages() : array {
		$assigned_languages = [];

		foreach ( MultiLingualPress\assignedLanguages() as $site_id => $press_language ) {
			$lng_code = $this->get_lang_code_from_locale( $press_language->locale() );
			$region   = Locale::getDisplayRegion( $press_language->locale(), $press_language->locale() );

			$assigned_languages[ $lng_code ] = [
				'locale'      => $press_language->locale(),
				'countryCode' => $lng_code,
				'region'      => $region,
				'id'          => $site_id,
				'domain'      => $this->remove_protocol( get_site_url( $site_id, '' ) ),
				'url'         => get_site_url( $site_id ),
				't10ns'       => $this->t10ns->get_t10ns( $press_language->locale() ),
			];
		}

		return apply_filters( 'ac_geo_redirect_assigned_languages', $assigned_languages );
	}

	/**
	 * Remove the protocoll from a URL.
	 *
	 * @param string $url URL.
	 *
	 * @return string
	 */
	protected function remove_protocol( string $url ) : string {
		return preg_replace( '/http(s)?:\/\//', '', $url );
	}

	/**
	 * Register and enqueue stylesheets.
	 */
	public function enqueue_styles() : void {
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
	protected function get_asset_last_modified_time( string $relative_path ) : string {
		return date( 'YmdHi', filemtime( $this->plugin_path . $relative_path ) );
	}
}
