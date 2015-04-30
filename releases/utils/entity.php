<?php
/**
 *  @file
 *  Logic to import entities.
 */

/**
 *  Import a file filled with entities.
 *
 *  @param string $filepath
 *    The path to the YAML file containing the entity definitions.
 *    For an example of the format see the examples file in this folder.
 *
 *  @return boolean
 *    TRUE on success FALSE on failure.
 */
function tag_import_entities($filepath, $release_dir = '..') {
  require_once("$release_dir/releases/utils/helpers/Spyc.php");
  require_once("$release_dir/releases/utils/helpers/EntityImport.php");
  $entity_creator = new EntityImport($filepath);
  if ($entity_creator->saveEntities()) {
    return TRUE;
  }

  return FALSE;
}
