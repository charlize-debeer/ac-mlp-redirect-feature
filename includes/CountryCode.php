<?php

namespace Ac_Geo_Redirect;

class CountryCode {

	/**
	 * @var array
	 */
	protected $headers = [];

	/**
	 * @var string
	 */
	protected $debug_header_country_code = 'x-ac-debug-country-code';

	public function __construct() {
		$this->set_headers();
	}

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
	protected function get_header( string $header = null ) :? string {
		return ( ! empty( $this->headers[ $header ] ) ) ? $this->headers[ $header ] : null;
	}

	/**
	 * Get the debug country code. (Header: x-ac-debug-country-code) if set.
	 *
	 * @return string
	 */
	protected function get_debug_country_code() :? string {
		return ( ! empty( $this->headers[ $this->debug_header_country_code ] ) )
			? strtolower( $this->headers[ $this->debug_header_country_code ] )
			: null;
	}

}
