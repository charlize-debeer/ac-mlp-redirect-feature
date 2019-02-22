<?php
/**
 * Class BasePlugin.
 *
 * @package Ac_Geo_Redirect
 */

namespace Ac_Geo_Redirect;

/**
 * Class BasePlugin
 *
 * @package Ac_Geo_Redirect
 */
class BasePlugin {

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	protected const VERSION = '1.0.2';

	/**
	 * URL to the plugin.
	 *
	 * @var string
	 */
	protected $plugin_url;

	/**
	 * Path to the plugin directory.
	 *
	 * @var string
	 */
	protected $plugin_path;

	/**
	 * Plugin slug (used as ID for the enqueued assets).
	 *
	 * @var string
	 */
	protected $plugin_slug = 'ac-geo-redirect';

	/**
	 * Plugin constructor.
	 */
	protected function __construct() {
		$this->plugin_url  = \dirname( untrailingslashit( plugins_url( '/', __FILE__ ) ) );
		$this->plugin_path = \dirname( untrailingslashit( plugin_dir_path( __FILE__ ) ) );
	}
}
