<?php
/**
 * Class Redirect.
 *
 * @package Ac_Geo_Redirect.
 */

namespace Ac_Geo_Redirect;

/**
 * Class Plugin
 *
 * @package Ac_Geo_Redirect
 */
class Redirect extends BasePlugin {

	/**
	 * Class Redirect
	 *
	 * @var Redirect
	 */
	protected static $instance;

	/**
	 * Redirect constructor.
	 */
	public function __construct() {
		parent::__construct();

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
	public function get_template( $template_name = 'popup.php' ) : void {
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
	protected function get_country_code() :? string {
		$header = ( defined( 'AC_GEO_REDIRECT_HEADER' ) ) ? AC_GEO_REDIRECT_HEADER : 'X-GeoIP-Country';
		$header = apply_filters( 'ac_geo_redirect_header', $header );

		$code = $this->get_header( $header );
		if ( ! $code ) {
			$header = 'CF-IPCountry';
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
	protected function get_header( string $header ) :? string {
		return ( ! empty( $_SERVER[ $header ] ) ) ? $_SERVER[ $header ] : null;
	}

	/**
	 * Get the debug country code. (Header: HTTP_X_AC_DEBUG_COUNTRY_CODE) if set.
	 *
	 * @return string
	 */
	protected function get_debug_country_code() :? string {
		return ( ! empty( $_SERVER['HTTP_X_AC_DEBUG_COUNTRY_CODE'] ) ) ? strtolower( $_SERVER['HTTP_X_AC_DEBUG_COUNTRY_CODE'] ) : null;
	}

}
