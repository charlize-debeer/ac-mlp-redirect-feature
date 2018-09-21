jQuery( function ( $ ) {

	/** @type {string} The local storage key */
	var localStorageKey = 'ac_geo_visited';

	// If local storage doesn't exist, bail.
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

	/** If the user has the cookie, bail */
	if ( acGeoRedirectHasCookie() ) {
		return;
	}

	var locale = AcGeoRedirect.redirectLocale;
	var redirectBlogData = null;

	if (!locale) {
		console.log('AcGeoRedirect.redirectLocale not set. (See class-redirect.php).', 'Did you add the debug header (http_x_ac_debug_country_code) or are the headers setup correctly for NGINX or cloudflare?', 'See the readme for more info');
		return;
	}

	if ( locale !== AcGeoRedirect.currentBlogData.countryCode ) {
		var $popup      = $( '#ac-geo-popup' ).hide(),
			$body       = $( 'body' ),
			$remainLink = $( '.ac-geo-popup-remain-link' );

		if ( AcGeoRedirect.siteMap.hasOwnProperty( locale ) ) {
			redirectBlogData = AcGeoRedirect.siteMap[ locale ];
		} else {
			redirectBlogData = AcGeoRedirect.siteMap[ AcGeoRedirect.defaultLocale ];
		}

		if ( ! redirectBlogData ) {
			return;
		}

		$body.addClass( 'ac-geo-popup-active' );

		let region = redirectBlogData.region;
		if ( 'United States' === region ) {
			region = 'the ' + region;
		}

		$( '.ac-geo-popup-header' ).html( redirectBlogData.t10ns.header + ' ' + region );
		$( '.ac-geo-popup-redirect-to.redirect-to' ).text( redirectBlogData.t10ns.takeMeTo + ' ' + redirectBlogData.domain );
		$( '.ac-geo-popup-redirect-link' ).attr( { href: redirectBlogData.url + '?no_geo_redirect=1' } ).data( 'locale', locale );
		$( '.ac-geo-popup-redirect-to.remain-on' ).text( redirectBlogData.t10ns.remainOn + ' ' + AcGeoRedirect.currentBlogData.domain );

		$popup.fadeIn( 400 );

		$remainLink.data( 'locale', AcGeoRedirect.currentBlogData.countryCode )
			.on( 'click', function ( e ) {
				e.preventDefault();
				acGeoRedirectSetCookie();

				$popup.fadeOut( 200, function () {
					$body.removeClass( 'ac-geo-popup-active' );
				} );
			} );
	}

	/**
	 * Check if local storage is available.
	 *
	 * @returns {boolean}
	 */
	function acGeoRedirectLocalstorageExists() {
		var als = 'angryls';
		try {
			localStorage.setItem(als, als);
			localStorage.removeItem(als);
			return true;
		} catch(e) {
			return false;
		}
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
	 * @returns {boolean}
	 */
	function acGeoRedirectHasCookie() {
		return localStorage.getItem( localStorageKey ) === '1';
	}

} );
