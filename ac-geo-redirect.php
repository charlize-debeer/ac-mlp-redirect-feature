<?php

/*
Plugin Name: AC Geo Redirect
Plugin URI: https://angrycreative.se
Description: A GEO IP plugin for use with MultilingualPress V.3.
Version: 1.1.0
Author: Angry Creatives
Author URI: https://angrycreative.se
License: GPL2
*/

use Ac_Geo_Redirect\API;
use Ac_Geo_Redirect\Country_Code;
use Ac_Geo_Redirect\Plugin;
use Ac_Geo_Redirect\Template;
use Ac_Geo_Redirect\Settings_Page;
use Ac_Geo_Redirect\T10ns;

define( 'AC_GEO_REDIRECT_FILE', __FILE__ );
define( 'AC_GEO_REDIRECT_DIR', dirname( AC_GEO_REDIRECT_FILE ) );

spl_autoload_register( function ( $classname ) {
	$classname = explode( '\\', $classname );
	$sub_dir   = ( count( $classname ) > 2 ) ? '/' . $classname[1] : '';

	$classfile = sprintf(
		'%sincludes%s/class-%s.php',
		plugin_dir_path( __FILE__ ),
		str_replace( '_', '-', strtolower( $sub_dir ) ),
		str_replace( '_', '-', strtolower( end( $classname ) ) )
	);

	if ( file_exists( $classfile ) ) {
		include_once $classfile;
	}
} );

function ac_geo_redirect_plugin() {
	static $plugin = null;

	if ( ! $plugin ) {
		$plugin = new Plugin(
			new Settings_Page(),
			new T10ns(),
			new API(),
			new Template(),
			new Country_Code()
		);
	}

	return $plugin;
}

ac_geo_redirect_plugin();
