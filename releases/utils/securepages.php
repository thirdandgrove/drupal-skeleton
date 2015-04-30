<?php
/**
 *  @file
 *  Utility functions to import securepages config.
 */

/**
 *  Import securepages settings from yaml.
 *
 *  @param string $filepath
 *    The path to the yaml file containing the securepages config.
 */
function tag_import_secure_pages($filepath)  {
  require_once('../releases/utils/helpers/Spyc.php');
  $pages = Spyc::YAMLLoad($filepath);

  $securepages_pages = variable_get('securepages_pages', '');
  foreach ($pages['paths'] as $path) {
    $securepages_pages .= $path . "\n";
  }
  variable_set('securepages_pages', $securepages_pages);
}
