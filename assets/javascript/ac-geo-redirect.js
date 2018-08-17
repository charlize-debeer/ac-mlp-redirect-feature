jQuery( function ( $ ) {

	// Bail if local storage doesn't exist.
	if ( ! acGeoRedirectLocalstorageExists() ) {
		return;
	}

	/** @type {string} The local storage key */
	var localStorageKey = 'ac_geo_visited';

	/** Add a flag to empty cookies, so that the client can test this */
	if ( window.location.search.indexOf( 'empty_cookies' ) > -1 || window.location.search.indexOf( 'faux_country_code' ) > -1 ) {
		localStorage.removeItem( localStorageKey );
	}

	if ( window.location.search.indexOf( 'no_geo_redirect' ) > -1 ) {
		acGeoRedirectSetCookie();
		return;
	}

	if ( acGeoRedirectHasCookie() ) {
		return;
	}


	var locale = AcGeoRedirect.redirectLocale;
	var redirectBlogData;

	if ( locale !== AcGeoRedirect.currentBlogData.countryCode && !locale.includes( 'null' ) ) {
		var $popup = $( '#ac-geo-popup' ).hide(),
			$body = $( 'body' ),
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

		$( '.ac-geo-popup-header' ).html( redirectBlogData.t10ns.header + ' ' + redirectBlogData.region + '?' );
		$( '.ac-geo-popup-sub-header' ).html( redirectBlogData.t10ns.subHeader );

		$( '.ac-geo-popup-redirect-flag.redirect-flag' ).append( $( '<img />', {
			src: redirectBlogData.flag,
		} ) );
		$( '.ac-geo-popup-redirect-to.redirect-to' ).text( redirectBlogData.t10ns.takeMeTo + ' ' + redirectBlogData.domain );
		$( '.ac-geo-popup-redirect-link' ).attr( { href: redirectBlogData.url + '?no_geo_redirect=1' } ).data( 'locale', locale );

		$( '.ac-geo-popup-redirect-flag.remain-flag' ).append( $( '<img />', {
			src: AcGeoRedirect.currentBlogData.flag,
		} ) );
		$( '.ac-geo-popup-redirect-to.remain-on' ).text( redirectBlogData.t10ns.remainOn + ' ' + AcGeoRedirect.currentBlogData.domain );
		$remainLink.data( 'locale', AcGeoRedirect.currentBlogData.countryCode );

		$popup.fadeIn( 400 );

		$remainLink.on( 'click', function ( e ) {
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
