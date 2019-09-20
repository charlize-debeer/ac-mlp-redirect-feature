<?php
/**
 * Created by PhpStorm.
 * User: richardsweeney
 * Date: 2017-05-26
 * Time: 13:01
 *
 * @package ideal-geo-redirect
 */

?>

<div id="ac-geo-popup" class="ac-geo-popup">
	<div class="ac-geo-popup-inner">
		<div class="ac-geo-popup-inner-text">
			<section>

				<header>
					<img class="ac-geo-popup-icon" src="<?php echo esc_url( apply_filters( 'ac_geo_redirect_icon', \Ac_Geo_Redirect\Plugin::get_instance()->get_url() . '/assets/images/map.svg' ) ); ?>" alt="Map Icon">

					<h3 class="ac-geo-popup-header"></h3>
				</header>

				<div class="ac-geo-popup-redirect">
					<a href="#" class="ac-geo-popup-redirect-link">
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
