<?php

/*
Plugin Name: AC Geo Redirect
Plugin URI: https://angrycreative.se
Description: A plugin to fix geo redirect for multilingualpress
Version: 0.0.1
Author: Angry Creatives
Author URI: https://angrycreative.se
License: GPL2
*/

use Ac_Geo_Redirect\Plugin;

define( 'AC_GEO_REDIRECT_FILE', __FILE__ );
define( 'AC_GEO_REDIRECT_DIR', dirname( LAVENDLA_FUNNEL_FILE ) );
include_once LAVENDLA_FUNNEL_DIR . '/includes/class-plugin.php';

Plugin::get_instance();
