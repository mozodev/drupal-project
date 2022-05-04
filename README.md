# drupal-project

FROM [drupal/recommended-project](https://github.com/drupal/recommended-project)

```bash
# Just use this as template to create new repo and clone it.
# Check dependencies.
$ php -v && sqlite3 -version && composer -V
# $ mysql -V | psql -V

# Load env vars and check install options.
$ . scripts/env.sh && env | grep DRUSH
# Install site.
$ composer site-install
# Run web server.
$ composer start
# Uninstall site.
$ composer site-uninstall
```
