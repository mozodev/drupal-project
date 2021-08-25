<?php

// @codingStandardsIgnoreFile

/**
 * @file
 * Local development override configuration feature.
 *
 * To activate this feature, copy and rename it such that its path plus
 * filename is 'sites/default/settings.local.php'. Then, go to the bottom of
 * 'sites/default/settings.php' and uncomment the commented lines that mention
 * 'settings.local.php'.
 *
 * If you are using a site name in the path, such as 'sites/example.com', copy
 * this file to 'sites/example.com/settings.local.php', and uncomment the lines
 * at the bottom of 'sites/example.com/settings.php'.
 */

assert_options(ASSERT_ACTIVE, TRUE);
\Drupal\Component\Assertion\Handle::register();

/**
 * 시스템 관련 필수 설정
 */
$databases['default']['default'] =[
  'database' => '../tmp/db.sqlite3',
  'prefix' => '',
  'namespace' => 'Drupal\\Core\\Database\\Driver\\sqlite',
  'driver' => 'sqlite',
];
$settings['hash_salt'] = '8-PSx9EkGd-VjrimMPbGgnx2IPYnpllK5S1yNrUoq1yUzLaG5kz0gW2NclwB1bcK551FztJMgA';
$settings['config_sync_directory'] = '../config/sync';
$settings['trusted_host_patterns'] = [
  '^127\.0\.0\.1$',
  '^localhost$',
];

/**
 * 개발, 디버그용 설정
 */
$settings['container_yamls'][] = $app_root . '/../config/site-dev/theme-dev.services.yml';
$config['system.logging']['error_level'] = 'verbose';
$settings['skip_permissions_hardening'] = TRUE;
$settings['config_exclude_modules'] = ['devel', 'stage_file_proxy'];
$settings['DRUPAL_DEBUG'] = getenv('DRUPAL_DEBUG', 1);
// Change kint max_depth setting.
if (class_exists('Kint')) {
  // Set the max_depth to prevent out-of-memory.
  \Kint::$max_depth = 4;
}

/**
 * Cache 관련 설정
 */
$config['system.performance']['css']['preprocess'] = FALSE;
$config['system.performance']['js']['preprocess'] = FALSE;
$settings['cache']['bins']['render'] = 'cache.backend.null';
$settings['cache']['bins']['page'] = 'cache.backend.null';
$settings['cache']['bins']['dynamic_page_cache'] = 'cache.backend.null';
