<?php

namespace Ac_Geo_Redirect;

use Inpsyde\MultilingualPress\Core\Admin\SiteSettingsRepository;

class Redirect extends BasePlugin {

	/**
	 * Class Redirect
	 *
	 * @var Redirect
	 */
	protected static $instance;

	/**
	 * @var array
	 */
	protected $headers = [];

	/**
	 * @var string
	 */
	protected $debug_header_country_code = 'http_x_ac_debug_country_code';

	/**
	 * Redirect constructor.
	 */
	protected function __construct() {
		parent::__construct();

		$this->set_headers();
		add_action( 'wp_footer', [ $this, 'add_popup' ] );
	}

	/**
	 * Get class instance
	 *
	 * @return Redirect
	 */
	public static function get_instance() : Redirect {
		if ( null === self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Get the headers!
	 */
	protected function set_headers() : void {
		if ( ! function_exists( 'apache_request_headers' ) ) {
			return;
		}

		$headers = [];
		foreach ( apache_request_headers() as $key => $value ) {
			$headers[ strtolower( $key ) ] = $value;
		}

		$this->headers = $headers;
	}

	public function get_headers() : array {
		return $this->headers;
	}

	/**
	 * Output Locale if the headers are set.
	 *
	 * @return string
	 */
	public function get_locale() :? string {
		return $this->get_debug_country_code() ?: $this->get_country_code();
	}

	/**
	 * Include template if we could locate it.
	 *
	 * @param string $template_name Template name.
	 */
	public function get_template( string $template_name = 'popup.php' ) : void {
		$located = $this->locate_template( $template_name );

		if ( ! file_exists( $located ) ) {
			_doing_it_wrong( __FUNCTION__, sprintf( ' <code>%s </code> does not exist . ', esc_html( $located ) ), esc_html( self::VERSION ) );
			return;
		}

		include $located;
	}

	/**
	 * Helper to locate templates.
	 *
	 * @param string $template_name Template name.
	 *
	 * @return string
	 */
	public function locate_template( string $template_name ) : string {
		$template_name = apply_filters( 'ac_geo_redirect_template_name', $template_name );
		$template_path = $this->plugin_path . ' / ';
		$default_path  = $this->plugin_path . '/templates';
		$template      = locate_template(
			[
				trailingslashit( $template_path ) . $template_name,
				$template_name,
			]
		);

		if ( ! $template ) {
			$template = $default_path . '/' . $template_name;
		}

		return apply_filters( 'ac_geo_redirect_template', $template, $template_name, $template_path );
	}

	/**
	 * Template modal.
	 */
	public function add_popup() : void {
		$this->get_template();
	}

	/**
	 * @return null|string
	 */
	public function get_country_code() :? string {
		$header = ( defined( 'AC_GEO_REDIRECT_HEADER' ) ) ? AC_GEO_REDIRECT_HEADER : 'x-country-code';
		$header = strtolower( apply_filters( 'ac_geo_redirect_header', $header ) );
		$code   = $this->get_header( $header );

		if ( ! $code ) {
			$header = 'cf-ipcountry';
			$code   = $this->get_header( $header );
		}

		$code = apply_filters( 'ac_geo_redirect_visitor_country_code', $code );

		return ( $code ) ? strtolower( $code ) : null;
	}

	/**
	 * @param string $header
	 *
	 * @return null|string
	 */
	public function get_header( string $header = null ) :? string {
		return ( ! empty( $this->headers[ $header ] ) ) ? $this->headers[ $header ] : null;
	}

	/**
	 * Get the debug country code. (Header: HTTP_X_AC_DEBUG_COUNTRY_CODE) if set.
	 *
	 * @return string
	 */
	protected function get_debug_country_code() :? string {
		return ( ! empty( $this->headers[ $this->debug_header_country_code ] ) )
			? strtolower( $this->headers[ $this->debug_header_country_code ] )
			: null;
	}
}
