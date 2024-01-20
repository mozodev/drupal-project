# drupal-project

FROM [drupal/recommended-project](https://github.com/drupal/recommended-project)

## development

```bash
# Just use this as template to create new repo and clone it.
# Check dependencies.
$ php -v && sqlite3 -version && composer -V
# $ mysql -V | psql -V
# Install php package dependencies
$ composer install

# cp config/site-dev/env.example ./.env
# @see composer.json:33
$ composer set-env
# @see composer.json:34
# Add env-vars.conf to expose envvar
# $ composer set-env:apache2

# Load env variable to shell
# https://direnv.net/
$ sudo apt install -y direnv
$ echo 'dotenv' > ./.envrc && direnv allow

# Run web server.
$ composer start

# [optional] Install site if needed.
$ composer site-install
# Uninstall site.
$ composer site-uninstall
```

## deploy

```bash
$ cp .env .env.stage
# add envvars for stage
$ ./scripts/project/deploy-init.sh {-:stage|prod}
```
