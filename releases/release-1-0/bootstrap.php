<?php

/**
 * @file
 * A global file containing includes for every step of the release process.
 *
 * Every time you call drush at the command line Drupal has to be booted up.
 * Putting the entire release into one file means we only have to call drush
 * php-script once, and thus boot Drupal once, saving oodles of time.
 */

$themes_to_enable = array(
  'project2015'
);
theme_enable($themes_to_enable);

// It becomes necessary to break up modules in different calls to
// module_enable() so that Drupal fully loads dependencies needed for later
// modules.
$modules_to_enable = array(
  'ctools' => array(
    'ctools'
  ),
  'primary' => array(
    'views', 'page_manager', 'panels', 'entity', 'securepages',
    'syslog', 'apachesolr', 'fieldable_panels_panes', 'navbar',
    'features', 'date', 'date_popup', 'entityreference', 'cer',
    'workflow', 'workflowfield', 'workflow_admin_ui',
    'field_permissions', 'entity_translation', 'scheduler', 'strongarm',
    'metatag', 'ckeditor', 'password_policy',
  ),
  'secondary' => array(
    'pathauto', 'filefield_paths', 'panelizer', 'entitycache', 'apachesolr_search',
    'panels_ipe', 'views_content', 'admin_views', 'apachesolr_autocomplete',
    'apachesolr_image', 'workflow_rules', 'rules_admin', 'chosen',
    'stringoverrides', 'image_alt_required', 'menu_attributes',
    'email_registration', 'exact_target_fuel', 'redirect', 'elements', 'metatag_panels',
  ),
  'features' => array(
    // Rename this to whatever you pick for the global module name.
    'project_global',
  ),
  'post-features' => array(),
  'post-permission' => array(),
);
foreach ($modules_to_enable as $key => $modules) {
  $success = module_enable($modules) ? 'success' : 'failure';
  drush_log(dt('Group @key: @success', array('@key' => $key, '@success' => $success)), 'ok');
}

$modules_to_disable = array(
  'bartik', 'overlay', 'shortcut', 'color', 'dashboard', 'rdf', 'help',
  'toolbar', 'field_ui', 'views_ui', 'dblog', 'comment',
);
module_disable($modules_to_disable);

// Cluster site functionality into logical sub files to organize all of the
// configuration settings that need to happen. Yes, you could do this in
// .install files of your custom modules but grouping it together in a separate
// place just for releases makes the code more discoverable.
require_once('_general_settings.php');
require_once('_users.php');

drupal_cron_run();
drupal_flush_all_caches();
