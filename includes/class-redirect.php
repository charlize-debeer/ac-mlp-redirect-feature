<?php

namespace Ac_Geo_Redirect;

/**
 * Class Plugin
 *
 * @package Redirect
 */
class Redirect {

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
	 * @return Plugin
	 */
	public static function get_instance() : Plugin {
		if ( null === self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}


	/**
	 * Output ESI Locale. (Header: X-Ac-Debug-Country-Code) if set.
	 *
	 * @return string
	 */
	public function output_esi_locale() {
		$debug_country_code = $this->get_debug_country_code();
		?>
		<script type='text/javascript'>
			var iDealGeoRedirectLocale = '<?php echo (
				$debug_country_code ?
				$debug_country_code :
				'<esi:include src="/esi/geoip_country"/>'
			); ?>';
		</script>
		<?php

	}

	/**
	 * Maybe add the popup to the footer.
	 */
	public function add_popup() {
		include dirname( __FILE__ ) . '/popup.php';
	}


	/**
	 * Get the debug country code. (Header: HTTP_X_IDEAL_DEBUG_COUNTRY_CODE) if set.
	 *
	 * @return string
	 */
	protected function get_debug_country_code() {
		if ( ! empty( $_SERVER['HTTP_X_AC_DEBUG_COUNTRY_CODE'] ) ) {
			return strtoupper( $_SERVER['HTTP_X_AC_DEBUG_COUNTRY_CODE'] );
		}
		return false;
	}



}
