<?php

namespace Ac_Geo_Redirect;

class Template {

	public function init() {
		add_action( 'wp_footer', [ $this, 'add_popup' ] );
	}

	/**
	 * Include template if we could locate it.
	 *
	 * @param string $template_name Template name.
	 */
	public function get_template( string $template_name = 'popup.php' ) : void {
		$located = $this->locate_template( $template_name );

		if ( ! file_exists( $located ) ) {
			_doing_it_wrong( __FUNCTION__, sprintf( ' <code>%s </code> does not exist . ', esc_html( $located ) ), esc_html( self::VERSION ) );
			return;
		}

		include $located;
	}

	/**
	 * Helper to locate templates.
	 *
	 * @param string $template_name Template name.
	 *
	 * @return string
	 */
	public function locate_template( string $template_name ) : string {
		$template_name = apply_filters( 'ac_geo_redirect_template_name', $template_name );
		$template_path = AC_GEO_REDIRECT_DIR . '/includes';
		$default_path  = AC_GEO_REDIRECT_DIR . '/templates';
		$template      = locate_template(
			[
				trailingslashit( $template_path ) . $template_name,
				$template_name,
			]
		);

		if ( ! $template ) {
			$template = $default_path . '/' . $template_name;
		}

		return apply_filters( 'ac_geo_redirect_template', $template, $template_name, $template_path );
	}

	/**
	 * Template modal.
	 */
	public function add_popup() : void {
		$this->get_template();
	}
}
