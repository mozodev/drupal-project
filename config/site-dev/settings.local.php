<?php

// phpcs:ignoreFile

/**
 * @file
 * Drupal site-specific cstom configuration file.
 */

if (!defined('DRUPAL_ENV')) {
  define('DRUPAL_ENV', getenv('DRUPAL_ENV', 'dev'));
}

/**
 * 시스템 $settings.
 */
$settings_keys = ['trusted_host_patterns'];
foreach ($settings_keys as $key) {
  if (!empty(getenv('DRUPAL_' . strtoupper($key)))) {
    $settings[$key] = ($key == 'trusted_host_patterns') ? 
      explode('|', $settings['trusted_host_patterns']) : getenv('DRUPAL_' . strtoupper($key));
  }
}

/**
 * DB 설정 $databases.
 */
unset($databases['default']['default']['namespace']);
$databases_keys = ['database', 'host', 'port', 'driver', 'username', 'password', 'prefix', 'collation'];
foreach ($databases_keys as $key) {
  if (!empty(getenv('DRUPAL_DB_' . strtoupper($key), 0))) {
    $databases['default']['default'][$key] = $value;
  }
}

/**
 * 개발 설정.
 */
if (DRUPAL_ENV == 'dev') {
  $settings['container_yamls'][] = '../config/site-dev/theme-dev.services.yml';
  $config['system.logging']['error_level'] = 'verbose';
  $settings['skip_permissions_hardening'] = TRUE;
  $settings['config_exclude_modules'] = ['devel', 'stage_file_proxy'];
  if (class_exists('Kint')) \Kint::$max_depth = 4;
  /**
   * Cache 관련 설정.
   */
  $config['system.performance']['css']['preprocess'] = FALSE;
  $config['system.performance']['js']['preprocess'] = FALSE;
  $settings['cache']['bins']['render'] = 'cache.backend.null';
  $settings['cache']['bins']['page'] = 'cache.backend.null';
  $settings['cache']['bins']['dynamic_page_cache'] = 'cache.backend.null';
}
