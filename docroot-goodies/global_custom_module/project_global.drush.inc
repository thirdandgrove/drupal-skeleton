<?php

/**
 * @file
 * Custom Drush integration.
 */

/**
 * Implements hook_drush_command().
 *
 * @return
 *   An associative array describing your command(s).
 */
function project_global_drush_command() {
  return array(
    'golocal' => array(
      'description' => dt('Puts your site in local development mode.'),
    ),
    'godev' => array(
      'description' => dt('Puts your site in Acquia development mode.'),
    ),
    'gotest' => array(
      'description' => dt('Puts your site in Acquia testing mode.'),
    ),
  );
}

/**
 * Put the site in Acquia testing server mode.
 */
function drush_project_global_gotest() { }

/**
 * Put the site in Acquia development server mode.
 */
function drush_project_global_godev() {
  // We run drush golocal and then undo a few things.
  drush_project_global_golocal();
  module_disable(array('devel', 'stage_file_proxy'));
}

/**
 * Put the site in local development mode.
 */
function drush_project_global_golocal() {
  // Enable dev friendly modules.
  $modules_to_enable = array('devel', 'reroute_email', 'dblog', 'update', 'diff', 'field_ui', 'views_ui', 'stage_file_proxy');
  module_enable($modules_to_enable, TRUE);
  foreach ($modules_to_enable as $module) {
    if (module_exists($module)) {
      drush_log(dt('@module enabled', array('@module' => $module)), 'ok');
    }
    else {
      drush_log(dt('@module not enabled!', array('@module' => $module)), 'error');
    }
  }

  // Disable any production modules.
  $disable = array('syslog', 'acquia_purge');
  module_disable($disable);
  drush_log(dt('Modules disabled: @modules', array('@modules' => implode(', ', $disable))), 'ok');

  // Change analytics code.
  // Use a different tracking code for GA.
  variable_set('googleanalytics_account', '');

  // Make sure the rerouting of email is turned on.
  if (module_exists('reroute_email')) {
    variable_set('reroute_email_enable', 1);
    variable_set('reroute_email_domain_whitelist', '');
    // We set this just in case our developer doesn't have this set in
    // secret.settings.php.
    variable_set('reroute_email_to', 'support@thirdandgrove.com');
    variable_set('reroute_email_enable_message', 1);
    drush_log(dt('Email is being rerouted to !email.', array('!email' => variable_get('reroute_email_to'))), 'ok');
  } else {
    drush_log('Emails will be sent to users!', 'warning');
  }

  // Allow everyone to see devel messages like dpm().
  if (module_exists('devel')) {
    user_role_grant_permissions(1, array('access devel information'));
    user_role_grant_permissions(2, array('access devel information'));
  }

  // Set some dev-friendly settings.
  variable_set('cache', "0");
  variable_set('block_cache', "0");
  variable_set('error_level', "2");
  variable_set('preprocess_js', "0");
  variable_set('preprocess_css', "0");
  variable_set('page_compression', "0");
  drush_log('Page cache, page compression, JS optimization, and CSS optimization disabled.', 'ok');

  drupal_flush_all_caches();
  drush_log('All caches cleared.', 'ok');

  drush_log('Site ready for development!', 'ok');
}
