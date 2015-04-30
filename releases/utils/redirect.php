<?php
/**
 *  @file
 *  Utility to import redirects using the redirect module.
 */

/**
 *  Import redirects from a yaml file.
 *
 *  @param string $filepath
 *    The path to the yaml file containing the redirects.
 */
function tag_import_redirects($filepath)  {
  require_once('../releases/utils/helpers/Spyc.php');
  $redirects = Spyc::YAMLLoad($filepath);
  foreach ($redirects['redirects'] as $item) {
    $redirect = new stdClass();
    redirect_object_prepare(
      $redirect, 
      array(
        'source' => $item['source'], 
        'source_options' => array(),
        'redirect' => $item['redirect'], 
        'redirect_options' => array(), 
        'language' => LANGUAGE_NONE, 
      ) 
    );
    redirect_save($redirect);
  }
}
