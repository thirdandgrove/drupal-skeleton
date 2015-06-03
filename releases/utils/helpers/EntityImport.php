<?php
/**
 *  @file
 *  Logic to import Drupal entities.
 */

/**
 *  The EntityImport Class. Mostly used to translate YAMLs into Drupal entities
 *  while preserving all the flexibility of authentic content creation.
 */
class EntityImport extends Spyc {

  /**
   *  @var array $entities.
   *    An array of entitymaps.
   */
  protected $entities;

  public function __construct($filepath) {
    $this->entities = array('entities' => array());
    // Because different environments scan and sort the file system differently
    // we need to manually sort the results of the file_scan_directory() to
    // ensure nodes are created in the same order everywhere.
    $files = file_scan_directory($filepath, '/.*\.yaml$/');
    ksort($files, SORT_STRING);
    foreach ($files as $file) {
      $incoming = $this->YAMLLoad($file->uri);
      $this->entities['entities'] = array_merge($incoming['entities'], $this->entities['entities']);
    }
    $this->fileDirectory = $filepath . '/files';
  }

  /**
   *  Loops through an array of entities and imports them.
   */
  public function saveEntities()  {
    foreach ($this->entities['entities'] as $entityMap) {
      $entity = $this->translateEntity($entityMap);
      $entity->title = $entityMap['title'];

      if (isset($entityMap['user']) && $account = user_load_by_name($entityMap['user'])) {
        $entity->uid = $account->uid;
        $entity->name = $account->name;
      }

      if (isset($entityMap['created']) && $time = strtotime($entityMap['created'])) {
        $entity->created = $time;
        $entity->updated = $time;
      }

      entity_save($entityMap['entity'], $entity);
    }
  }

  /**
   *  Translate the entity as presented in the YAML, into a Drupal entity.
   *
   *  @param array $entityMap
   *    An array of the entities values keyed by field name.
   *
   *  @return object
   *    The entity object, ready to be saved.
   */
  protected function translateEntity($entityMap) {
    // Bundle is required, we add field_name in for field collections. Entities
    // that don't need this field will ignore it.
    $entity = $this->createEntity($entityMap['entity'], $entityMap['bundle']);

    // Explicitly set the uid if it is defined.
    if (!$entity->uid) {
      if (!empty($entityMap['uid'])) {
        $entity->uid = $entityMap['uid'];
      }
      else {
        $entity->uid = 1;
      }
    }

    foreach ($entityMap['values'] as $key => $info) {
      // This is for properties like status, sticky, published etc.
      if (!is_array($info)) {
        $entity->{$key} = $info;
        continue;
      }

      switch ($info['type']) {
        case 'entity':
          $field_entity = $this->translateEntity($info);
          $field_entity->setHostEntity($entityMap['entity'], $entity);
          $field_entity->save();
          break;
        case 'file':
           $file = $this->uploadFileField($info['value']);
           $file['alt'] = isset($info['alt']) ? $info['alt'] : NULL;
           $file['title'] = isset($info['title']) ? $info['title'] : NULL;
           $entity->{$key}[LANGUAGE_NONE][] = $file;
          break;
        case 'link':
          foreach ($info['value'] as $item) {
            $entity->{$key}[LANGUAGE_NONE][] = $item;
          }
          break;
        case 'date':
          $field = field_info_field($key);
          $timezone = date_get_timezone($field['settings']['tz_handling'], $timezone);
          $timezone_db = date_get_timezone_db($field['settings']['tz_handling']);
          foreach ($info['value'] as $item) {
            $entity->{$key}[LANGUAGE_NONE][] = array(
              'value' => strtotime($item),
              'timezone' => $timezone,
              'timezone_db' => $timezone_db,
              'date_type' => 'datestamp',
            );
          }
          break;
        case 'tag':
          foreach ($info['value'] as $item) {
            $this->addTag($entity, $key, $item);
          }
          break;
        case 'entity_reference':
          foreach ($info['value'] as $item) {
            $this->addEntityReference($entity, $key, $item);
          }
          break;
        case 'term':
          foreach ($info['value'] as $item) {
            $this->addTermAssoc($entity, $key, $item);
          }
          break;
        case 'metatag':
          foreach ($info['value'] as $name => $item) {
            $entity->{$key}[LANGUAGE_NONE][$name]['value'] = $item;
          }
          break;
        case 'address':
          foreach ($info['value'] as $name => $item) {
            $entity->{$key}[LANGUAGE_NONE][0][$name] = $item;
          }
          break;
        case 'attribute':
          $entity->{$key} = $info['value'];
        case 'path':
          $entity->path = array(
            'pathauto' => 0,
            'alias' => $info['value'],
            'language' => LANGUAGE_NONE,
          );
          break;
        case 'commerce_price':
          if (gettype($info['value']) != 'array') {
            $info['value'] = array($info['value']);
          }
          foreach ($info['value'] as $value) {
            $entity->{$key}[LANGUAGE_NONE][] = array(
              'amount' => $value * 100,
              'currency_code' => 'USD',
            );
          }
          break;
        case 'interval':
          foreach ($info['value'] as $name => $item) {
            $entity->{$key}[LANGUAGE_NONE][0][$name] = $item;
          }
          break;
        case 'youtube':
          $entity->{$key}[LANGUAGE_NONE][] = array(
            'input' => 'https://www.youtube.com/watch?v=' . $info['value'],
            'video_id' => $info['value'],
          );
          break;
        default:
          if (gettype($info['value']) != 'array') {
            $info['value'] = array($info['value']);
          }

          // If a field has a default value set $entity->{$key} will already be
          // set and have a value, so doing [] will actually put your imported
          // value as the second value on the field. Since we are importing
          // this specific field it's safe to just blow away the default.
          $entity->{$key}[LANGUAGE_NONE] = array();

          $text_format = isset($info['format']) ? $info['format'] : NULL;
          foreach ($info['value'] as $value) {
            $field_info = array();
            $field_info['value'] = $value;
            if (!is_null($text_format)) {
              $field_info['format'] = $text_format;
            }
            // If this is a body field make sure we capture the summary.
            if (isset($info['summary'])) {
              $field_info['summary'] = $info['summary'];
            }
            $entity->{$key}[LANGUAGE_NONE][] = $field_info;
          }
      }
    }

    // Add support for path handling.
    if ($entityMap['entity'] == 'node' && isset($entityMap['path'])) {
      $entity->path['pathauto'] = FALSE;
    }
    // Allow version specific alterations
    if (function_exists('tag_entitybuild_alter')) {
      tag_entitybuild_alter($entity);
    }
    return $entity;
  }

  /**
   *  Creates an entity. A wrapper for entity_create. Mostly intended to provide
   *  the ability to override this function in the case of nodes.
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
    // Bundle is required, we add field_name in for field collections. Entities
    // that don't need this field will ignore it.
    // If this is a commerce_product, add type attribute which is required.
    // @see line 102 of commerce_product.controller.inc.
    $entity = entity_create($name, array('bundle' => $bundle, 'type' => $bundle, 'field_name' => $bundle));
    return $entity;
  }

  /**
   *  Upload a file.
   *
   *  @return array
   *    An array of Drupal file info.
   */
  protected function uploadFileField($fileName)  {
    $filepath = drupal_realpath($this->fileDirectory . '/' . $fileName);
    $file = new stdClass();
    $file->uid = 1;
    $file->uri = $filepath;
    $file->filemime = file_get_mimetype($filepath);
    $file->status = 1;
    $file->display = 1;
    $file = file_copy($file, 'public://');
    return (array) $file;
  }

  /**
   * Add a term association to an entity.
   *
   * If the tag doesn't exist it will be created.
   *
   *  @param object $entity
   *    The entity to add the term to, passed by reference.
   *  @param string $field_name
   *    The name of the field.
   *  @param string $term_name
   *    The name of the term.
   */
  protected function addEntityReference(&$entity, $field_name, $entity_reference_title)  {
    $field_info = field_info_field($field_name);

    // Try to find a matching entity.
    $bundles = array_values($field_info['settings']['handler_settings']['target_bundles']);
    $target_type = $field_info['settings']['target_type'];
    $query = new EntityFieldQuery();
    $query->entityCondition('entity_type', $target_type)
      ->propertyCondition('title', $entity_reference_title, '=')
      ->entityCondition('bundle', $bundles);
    $result = $query->execute();

    // No results, so nothing to attach.
    if (empty($result)) {
      return;
    }

    $last_result = array_pop($result[$target_type]);
    $target_entity_id = $last_result->id;
    $entity->{$field_name}[LANGUAGE_NONE][]['target_id'] = $target_entity_id;
  }

  /**
   * Add a term association to an entity.
   *
   * If the tag doesn't exist it will be created.
   *
   *  @param object $entity
   *    The entity to add the term to, passed by reference.
   *  @param string $field_name
   *    The name of the field.
   *  @param string $term_name
   *    The name of the term.
   */
  protected function addTag(&$entity, $field_name, $term_name)  {
    $field_info = field_info_field($field_name);
    $vocabulary_name = $field_info['settings']['allowed_values'][0]['vocabulary'];
    $term = reset(taxonomy_get_term_by_name($term_name, $vocabulary_name));
    if (empty($term)) {
      $vocabulary = taxonomy_vocabulary_machine_name_load($vocabulary_name);
      $term = new stdClass();
      $term->vid = $vocabulary->vid;
      $term->name = $term_name;
      taxonomy_term_save($term);
    }
    $entity->{$field_name}[LANGUAGE_NONE][]['tid'] = $term->tid;
  }

  /**
   *  Add a term association to an entity.
   *
   *  @param object $entity
   *    The entity to add the term to, passed by reference.
   *  @param string $field_name
   *    The name of the field.
   *  @param string $term_name
   *    The name of the term.
   */
  protected function addTermAssoc(&$entity, $field_name, $term_name)  {
    $field_info = field_info_field($field_name);
    $vocabulary_name = $field_info['settings']['allowed_values'][0]['vocabulary'];
    $term = reset(taxonomy_get_term_by_name($term_name, $vocabulary_name));
    $entity->{$field_name}[LANGUAGE_NONE][]['tid'] = $term->tid;
  }

}
