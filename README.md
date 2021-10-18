# drupal-project

FROM [drupal/recommended-project](https://github.com/drupal/recommended-project)

```bash
# Check dependencies.
$ php -v && mysql -V && sqlite3 -version && composer -V

# Composer create project.
$ composer create-project mozodev/drupal-project {{ project_code }}

# Add and load settings of drupal and drush.
$ cp config/site-dev/env.example .env
$ nano .env
$ set -a; source .env; set +a

# Install drupal.
$ drush -y si standard \
install_configure_form.date_default_country=KR \
install_configure_form.date_default_timezone=Asia/Seoul \
install_configure_form.enable_update_status_emails=NULL

# uninstall
chmod +w web/sites/default && rm -rf drive/db.* web/sites/default/settings.* web/sites/default/files/

```

