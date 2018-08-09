jQuery( function ( $ ) {

	/** Add a flag to empty cookies, so that the client can test this */
	if ( window.location.search.indexOf( 'empty_cookies' ) > -1 || window.location.search.indexOf( 'faux_country_code' ) > -1 ) {
		console.log( 'Clearing cookies!' );
		document.cookie.split( ';' ).forEach( function ( c ) {
			document.cookie = c.replace( /^ +/, '' ).replace( /=.*/, '=;expires=' + new Date().toUTCString() + ';path=/' );
		} );
	}

	if ( document.cookie.indexOf( 'visited=' ) > -1 ) {
		console.log( 'Cookie is already set.' );

	}
	else if ( window.location.search.indexOf( 'no_geo_redirect' ) > -1 ) {
		// console.log( 'Cookie has now been set due to query string' );
		AcGeoRedirectSetCookie();

	}
	else {
		//get locale variable
		var locale = AcGeoRedirectLocale.toLowerCase();
		// var locale = 'eu';
		var redirectBlogData = {};
		console.log(AcGeoRedirect)
		if ( locale !== AcGeoRedirect.currentBlogData.countryCode && !locale.includes( 'esi:include' ) && !locale.includes( 'null' ) ) {

			if ( AcGeoRedirect.siteMap.hasOwnProperty( locale ) ) {
				redirectBlogData = AcGeoRedirect.siteMap[ locale ];
			}
			else {
				redirectBlogData = AcGeoRedirect.siteMap[ 'eu' ];
			}

			var redirectURL = '';
			if ( AcGeoRedirect.sku ) {
				redirectURL = redirectBlogData.url + '/sku/' + AcGeoRedirect.sku + AcGeoRedirect.query_string;
			}
			else {
				redirectURL = redirectBlogData.url + AcGeoRedirect.query_string;
			}

			var $popup = $( '#ac-geo-popup' ).hide(),
				$body = $( 'body' );

			$body.addClass( 'ac-geo-popup-active' );

			$( '.ac-geo-popup-header' ).html( redirectBlogData.t10ns.header + ' ' + redirectBlogData.region + '?' );
			$( '.ac-geo-popup-sub-header' ).html( redirectBlogData.t10ns.subHeader );

			$( '.ac-geo-popup-redirect-flag.redirect-flag' ).append( $( '<img />', {
				src: redirectBlogData.flag,
			} ) );
			$( '.ac-geo-popup-redirect-to.redirect-to' ).text( redirectBlogData.t10ns.takeMeTo + ' ' + redirectBlogData.domain );
			$( '.ac-geo-popup-redirect-link' ).attr( { href: redirectURL } ).data( 'locale', locale );

			$( '.ac-geo-popup-redirect-flag.remain-flag' ).append( $( '<img />', {
				src: AcGeoRedirect.currentBlogData.flag,
			} ) );
			$( '.ac-geo-popup-redirect-to.remain-on' ).text( redirectBlogData.t10ns.remainOn + ' ' + AcGeoRedirect.currentBlogData.domain );
			$( '.ac-geo-popup-remain-link' ).data( 'locale', AcGeoRedirect.currentBlogData.countryCode );

			$popup.fadeIn( 400 );

			$( '.ac-geo-popup-remain-link' ).on( 'click', function ( e ) {
				// console.log( 'Remaining on site, setting cookie & closing popup.' );
				e.preventDefault();
				AcGeoRedirectSetCookie();

				$popup.fadeOut( 200, function () {
					$body.removeClass( 'ac-geo-popup-active' );
				} );

			} );

		}
		else {

			// Set the cookie if we don't need to redirect to avoid subsequent AJAX calls.
			AcGeoRedirectSetCookie();
		}

	}

	function AcGeoRedirectSetCookie () {
		document.cookie = 'visited=yes';
	}

} );
