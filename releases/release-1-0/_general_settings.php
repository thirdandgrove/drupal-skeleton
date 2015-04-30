<?php

// Site settings.
variable_set('site_frontpage', 'front');
variable_set('site_slogan', 'Slogan here');
variable_set('site_name', 'Client name');
variable_set('site_mail', 'no-reply@example.com');
variable_set('date_default_timezone', 'America/New_York');
variable_set('date_first_day', '1');

// Theme configuration.
variable_set('theme_default', 'project2015');
variable_set('theme_project2015_settings', array (
  'toggle_logo' => 1,
  'toggle_name' => 1,
  'toggle_slogan' => 0,
  'toggle_node_user_picture' => 0,
  'toggle_comment_user_picture' => 0,
  'toggle_comment_user_verification' => 0,
  'toggle_favicon' => 0,
  'toggle_main_menu' => 1,
  'toggle_secondary_menu' => 1,
  'default_logo' => 1,
  'logo_path' => '',
  'logo_upload' => '',
  'default_favicon' => 1,
  'favicon_path' => '',
  'favicon_upload' => '',
));
db_query('UPDATE block SET status = 0 WHERE delta=:delta', array(':delta' => 'powered-by'));
db_query('UPDATE block SET status = 0 WHERE module=:module AND delta=:delta', array(':module' => 'user', ':delta' => 'login'));

// Some good Drupal defaults.
variable_set('cron_safe_threshold', 0);
variable_set('block_cache', 1);
variable_set('error_level', 0);
variable_set('preprocess_js', 1);
variable_set('preprocess_css', 1);
variable_set('page_compression', 1);
variable_set('cache_lifetime', 0);
variable_set('page_cache_maximum_age', 10800);
variable_set('cache', 1);
variable_set('features_default_export_path', 'sites/all/modules/custom');

drush_log(dt('General settings set'), 'ok');
