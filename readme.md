# AC Geo Redirect

This is a plugin to make geo redirect happen when visiting a site and a site in your country exists. **You MUST have MultilingualPress v.3 insalled for this plugin to work**.

You'll also need to make sure the the correct headers are being sent. These will either be: `x-geoip-country` for NGINX or `cf-ipcountry` for Cloudflare.

**You must ask Toni to configure the headers for the site in order for this plugin to work at all!!!** 

## Testing

You can mock a country code by adding the header `http_x_ac_debug_country_code` and a language code, eg `us` or `fr`.

Chrome extension -> https://chrome.google.com/webstore/detail/modheader/idgpnmonknjnojddfkpgkljpfnnfcklj?hl=en

## Hooks & Filters

Most of the hooks can be found in the Redirect class. It's pretty much possible to override all of the settings and/or data that figures out which site to redirect to etc. 

### How to

1. Install Multilingualpress 3.
2. Install this plugin
3. The settings for the popup are found under Settings->Ac Geo Redirect
4. :heart: that's it

**OBS!** Ac Geo Redirect plugin is dependent on Multilingualpress 3 which is not currently available. Ask Samuel for help in acquiring a copy! 

### Tests

1. SSH in to your VM
2. Check that you have SVN installed here: run `yum install subversion` if you are unsure (this will not install it if it's already installed)
3. From this plugin directory *in your VM* run `./tests/bin/install-wp-tests.sh {test_database_name} root angrycreative4711` to install the WordPress test suite and database. **OBS!** Everything in this database will be deleted every time the tests are run, so use a database that you won't need afterwards!!
4. Run `phpunit`
5. Write some more tests!
