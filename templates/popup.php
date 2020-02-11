<?php
/**
 *
 * @package ideal-geo-redirect
 *
 * This template is used to render the popup.
 *
 * You can override this template via the
 * `ac_geo_redirect_template` and
 * `ac_geo_redirect_template_name` hooks.
 *
 * NB! These classnames are REQUIRED for the JS in order to
 * replace the text dynamically.
 */

$image_url = apply_filters( 'ac_ge_redirect_header_image', ac_geo_redirect_plugin()->get_url() . '/assets/images/map.svg' );

?>

<div id="ac-geo-popup" class="ac-geo-popup">
	<div class="ac-geo-popup-inner">
		<div class="ac-geo-popup-inner-text">
			<section>
				<header>
					<img
						class="ac-geo-popup-icon"
						src="<?php echo esc_url( $image_url ); ?>"
						alt="<?php esc_attr_e( 'Map Icon', 'ac-geo-redirect' ); ?>"
					>
					<h3 class="ac-geo-popup-header"></h3>
				</header>

				<div class="ac-geo-popup-redirect">
					<a href="<?php echo esc_url( home_url() ) ?>" class="ac-geo-popup-redirect-link">
						<div class="ac-geo-popup-redirect-to redirect-to"></div>
					</a>
				</div>

				<div class="ac-geo-popup-redirect">
					<a href="#" class="ac-geo-popup-remain-link">
						<div class="ac-geo-popup-redirect-to remain-on"></div>
					</a>
				</div>
			</section>
		</div>
	</div>
</div>
