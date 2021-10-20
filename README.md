# drupal-project

FROM [drupal/recommended-project](https://github.com/drupal/recommended-project)

```bash
# Just use this as template to create new repo and clone it.
# Check dependencies.
$ php -v && mysql -V && sqlite3 -version && composer -V

# Load env vars.
$ . scripts/env.sh
# Show install options.
$ env | grep DRUSH
# First, drush site:install and add settings.local.php.
$ composer site-install
# Start drush runserver.
$ composer start
# Uninstall site.
$ composer site-uninstall
```
