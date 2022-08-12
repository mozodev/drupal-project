<?php
// phpcs:ignoreFile

/**
 * @file
 * Simple action script for example.
 */

$drupal_env = getenv('DRUPAL_ENV', '');
if ($drupal_env) {
  $config = \Drupal::service('config.factory')->getEditable('ppurio.settings');
  $config->set('ppurio_api_host', 'https://dev-api.bizppurio.com')->save();
  $this->output->writeln('[' . $drupal_env . '] ppurio.settings.ppurio_api_host => ' . $config->get('ppurio_api_host'));
}
else {
  $this->output->writeln('DRUPAL_ENV is not set.');
}
