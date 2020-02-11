/* global localStorage:Object, AcGeoRedirect:Object  */

import countries from './countries';

/**
 * @param {string} countryCode the shortened country code, eg 'us'
 * @return {null|string} The country name
 */
const getCountryNameFromCode = ( countryCode ) => {
	if ( ! countryCode ) {
		return null;
	}

	return countries[ countryCode ] ? countries[ countryCode ] : null;
};

/**
 * Check if local storage is available.
 *
 * @return {Boolean} whether or not
 */
function acGeoRedirectLocalstorageExists() {
	const als = 'angryls';

	try {
		localStorage.setItem( als, als );
		localStorage.removeItem( als );
		return true;
	} catch ( e ) {
		return false;
	}
}

function getDefaultBlogData() {
	const { siteMap, defaultSiteId, defaultLocale } = AcGeoRedirect;

	if ( defaultSiteId ) {
		const site = Object.values( siteMap )
			.find( ( { id } ) => parseInt( id ) === parseInt( defaultSiteId ) );

		if ( site ) {
			return site;
		}
	}

	return siteMap[ defaultLocale ] ? siteMap[ defaultLocale ] : null;
}

jQuery( ( $ ) => {
	/** @type {string} The local storage key */
	const localStorageKey = 'ac_geo_visited';
	const { redirectLocale, currentBlogData, siteMap, defaultT10ns } = AcGeoRedirect;

	if ( ! acGeoRedirectLocalstorageExists() ) {
		return;
	}

	/** Add a flag to empty cookies, so that the client can test this */
	if ( window.location.search.indexOf( 'empty_cookies' ) > -1 ) {
		localStorage.removeItem( localStorageKey );
	}

	/** If the user has been redicred here, set the cookie and bail */
	if ( window.location.search.indexOf( 'no_geo_redirect' ) > -1 ) {
		acGeoRedirectSetCookie();
		return;
	}

	if ( acGeoRedirectHasCookie() ) {
		return;
	}

	if ( ! redirectLocale ) {
		console.warn(
			'AcGeoRedirect.redirectLocale not set. (See class-redirect.php).',
			'Did you add the debug header (http_x_ac_debug_country_code) or are the headers setup correctly for NGINX or cloudflare?',
			'See the readme for more info'
		);

		return;
	}

	let redirectBlogData = null;

	if ( redirectLocale !== currentBlogData.countryCode ) {
		const $popup = $( '#ac-geo-popup' ).hide();
		const $body = $( 'body' );
		let foundDomain = false;

		if ( siteMap.hasOwnProperty( redirectLocale ) ) {
			foundDomain = true;
			redirectBlogData = siteMap[ redirectLocale ];
		} else {
			redirectBlogData = getDefaultBlogData();
		}

		if ( ! redirectBlogData ) {
			return;
		}

		let region;

		if ( foundDomain ) {
			region = redirectBlogData.region;
		} else {
			region = getCountryNameFromCode( redirectLocale );
			redirectBlogData.t10ns = defaultT10ns;
		}

		if ( 'United States' === region ) {
			region = 'the ' + region;
		}

		$( '.ac-geo-popup-header' ).html( `${ redirectBlogData.t10ns.header } ${ region }` );
		$( '.ac-geo-popup-redirect-to.redirect-to' ).text( `${ redirectBlogData.t10ns.takeMeTo } ${ redirectBlogData.domain }` );
		$( '.ac-geo-popup-redirect-to.remain-on' ).text( `${ redirectBlogData.t10ns.remainOn } ${ currentBlogData.domain }` );
		$( '.ac-geo-popup-redirect-link' )
			.attr( {
				href: `${ redirectBlogData.url }?no_geo_redirect=1`,
			} )
			.data( 'locale', redirectLocale );

		$body.addClass( 'ac-geo-popup-active' );
		$popup.fadeIn( 400 );

		$( '.ac-geo-popup-remain-link' )
			.data( 'locale', currentBlogData.countryCode )
			.on( 'click', function( e ) {
				e.preventDefault();
				acGeoRedirectSetCookie();

				$popup.fadeOut( 200, function() {
					$body.removeClass( 'ac-geo-popup-active' );
				} );
			} );
	}

	/**
	 * Set the local storage flag
	 */
	function acGeoRedirectSetCookie() {
		localStorage.setItem( localStorageKey, '1' );
	}

	/**
	 * Check whether or not the local storage key exists
	 *
	 * @return {boolean} True if we haz cookies!
	 */
	function acGeoRedirectHasCookie() {
		return localStorage.getItem( localStorageKey ) === '1';
	}
} );
