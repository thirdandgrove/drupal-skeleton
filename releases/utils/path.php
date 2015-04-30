<?php

/**
 * @file
 * Alias setup utility.
 */

/**
 * Save a path alias to the database.
 *
 * Clone of path_save() with less checks for exiting pids
 *
 * @param $path
 *   An associative array containing the following keys:
 *   - source: The internal system path.
 *   - alias: The URL alias.
 *   - pid: (optional) Unique path alias identifier.
 *   - language: (optional) The language of the alias.
 */
function tag_path_save($path) {
  $path += array('language' => LANGUAGE_NONE);

  drupal_write_record('url_alias', $path);
  module_invoke_all('path_insert', $path);

  // Clear internal properties.
  unset($path['original']);

  // Clear the static alias cache.
  drupal_clear_path_cache($path['source']);
}

/**
 *  Import path from yaml path definitions.
 *  @see examples/example_path.yaml.
 *
 *  @param string $filepath
 *    The path to the YAML file containing the path definitions.
 */
function tag_import_path($filepath)  {
  require_once('../releases/utils/helpers/Spyc.php');
  $paths = Spyc::YAMLLoad($filepath);
  foreach ($paths['paths'] as $path) {
    tag_path_save(array('source' => $path['original'], 'alias' => $path['new']));
  }
}
