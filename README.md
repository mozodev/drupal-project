# drupal-project

FROM [drupal/recommended-project](https://github.com/drupal/recommended-project)

```bash
# Check dependencies.
$ php -v && mysql -V && sqlite3 -version && composer -V

# Composer create project.
$ composer create-project mozodev/drupal-project {{ project_code }}
```

## scripts

- ```composer start```  : run server
- ```composer add-workspace```  : add vscode workspace settings
- ```composer add-env```  : copy .env file
- ```composer load-env```  : load environment variables on terminal
- ```composer scaffold-config```  : add local setting file and load
- ```composer drupal-install```  : edit .env and install drupal
- ```composer drupal-uninstall-sqlite```  : uninstall drupal (sqlite)
- ```composer drupal-uninstall```  : uninstall drupal

## base profile

```bash
$ cd web/profile/custom/base/
$ find config/install/ -type f -exec sed -i -e '/^uuid: /d' {} \;
$ find config/install/ -type f -exec sed -i -e '/_core:/,+1d' {} \;
```