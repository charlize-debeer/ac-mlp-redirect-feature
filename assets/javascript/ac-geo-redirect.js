/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "/";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 0);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./src/app.js":
/*!********************!*\
  !*** ./src/app.js ***!
  \********************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _countries__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./countries */ "./src/countries.js");
/* global localStorage:Object, AcGeoRedirect:Object  */

/**
 * @param {string} countryCode the shortened country code, eg 'us'
 * @return {null|string} The country name
 */

var getCountryNameFromCode = function getCountryNameFromCode(countryCode) {
  if (!countryCode) {
    return null;
  }

  return _countries__WEBPACK_IMPORTED_MODULE_0__["default"][countryCode] ? _countries__WEBPACK_IMPORTED_MODULE_0__["default"][countryCode] : null;
};
/**
 * Check if local storage is available.
 *
 * @return {Boolean} whether or not
 */


function acGeoRedirectLocalstorageExists() {
  var als = 'angryls';

  try {
    localStorage.setItem(als, als);
    localStorage.removeItem(als);
    return true;
  } catch (e) {
    return false;
  }
}

function getDefaultBlogData() {
  var _AcGeoRedirect = AcGeoRedirect,
      siteMap = _AcGeoRedirect.siteMap,
      defaultSiteId = _AcGeoRedirect.defaultSiteId,
      defaultLocale = _AcGeoRedirect.defaultLocale;

  if (defaultSiteId) {
    var site = Object.values(siteMap).find(function (_ref) {
      var id = _ref.id;
      return parseInt(id) === parseInt(defaultSiteId);
    });

    if (site) {
      return site;
    }
  }

  return siteMap[defaultLocale] ? siteMap[defaultLocale] : null;
}

jQuery(function ($) {
  /** @type {string} The local storage key */
  var localStorageKey = 'ac_geo_visited';
  var _AcGeoRedirect2 = AcGeoRedirect,
      redirectLocale = _AcGeoRedirect2.redirectLocale,
      currentBlogData = _AcGeoRedirect2.currentBlogData,
      siteMap = _AcGeoRedirect2.siteMap,
      defaultT10ns = _AcGeoRedirect2.defaultT10ns;

  if (!acGeoRedirectLocalstorageExists()) {
    return;
  }
  /** Add a flag to empty cookies, so that the client can test this */


  if (window.location.search.indexOf('empty_cookies') > -1) {
    localStorage.removeItem(localStorageKey);
  }
  /** If the user has been redicred here, set the cookie and bail */


  if (window.location.search.indexOf('no_geo_redirect') > -1) {
    acGeoRedirectSetCookie();
    return;
  }

  if (acGeoRedirectHasCookie()) {
    return;
  }

  if (!redirectLocale) {
    console.warn('AcGeoRedirect.redirectLocale not set. (See class-redirect.php).', 'Did you add the debug header (http_x_ac_debug_country_code) or are the headers setup correctly for NGINX or cloudflare?', 'See the readme for more info');
    return;
  }

  var redirectBlogData = null;

  if (redirectLocale !== currentBlogData.countryCode) {
    var $popup = $('#ac-geo-popup').hide();
    var $body = $('body');
    var foundDomain = false;

    if (siteMap.hasOwnProperty(redirectLocale)) {
      foundDomain = true;
      redirectBlogData = siteMap[redirectLocale];
    } else {
      redirectBlogData = getDefaultBlogData();
    }

    if (!redirectBlogData) {
      return;
    }

    var region;

    if (foundDomain) {
      region = redirectBlogData.region;
    } else {
      region = getCountryNameFromCode(redirectLocale);
      redirectBlogData.t10ns = defaultT10ns;
    }

    if ('United States' === region) {
      region = 'the ' + region;
    }

    $('.ac-geo-popup-header').html("".concat(redirectBlogData.t10ns.header, " ").concat(region));
    $('.ac-geo-popup-redirect-to.redirect-to').text("".concat(redirectBlogData.t10ns.takeMeTo, " ").concat(redirectBlogData.domain));
    $('.ac-geo-popup-redirect-to.remain-on').text("".concat(redirectBlogData.t10ns.remainOn, " ").concat(currentBlogData.domain));
    $('.ac-geo-popup-redirect-link').attr({
      href: "".concat(redirectBlogData.url, "?no_geo_redirect=1")
    }).data('locale', redirectLocale);
    $body.addClass('ac-geo-popup-active');
    $popup.fadeIn(400);
    $('.ac-geo-popup-remain-link').data('locale', currentBlogData.countryCode).on('click', function (e) {
      e.preventDefault();
      acGeoRedirectSetCookie();
      $popup.fadeOut(200, function () {
        $body.removeClass('ac-geo-popup-active');
      });
    });
  }
  /**
   * Set the local storage flag
   */


  function acGeoRedirectSetCookie() {
    localStorage.setItem(localStorageKey, '1');
  }
  /**
   * Check whether or not the local storage key exists
   *
   * @return {boolean} True if we haz cookies!
   */


  function acGeoRedirectHasCookie() {
    return localStorage.getItem(localStorageKey) === '1';
  }
});

/***/ }),

/***/ "./src/countries.js":
/*!**************************!*\
  !*** ./src/countries.js ***!
  \**************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = ({
  bd: 'Bangladesh',
  be: 'Belgium',
  bf: 'Burkina Faso',
  bg: 'Bulgaria',
  ba: 'Bosnia and Herzegovina',
  bb: 'Barbados',
  wf: 'Wallis and Futuna',
  bl: 'Saint Barthelemy',
  bm: 'Bermuda',
  bn: 'Brunei',
  bo: 'Bolivia',
  bh: 'Bahrain',
  bi: 'Burundi',
  bj: 'Benin',
  bt: 'Bhutan',
  jm: 'Jamaica',
  bv: 'Bouvet Island',
  bw: 'Botswana',
  ws: 'Samoa',
  bq: 'Bonaire, Saint Eustatius and Saba ',
  br: 'Brazil',
  bs: 'Bahamas',
  je: 'Jersey',
  by: 'Belarus',
  bz: 'Belize',
  ru: 'Russia',
  rw: 'Rwanda',
  rs: 'Serbia',
  tl: 'East Timor',
  re: 'Reunion',
  tm: 'Turkmenistan',
  tj: 'Tajikistan',
  ro: 'Romania',
  tk: 'Tokelau',
  gw: 'Guinea-Bissau',
  gu: 'Guam',
  gt: 'Guatemala',
  gs: 'South Georgia and the South Sandwich Islands',
  gr: 'Greece',
  gq: 'Equatorial Guinea',
  gp: 'Guadeloupe',
  jp: 'Japan',
  gy: 'Guyana',
  gg: 'Guernsey',
  gf: 'French Guiana',
  ge: 'Georgia',
  gd: 'Grenada',
  gb: 'United Kingdom',
  ga: 'Gabon',
  sv: 'El Salvador',
  gn: 'Guinea',
  gm: 'Gambia',
  gl: 'Greenland',
  gi: 'Gibraltar',
  gh: 'Ghana',
  om: 'Oman',
  tn: 'Tunisia',
  jo: 'Jordan',
  hr: 'Croatia',
  ht: 'Haiti',
  hu: 'Hungary',
  hk: 'Hong Kong',
  hn: 'Honduras',
  hm: 'Heard Island and McDonald Islands',
  ve: 'Venezuela',
  pr: 'Puerto Rico',
  ps: 'Palestinian Territory',
  pw: 'Palau',
  pt: 'Portugal',
  sj: 'Svalbard and Jan Mayen',
  py: 'Paraguay',
  iq: 'Iraq',
  pa: 'Panama',
  pf: 'French Polynesia',
  pg: 'Papua New Guinea',
  pe: 'Peru',
  pk: 'Pakistan',
  ph: 'Philippines',
  pn: 'Pitcairn',
  pl: 'Poland',
  pm: 'Saint Pierre and Miquelon',
  zm: 'Zambia',
  eh: 'Western Sahara',
  ee: 'Estonia',
  eg: 'Egypt',
  za: 'South Africa',
  ec: 'Ecuador',
  it: 'Italy',
  vn: 'Vietnam',
  sb: 'Solomon Islands',
  et: 'Ethiopia',
  so: 'Somalia',
  zw: 'Zimbabwe',
  sa: 'Saudi Arabia',
  es: 'Spain',
  er: 'Eritrea',
  me: 'Montenegro',
  md: 'Moldova',
  mg: 'Madagascar',
  mf: 'Saint Martin',
  ma: 'Morocco',
  mc: 'Monaco',
  uz: 'Uzbekistan',
  mm: 'Myanmar',
  ml: 'Mali',
  mo: 'Macao',
  mn: 'Mongolia',
  mh: 'Marshall Islands',
  mk: 'Macedonia',
  mu: 'Mauritius',
  mt: 'Malta',
  mw: 'Malawi',
  mv: 'Maldives',
  mq: 'Martinique',
  mp: 'Northern Mariana Islands',
  ms: 'Montserrat',
  mr: 'Mauritania',
  im: 'Isle of Man',
  ug: 'Uganda',
  tz: 'Tanzania',
  my: 'Malaysia',
  mx: 'Mexico',
  il: 'Israel',
  fr: 'France',
  io: 'British Indian Ocean Territory',
  sh: 'Saint Helena',
  fi: 'Finland',
  fj: 'Fiji',
  fk: 'Falkland Islands',
  fm: 'Micronesia',
  fo: 'Faroe Islands',
  ni: 'Nicaragua',
  nl: 'Netherlands',
  no: 'Norway',
  na: 'Namibia',
  vu: 'Vanuatu',
  nc: 'New Caledonia',
  ne: 'Niger',
  nf: 'Norfolk Island',
  ng: 'Nigeria',
  nz: 'New Zealand',
  np: 'Nepal',
  nr: 'Nauru',
  nu: 'Niue',
  ck: 'Cook Islands',
  xk: 'Kosovo',
  ci: 'Ivory Coast',
  ch: 'Switzerland',
  co: 'Colombia',
  cn: 'China',
  cm: 'Cameroon',
  cl: 'Chile',
  cc: 'Cocos Islands',
  ca: 'Canada',
  cg: 'Republic of the Congo',
  cf: 'Central African Republic',
  cd: 'Democratic Republic of the Congo',
  cz: 'Czech Republic',
  cy: 'Cyprus',
  cx: 'Christmas Island',
  cr: 'Costa Rica',
  cw: 'Curacao',
  cv: 'Cape Verde',
  cu: 'Cuba',
  sz: 'Swaziland',
  sy: 'Syria',
  sx: 'Sint Maarten',
  kg: 'Kyrgyzstan',
  ke: 'Kenya',
  ss: 'South Sudan',
  sr: 'Suriname',
  ki: 'Kiribati',
  kh: 'Cambodia',
  kn: 'Saint Kitts and Nevis',
  km: 'Comoros',
  st: 'Sao Tome and Principe',
  sk: 'Slovakia',
  kr: 'South Korea',
  si: 'Slovenia',
  kp: 'North Korea',
  kw: 'Kuwait',
  sn: 'Senegal',
  sm: 'San Marino',
  sl: 'Sierra Leone',
  sc: 'Seychelles',
  kz: 'Kazakhstan',
  ky: 'Cayman Islands',
  sg: 'Singapore',
  se: 'Sweden',
  sd: 'Sudan',
  "do": 'Dominican Republic',
  dm: 'Dominica',
  dj: 'Djibouti',
  dk: 'Denmark',
  vg: 'British Virgin Islands',
  de: 'Germany',
  ye: 'Yemen',
  dz: 'Algeria',
  us: 'United States',
  uy: 'Uruguay',
  yt: 'Mayotte',
  um: 'United States Minor Outlying Islands',
  lb: 'Lebanon',
  lc: 'Saint Lucia',
  la: 'Laos',
  tv: 'Tuvalu',
  tw: 'Taiwan',
  tt: 'Trinidad and Tobago',
  tr: 'Turkey',
  lk: 'Sri Lanka',
  li: 'Liechtenstein',
  lv: 'Latvia',
  to: 'Tonga',
  lt: 'Lithuania',
  lu: 'Luxembourg',
  lr: 'Liberia',
  ls: 'Lesotho',
  th: 'Thailand',
  tf: 'French Southern Territories',
  tg: 'Togo',
  td: 'Chad',
  tc: 'Turks and Caicos Islands',
  ly: 'Libya',
  va: 'Vatican',
  vc: 'Saint Vincent and the Grenadines',
  ae: 'United Arab Emirates',
  ad: 'Andorra',
  ag: 'Antigua and Barbuda',
  af: 'Afghanistan',
  ai: 'Anguilla',
  vi: 'U.S. Virgin Islands',
  is: 'Iceland',
  ir: 'Iran',
  am: 'Armenia',
  al: 'Albania',
  ao: 'Angola',
  aq: 'Antarctica',
  as: 'American Samoa',
  ar: 'Argentina',
  au: 'Australia',
  at: 'Austria',
  aw: 'Aruba',
  "in": 'India',
  ax: 'Aland Islands',
  az: 'Azerbaijan',
  ie: 'Ireland',
  id: 'Indonesia',
  ua: 'Ukraine',
  qa: 'Qatar',
  mz: 'Mozambique'
});

/***/ }),

/***/ 0:
/*!**************************!*\
  !*** multi ./src/app.js ***!
  \**************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! /Users/richardsweeney/Projects/www/qala/site/public/wp-content/plugins/ac-geo-redirect/src/app.js */"./src/app.js");


/***/ })

/******/ });