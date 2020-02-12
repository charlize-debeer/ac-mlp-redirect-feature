/* global localStorage:Object  */

import countries from './countries';

/**
 * @param {string} countryCode the shortened country code, eg 'us'
 * @return {null|string} The country name
 */
export function getCountryNameFromCode( countryCode ) {
	if ( ! countryCode ) {
		return null;
	}

	return countries[ countryCode ] ? countries[ countryCode ] : null;
}

/**
 * Check if local storage is available.
 *
 * @return {Boolean} whether or not
 */
export function localStorageExists() {
	const als = 'angryls';

	try {
		localStorage.setItem( als, als );
		localStorage.removeItem( als );
		return true;
	} catch ( e ) {
		return false;
	}
}


/**
 * Get the country code via AJAX to avoid caching issues.
 *
 * @return {Promise|Promise} the API reponse.
 */
export function getCountryCode( APIURL ) {
	return new Promise( ( resolve, reject ) => {
		jQuery.post( `${ APIURL }/get-country-code` )
			.then( ( { code } ) => resolve( code ) )
			.fail( ( something ) => reject( something ) );
	} );
}

export function getDefaultBlogData( { siteMap, defaultSiteId, defaultLocale } ) {
	if ( defaultSiteId ) {
		const site = Object.values( siteMap )
			.find( ( { id } ) => parseInt( id ) === parseInt( defaultSiteId ) );

		if ( site ) {
			return site;
		}
	}

	return siteMap[ defaultLocale ] ? siteMap[ defaultLocale ] : null;
}
