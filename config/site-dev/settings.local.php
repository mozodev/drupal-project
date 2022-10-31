<?php
// phpcs:ignoreFile

/**
 * @file
 * Drupal site-specific custom configuration file.
 */

/**
 * 드루팔 환경
 */
$DRUPAL_ENV = getenv('DRUPAL_ENV');

/**
 * 시스템 $settings.
 */
$settings_keys = ['trusted_host_patterns', 'hash_salt', 'config_sync_directory'];
foreach ($settings_keys as $key) {
  $value = getenv('DRUPAL_' . strtoupper($key));
  if (empty($value) || !empty($settings[$key])) {
    continue;
  }
  $settings[$key] = match ($key) {
    'trusted_host_patterns' => explode('|', $value),
    default => $value,
  };
}

/**
 * DB 설정 $databases.
 */
$databases_keys = ['database', 'host', 'port', 'driver', 'username', 'password', 'prefix', 'collation'];
foreach ($databases_keys as $key) {
  $value = getenv('DRUPAL_DB_' . strtoupper($key));
  if (!empty($value)) {
    $databases['default']['default'][$key] = $value;
  }
}

/**
 * 개발 설정.
 */
if ($DRUPAL_ENV == 'dev') {
  $config['system.logging']['error_level'] = 'verbose';
  $settings['container_yamls'][] = '../config/site-dev/theme-dev.services.yml';
  $settings['skip_permissions_hardening'] = TRUE;
  $settings['config_exclude_modules'] = [
    'devel', 'devel_generagte', 'stage_file_proxy'
  ];
  /**
   * Cache 관련 설정.
   */
  $config['system.performance']['css']['preprocess'] = FALSE;
  $config['system.performance']['js']['preprocess'] = FALSE;
  $settings['cache']['bins']['render'] = 'cache.backend.null';
  $settings['cache']['bins']['page'] = 'cache.backend.null';
  $settings['cache']['bins']['dynamic_page_cache'] = 'cache.backend.null';
}
