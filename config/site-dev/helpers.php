<?php

/**
 * @file
 * Helper functions for development.
 */

use Drupal\Component\Plugin\Exception\PluginNotFoundException;

/**
 * Debug function.
 */
function bs_debug($module, $object) {
  $prefix = '!!!!' . Drupal::request()->getClientIp() . ' ===> ';
  \Drupal::logger($module)->debug($prefix . print_r($object, TRUE));
}

/**
 * Entity storage helper.
 */
function entity_storage($entity_type) {
  try {
    $type = \Drupal::entityTypeManager()->getDefinition($entity_type);
  }
  catch (PluginNotFoundException $e) {
    bs_debug('entityStorage', $e->getMessage());
  }
  return \Drupal::entityTypeManager()->getStorage($entity_type);
}

/**
 * Get list of entities with conditions.
 */
function entity_list($entity_type, $where = [], $key = NULL) {
  $entities = [];
  $storage = entity_storage($entity_type);
  $entities = $storage->loadByProperties($where);
  if ($key) {
    $entities = array_values(array_keys($entities));
  }
  return $entities;
}

/**
 * Check if string startsWith in array.
 */
function starts_with_in_array(string $needle, array $haystack) {
  $searches = array_filter($haystack, function ($string) use ($needle) {
    return substr($needle, 0, strlen($string)) == $string;
  });
  if (!empty($searches) && count($searches) > 0) {
    return TRUE;
  }
  return FALSE;
}

/**
 * Get classname without namespace.
 */
function get_class_name($object) {
  $path = explode('\\', get_class($object));
  return array_pop($path);
}

/**
 * Camelize.
 */
function camelize($input, $separator = '_') {
  return str_replace($separator, '', ucwords($input, $separator));
}
