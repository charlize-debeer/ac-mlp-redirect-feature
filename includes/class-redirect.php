<?php
/**
 * Class Redirect.
 *
 * @package ACGeoRedirect.
 */

namespace Ac_Geo_Redirect;

/**
 * Class Plugin
 *
 * @package Redirect
 */
class Redirect {

	/**
	 * Class Redirect
	 *
	 * @var Redirect
	 */
	protected static $instance;

	/**
	 * Redirect constructor.
	 */
	private function __construct() {
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
	 * Maybe add the popup to the footer.
	 */
	public function add_popup() {
		include \dirname( __DIR__ ) . '/templates/popup.php';
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
