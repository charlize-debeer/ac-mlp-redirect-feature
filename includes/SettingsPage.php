<?php

namespace Ac_Geo_Redirect;

class SettingsPage {

	/**
	 * Settings_Page constructor.
	 */
	public function init() {
		add_action( 'admin_menu', [ $this, 'add_menu_page' ] );
	}

	/**
	 * Register the submenu page.
	 */
	public function add_menu_page() {
		add_options_page(
			__( 'Geo IP debug', 'ac-geo-redirect' ),
			__( 'Geo IP debug', 'ac-geo-redirect' ),
			'manage-options',
			'geo-ip-debug.php',
			[
				$this,
				'render_options_page',
			]
		);
	}

	/**
	 * Render the options page.
	 */
	public function render_options_page() {
		$country_code = ac_geo_redirect_plugin()->get_country_code();
		?>

		<div class="wrap">
			<h1><?php esc_html_e( 'Geo IP debug info', 'ac-geo-redirect' ); ?></h1>

			<h3><?php esc_html_e( 'Country code:', 'ac-geo-redirect' ); ?></h3>
			<p>
				<?php if ( ! $country_code->get_locale() ) : ?>
					<?php esc_html_e( "We couldn't find a country code header set for this site. Please check that the correct headers are being sent via either NGINX or Cloudflare where appropriate.", 'ac-geo-redirect' ); ?>
				<?php else : ?>
					<strong><?php echo esc_html( $country_code->get_locale() ); ?></strong>
				<?php endif; ?>
			</p>

			<p>
				<?php
				if ( defined( 'AC_GEO_REDIRECT_HEADER' ) ) :
					/* translators: %s the value of the constant: AC_GEO_REDIRECT_HEADER */
					printf( esc_html__( 'The header was defined via the AC_GEO_REDIRECT_HEADER constant as: %s', 'ac-geo-redirect' ), esc_html( AC_GEO_REDIRECT_HEADER ) );
				endif;
				?>
			</p>

			<hr>

			<h3><?php esc_html_e( 'All request headers:', 'ac-geo-redirect' ); ?></h3>

			<ul>
				<?php foreach ( $country_code->get_headers() as $key => $value ) : ?>
					<li><strong><?php echo esc_html( $key ); ?>:</strong> <?php echo esc_html( $value ) . "\n"; ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php
	}
}
