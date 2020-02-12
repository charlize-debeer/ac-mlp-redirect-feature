const mix = require( 'laravel-mix' );

mix.js( 'src/javascript/app.js', 'assets/javascript/ac-geo-redirect.js' );
mix.sass( 'src/scss/app.scss', 'assets/css/ac-geo-redirect.css' );
