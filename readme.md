# AC Plugin Boilerplate V2

This is a suggested form for the new plugin boilerplate. 

## How to

1. Clone this repository and `cd` into it
2. Run `composer install`
3. Run `php ./installer/installer.php plugin:create` and follow the prompts
4. :heart: that's it

**OBS!** The install script will rename the directory you cloned the project into. So don't get confused after you run the script!

### Tests

1. SSH in to your VM
2. Check that you have SVN installed here: run `yum install subversion` if you are unsure (this will not install it if it's already installed)
3. From this plugin directory *in your VM* run `./tests/bin/install-wp-tests.sh {test_database_name} root angrycreative4711` to install the WordPress test suite and database. **OBS!** Everything in this database will be deleted every time the tests are run, so use a database that you won't need afterwards!!
4. Run `phpunit`
5. Write some more tests!
