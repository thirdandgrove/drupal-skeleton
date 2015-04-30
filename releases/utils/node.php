<?php
/**
 *  @file
 *  Logic to import nodes.
 */

/**
 *  Import nodes from a yaml file.
 */
function tag_import_nodes($filepath)  {
  require_once('../releases/utils/helpers/Spyc.php');
  require_once('../releases/utils/helpers/EntityImport.php');
  require_once('../releases/utils/helpers/NodeImport.php');
  $node_creator = new NodeImport($filepath);
  if ($node_creator->saveEntities()) {
    return TRUE;
  }

  return FALSE;
}
