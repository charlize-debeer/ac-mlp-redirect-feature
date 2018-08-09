<?php
/**
 * Class Redirect.
 *
 * @package Ac_Geo_Redirect.
 */

namespace Ac_Geo_Redirect;

use Inpsyde\MultilingualPress as multilingual;

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

		add_action( 'wp_enqueue_scripts', [ $this, 'output_esi_locale' ], 20 );
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
	 * Output ESI Locale. (Header: X-Ac-Debug-Country-Code) if set.
	 *
	 * @return void
	 */
	public function output_esi_locale() : void {
		$debug_country_code = $this->get_debug_country_code() ?: '<esi:include src="/esi/geoip_country"/>';
		?>
		<script type='text/javascript'>
					var AcGeoRedirectLocale = '<?php echo $debug_country_code; ?>';
		</script>
		<?php

	}

	/**
	 * Include template if we could locate it.
	 *
	 * @param string $template_name Template name.
	 */
	public function get_template( $template_name = 'popup.php' ) {
		$located = $this->locate_template( $template_name );
		if ( ! file_exists( $located ) ) {
			_doing_it_wrong( __FUNCTION__, sprintf( ' < code>%s </code > does not exist . ', esc_html( $located ) ), esc_html( self::VERSION ) );

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
	public function locate_template( $template_name ) : string {
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

		return apply_filters( 'ac_geo_redirect', $template, $template_name, $template_path );
	}

	/**
	 * Template modal.
	 */
	public function add_popup() : void {
		$this->get_template();
	}

	/**
	 * Get the debug country code. (Header: HTTP_X_AC_DEBUG_COUNTRY_CODE) if set.
	 *
	 * @return string
	 */
	protected function get_debug_country_code() : string {
		if ( ! empty( $_SERVER['HTTP_X_AC_DEBUG_COUNTRY_CODE'] ) ) {
			return strtoupper( $_SERVER['HTTP_X_AC_DEBUG_COUNTRY_CODE'] );
		}

		return false;
	}

}
