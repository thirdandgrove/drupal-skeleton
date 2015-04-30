<?php
/**
 *  @file
 *  Logic to import taxonomy terms.
 */

/**
 *  Import terms from a yaml file. Note that the vocabularies should already
 *  exist.
 */
function tag_import_terms($filepath)  {
  require_once('../releases/utils/helpers/Spyc.php');
  $terms = Spyc::YAMLLoad($filepath);
  foreach ($terms['vocabularies'] as $vocabulary_machine_name => $terms) {
    $vocabulary = taxonomy_vocabulary_machine_name_load($vocabulary_machine_name);
    tag_import_terms_recurse($vocabulary, $terms);
  }
}

/**
 * Helper function to recursively create terms.
 */
function tag_import_terms_recurse($vocabulary, $terms, $parent_term = NULL) {
  foreach ($terms as $maybe_term_name => $maybe_term) {
    if (is_array($maybe_term)) {
      $term = new stdClass();
      $term->name = $maybe_term_name;
      $term->vid = $vocabulary->vid;
      if (isset($parent_term)) {
        $term->parent = $parent_term->tid;
      }
      // We either have fields on this term or children terms. We clip off the
      // fields and attach them to $term so that $maybe_term is empty or just
      // has the children terms.
      foreach ($maybe_term as $maybe_field_name => $maybe_field) {
        if (is_numeric($maybe_field_name)) {
          continue;
        }

        if (substr($maybe_field_name, 0, 6) != 'field_') {
          continue;
        }

        $term->{$maybe_field_name}[LANGUAGE_NONE] = $maybe_field;
        unset($maybe_term[$maybe_field_name]);
      }
      taxonomy_term_save($term);

      if (!empty($maybe_term)) {
        tag_import_terms_recurse($vocabulary, $maybe_term, $term);
      }
    }
    else {
      $term = new stdClass();
      $term->name = $maybe_term;
      $term->vid = $vocabulary->vid;
      if (isset($parent_term)) {
        $term->parent = $parent_term->tid;
      }
      taxonomy_term_save($term);
    }
  }
}
