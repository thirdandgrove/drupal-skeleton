<?php

/**
 * @file
 * Main theme overrides and hooks for Skeleton Theme.
 */

/**
 * Implements hook_css_alter().
 */
function skeleton_css_alter(&$css) {

  $exclude = array(
    // Remove Drupal core css.
    'modules/node/node.css' => FALSE,
    'modules/system/admin.css' => FALSE,
    'modules/system/maintenance.css' => FALSE,
    'modules/system/system.css' => FALSE,
    'modules/system/system.admin.css' => FALSE,
    'modules/system/system.maintenance.css' => FALSE,
    'modules/system/system.messages.css' => FALSE,
    'modules/system/system.menus.css' => FALSE,
    'modules/system/system.theme.css' => FALSE,

    // Remove contrib module CSS
    drupal_get_path('module', 'views') . '/css/views.css' => FALSE,
  );

  $css = array_diff_key($css, $exclude);
}
