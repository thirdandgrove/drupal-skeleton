<?php
/**
 *  @file
 *  Utility functions to import image styles.
 */

/**
 *  Import image styles from yaml image style definitions.
 *  @see examples/example_image_style.yaml.
 *
 *  @param string $filepath
 *    The path to the YAML file containing the menu definitions.
 */
function tag_import_image_styles($filepath) {
  require_once('../releases/utils/helpers/Spyc.php');
  $image_styles = Spyc::YAMLLoad($filepath);
  foreach ($image_styles['styles'] as $is) {
    $style = image_style_save(array('label' => $is['label'], 'name' => $is['name']));
    $effects = $is['effects'];
    foreach ($effects as $effect) {
      $effect['upscale'] = TRUE;
      $effect['isid'] = $style['isid'];
      image_effect_save($effect);
    }
  }
}
