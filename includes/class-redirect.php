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

}
