<?php

namespace Ac_Geo_Redirect;

use WP_REST_Request;

class API {

	/**
	 * @var string The API namespace.
	 */
	protected $namespace = 'ac-geo-redirect/v1';

	public function init() {
		add_action( 'rest_api_init', [ $this, 'add_rest_endpoint' ] );
	}

	/**
	 * Register the endpoint
	 */
	public function add_rest_endpoint() : void {
		register_rest_route(
			$this->namespace,
			'/get-country-code',
			[
				'methods'  => 'POST', // workaround for cache!
				'callback' => [ $this, 'get_country_code' ],
			]
		);
	}

	public function get_namespace() : string {
		return $this->namespace;
	}

	/**
	 * @param WP_REST_Request $request
	 *
	 * @return array
	 */
	public function get_country_code( WP_REST_Request $request ) : array {
		return [
			'code' => ac_geo_redirect_plugin()->get_country_code()->get_locale(),
		];
	}
}
