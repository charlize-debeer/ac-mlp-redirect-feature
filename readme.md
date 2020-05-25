# AC Geo Redirect

This is a plugin to make geo redirect happen when visiting a site and a site in your country exists. **You MUST have MultilingualPress v.3 insalled for this plugin to work**.

You'll also need to make sure the the correct headers are being sent. These will either be: `x-country-code` for NGINX or `cf-ipcountry` for Cloudflare.

**You must ask Toni to configure the headers for the site in order for this plugin to work at all! The above mentioned headers will only work on production, since staging environment doesn't have any location unfortunately.**

## Changelog

### 2.0.3

#### Bugfixes

* Fixed incorrectly named function `strip_protocoll` to `strip_protocol`.
* Changed debug request header to one that works with HTTP protocol. It's now `x-ac-debug-country-code`.
* Fixed settings page and the REST endpoint to actually take into account the debug header if it's set.

### 2.0.0

#### Bugfixes

Fix for "Hi, it seems like you're in the United States" for visitors from countries where a language does not exist on the network.

#### New

Total rewrite with ES6, Scss support AND composer autoloader! :tada:

Now compatible with full-page cache!

Use MLP's `hreflang: X-default` setting to grab a default locale for the current site (blog).

New `ac_geo_redirect_default_t10n_locale` filter to define the default translations to use if the country code is not found on the network.

### Testing

You can mock a country code by adding the header `x-country-code` and a country code, eg `us` or `fr`.
On staging, test or live environment you need to use the debug code `x-ac-debug-country-code`.

Chrome extension -> https://chrome.google.com/webstore/detail/modheader/idgpnmonknjnojddfkpgkljpfnnfcklj?hl=en

### Hooks & Filters

Most of the hooks can be found in the Redirect class. It's pretty much possible to override all of the settings and/or data that figures out which site to redirect to etc.

### Translations

If the visitor is shown the prompt it will mean that she is (we assume) not in the country of the current site. She should therefore not be show a message in the locale of the current site.

For example: If I visit angrycreative.se from the UK, I should see a popup suggesing I might wish to visit angrycreative.co.uk. If angrycreative.se is in swedish then the message I get (which assumes that I reside in the UK) should be in the en_UK locale.

This is why we cannot handle t10ns as we normally would in WordPress. The locale for the current site will not match (again, we assume) the locale for the visitor who should see the popup.

If you need to change the t10ns, which are intentionally kept short, the `ac_geo_redirect_t10ns` in the `T10ns` class is available for this purpose.`

There are 3 t10ns to change and where a t10n is not found we revert always to the `en_US` translation.

```
'en_US' => [
	'header'   => "Hi! It seems like you're in",
	'takeMeTo' => 'Go to',
	'remainOn' => 'Stay at',
],
'sv_SE' => [
	'header'   => 'Hej! Vi tror att du befinner dig i',
	'takeMeTo' => 'Gå till',
	'remainOn' => 'Stanna på',
],
```

#### How to

1. Install Multilingualpress 3.
2. Install this plugin
3. The settings for the popup are found under Settings->Ac Geo Redirect
4. :heart: that's it

#### Unit tests

1. SSH in to your VM
2. Check that you have SVN installed here: run `yum install subversion` if you are unsure (this will not install it if it's already installed)
3. From this plugin directory *in your VM* run `./tests/bin/install-wp-tests.sh {test_database_name} root angrycreative4711` to install the WordPress test suite and database. **OBS!** Everything in this database will be deleted every time the tests are run, so use a database that you won't need afterwards!!
4. Run `phpunit`
5. Write some more tests!
