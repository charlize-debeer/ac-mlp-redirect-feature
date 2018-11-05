<?php

/*
Plugin Name: AC Geo Redirect
Plugin URI: https://angrycreative.se
Description: A GEO IP plugin for use with MultilingualPress V.3.
Version: 1.0.1
Author: Angry Creatives
Author URI: https://angrycreative.se
License: GPL2
*/

use Ac_Geo_Redirect\Plugin;

define( 'AC_GEO_REDIRECT_FILE', __FILE__ );
define( 'AC_GEO_REDIRECT_DIR', dirname( AC_GEO_REDIRECT_FILE ) );

include_once AC_GEO_REDIRECT_DIR . '/includes/class-plugin.php';

Plugin::get_instance();
