<?php

/*
Plugin Name: AC Geo Redirect
Plugin URI: https://angrycreative.se
Description: A GEO IP plugin for use with MultilingualPress V.3.
Version: 2.0.3
Author: Angry Creatives
Author URI: https://angrycreative.se
License: GPL2
*/

require COMPOSER_VENDOR_DIR . '/autoload.php';

use Ac_Geo_Redirect\API;
use Ac_Geo_Redirect\CountryCode;
use Ac_Geo_Redirect\Plugin;
use Ac_Geo_Redirect\Template;
use Ac_Geo_Redirect\SettingsPage;
use Ac_Geo_Redirect\T10ns;

define( 'AC_GEO_REDIRECT_FILE', __FILE__ );
define( 'AC_GEO_REDIRECT_DIR', dirname( AC_GEO_REDIRECT_FILE ) );

function ac_geo_redirect_plugin() {
	static $plugin = null;

	if ( ! $plugin ) {
		$plugin = new Plugin(
			new SettingsPage(),
			new T10ns(),
			new API(),
			new Template(),
			new CountryCode()
		);
	}

	return $plugin;
}

ac_geo_redirect_plugin();
