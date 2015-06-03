<?php
/**
 *  @file
 *  Logic to import nodes.
 */

/**
 *  The node import class. And extension of the entity import class that uses
 *  node_save() instead of entity_save in order to invoke node hooks.
 */
class NodeImport extends EntityImport {

  public function __construct($filepath) {
    parent::__construct($filepath);
  }

  /**
   *  Creates a node or an entity depending on the context.
   *
   *  @param string $name
   *    The name of the entity.
   *  @param string $type
   *    The type of the entity.
   *
   *  @return object
   *    The newly created entity.
   */
  protected function createEntity($name, $bundle) {
    if ($name == 'node') {
      $node = new stdClass();
      $node->type = $bundle;
      node_object_prepare($node);
      return $node;
    }

    $entity = entity_create($name, array('bundle' => $bundle, 'field_name' => $bundle));
    return $entity;
  }

  /**
   *  Loops through an array of entities and imports them.
   */
  public function saveEntities()  {
    foreach ($this->entities['entities'] as $entityMap) {
      $node = $this->translateEntity($entityMap);
      $node->title = $entityMap['title'];
      if (isset($entityMap['user']) && $account = user_load_by_name($entityMap['user'])) {
        $node->uid = $account->uid;
        $node->name = $account->name;
      }

      if (isset($entityMap['created']) && $time = strtotime($entityMap['created'])) {
        $node->created = $time;
        $node->updated = $time;
      }

      node_save($node);

      if ($entityMap['entity'] == 'node' && isset($entityMap['path'])) {
        $path = array(
          'source' => 'node/' . $node->nid,
          'alias' => $entityMap['path'],
          'language' => isset($entityMap['values']['language']['value']) ? $entityMap['values']['language']['value'] : NULL,
        );
        path_save($path);
      }
    }
  }

}
