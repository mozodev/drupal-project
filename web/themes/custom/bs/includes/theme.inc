<?php

/**
 * @file
 * Custom theme hooks.
 */

use Drupal\Core\Form\FormStateInterface;

/**
 * Implement form_system_theme_settings_alter().
 */
function bs_form_system_theme_settings_alter(&$form, FormStateInterface $form_state, $form_id = NULL) {
  // Work-around for a core bug affecting admin themes. See issue #943212.
  if (isset($form_id)) {
    return;
  }
  $form['custom_settings']['enable_darkmode'] = [
    '#type' => 'checkbox',
    '#title' => t('Enable dark mode (BETA)'),
    '#description' => t("Enables dark mode."),
    '#default_value' => theme_get_setting('enable_darkmode'),
  ];
}

/**
 * Implements hook_preprocess_HOOK() for html.
 */
function bs_preprocess_html(&$vars) {
  // Hello darkness, my old friend.
  if (theme_get_setting('enable_darkmode')) {
    $vars['attributes']['class'][] = 'bs--dark-mode';
  }
}
