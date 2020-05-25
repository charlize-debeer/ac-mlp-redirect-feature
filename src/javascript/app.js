/* global localStorage:Object, AcGeoRedirect:Object  */

import 'promise-polyfill/src/polyfill';
import { getCountryNameFromCode, localStorageExists, getCountryCode, getDefaultBlogData } from './helpers';

jQuery( ( $ ) => {
	/** @type {string} The local storage key */
	const localStorageKey = 'ac_geo_visited';

	/**
	 * Set the local storage flag
	 */
	function acGeoRedirectSetCookie() {
		localStorage.setItem( localStorageKey, '1' );
	}

	/**
	 * Check whether or not the local storage key exists
	 *
	 * @return {boolean} True if we can haz cookies!
	 */
	function acGeoRedirectHasCookie() {
		return localStorage.getItem( localStorageKey ) === '1';
	}

	/**
	 * Show the popup where appropriate.
	 *
	 * @return {Promise<void>} the promise from getCountryCode
	 */
	async function showPopup() {
		const { currentBlogData, siteMap, defaultT10ns, APIURL } = AcGeoRedirect;
		const redirectLocale = await getCountryCode( APIURL );

		if ( ! redirectLocale ) {
			console.error(
				'AcGeoRedirect.redirectLocale not set. (See Plugin.php).',
				'Did you add the debug header (x-ac-debug-country-code) or are the headers setup correctly for NGINX or cloudflare?',
				'See the readme for more info'
			);

			return;
		}

		let redirectBlogData = null;

		if ( redirectLocale === currentBlogData.countryCode ) {
			return;
		}

		let foundDomain = false;

		if ( siteMap.hasOwnProperty( redirectLocale ) ) {
			foundDomain = true;
			redirectBlogData = siteMap[ redirectLocale ];
		} else {
			redirectBlogData = getDefaultBlogData( AcGeoRedirect );
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

		if ( ! region ) {
			console.warn( `Unable to find country name for country code: ${ redirectLocale }` );

			return;
		}

		if ( 'United States' === region || 'United Kingdom' === region ) {
			region = `the ${ region }`;
		}

		render( {
			redirectBlogData,
			currentBlogData,
			region,
		} );
	}

	/**
	 * Render the Popup with the correct content.
	 *
	 * @param { Object } Text strings.
	 */
	function render( { redirectBlogData, currentBlogData, region = '' } ) {
		const $popup = $( '#ac-geo-popup' );
		const $body = $( 'body' );

		$( '.ac-geo-popup-header' ).html( `${ redirectBlogData.t10ns.header } ${ region }` );

		$( '.ac-geo-popup-redirect-to.redirect-to' ).text( `${ redirectBlogData.t10ns.takeMeTo } ${ redirectBlogData.domain }` );
		$( '.ac-geo-popup-redirect-to.remain-on' ).text( `${ redirectBlogData.t10ns.remainOn } ${ currentBlogData.domain }` );

		$( '.ac-geo-popup-redirect-link' ).attr( 'href', `${ redirectBlogData.url }?no_geo_redirect=1` );

		$body.addClass( 'ac-geo-popup-active' );
		$popup.fadeIn( 300 );

		$( '.ac-geo-popup-remain-link' )
			.on( 'click', function( e ) {
				e.preventDefault();
				acGeoRedirectSetCookie();

				$popup.fadeOut( 200, function() {
					$body.removeClass( 'ac-geo-popup-active' );
				} );
			} );
	}

	/**
	 * Show the popup where applicable.
	 */
	function init() {
		if ( ! localStorageExists() ) {
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

		showPopup();
	}

	init();
} );
