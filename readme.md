# AC Geo Redirect

This is a plugin to make geo redirect happen when visiting a site and a site in your country exists

## How to

1. Install Multilingualpress 3.
2. Install Ac Geo Redirect
3. The settings for the popup is found under Settings->Ac Geo Redirect
4. :heart: that's it

**OBS!** Ac Geo Redirect plugin is dependent on Multilingualpress 3.

### Tests

1. SSH in to your VM
2. Check that you have SVN installed here: run `yum install subversion` if you are unsure (this will not install it if it's already installed)
3. From this plugin directory *in your VM* run `./tests/bin/install-wp-tests.sh {test_database_name} root angrycreative4711` to install the WordPress test suite and database. **OBS!** Everything in this database will be deleted every time the tests are run, so use a database that you won't need afterwards!!
4. Run `phpunit`
5. Write some more tests!
