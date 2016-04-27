<?php
/**
 * @file
 * Utility to import redirects using the redirect module.
 */

/**
 * Import redirects from a yaml file.
 *
 * @param string $filepath
 *   The path to the yaml file containing the redirects.
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

/**
 * Import redirects from a csv file.
 *
 * The CSV should be formatted with the source in the first column and
 * destination in the second column. Empty destinations are assumed to be the
 * front page. The source should always be a relative path.
 *
 * @param string $filepath
 *   The path to the csv file containing the redirects.
 */
function tag_import_redirects_csv($filepath)  {
  $file = fopen($filepath, 'r');
  while ($row = fgetcsv($file)) {
    $redirect = new stdClass();
    redirect_object_prepare(
      $redirect,
      array(
        'source' => $row[0],
        'source_options' => array(),
        'redirect' => empty($row[1]) ? '<front>' : $row[1],
        'redirect_options' => array(),
        'language' => LANGUAGE_NONE,
      )
    );
    redirect_save($redirect);
  }
}
