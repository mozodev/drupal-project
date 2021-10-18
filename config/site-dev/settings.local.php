<?php

/**
 * 시스템 관련 필수 설정
 */
$keys = ['hash_salt', 'config_sync_directory', 'trusted_host_patterns'];
foreach ($keys as $key) {
  if (!empty(getenv(strtoupper($key), 0))) {
    $settings[$key] = ($key == 'trusted_host_patterns') ? 
      explode('|', $settings['trusted_host_patterns']) : getenv(strtoupper($key));
  }
}

/**
 * DB 설정
 */
$databases_keys = [ 'database', 'host', 'port', 'driver', 'username', 'password', 'prefix', 'collation' ];
foreach ($databases_keys as $key) {
  if (!empty(getenv('DB_' . strtoupper($key), 0))) {
    $databases['default']['default'][$key] = $value;
  }
}
unset($databases['default']['default']['namespace']);

/**
 * 디버그용 설정
 */
$settings['DEBUG'] = getenv('DEBUG', 0);
if (!$settings['DEBUG']) {
  $settings['container_yamls'][] = '../config/site-dev/theme-dev.services.yml';
  $config['system.logging']['error_level'] = 'verbose';
  $settings['skip_permissions_hardening'] = TRUE;
  $settings['config_exclude_modules'] = ['devel', 'stage_file_proxy'];
  if (class_exists('Kint')) {
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
}
