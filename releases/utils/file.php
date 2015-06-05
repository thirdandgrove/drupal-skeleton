<?php
/**
 *  @file
 *  Helper to move files to Drupal's files directory.
 */

/**
 * Upload files to Drupal's file directory.
 *
 * This scans the source directory recursively. By default, all the new files go
 * to the same destination directory. Set $retain_path = TRUE to retain the old
 * paths relative to the new $destination_base_path.
 *
 * @param string $source_base_path
 *   The path to the directory containing the files.
 * @param string $destination_base_path
 *   The destination directory prefix.
 * @param boolean $retain_path
 *   If TRUE, any path after $source_base_path in the source will be retained on
 *   the new copy.
 */
function tag_import_files($source_base_path, $destination_base_path = '', $retain_path = FALSE) {
  $files = file_scan_directory($source_base_path, '/[.*\.jpg]|[.*\.png]|[.*\.gif]|[.*\.pdf]$/');
  // Sorting what is returned helps ensure that the file order is the same when
  // run on different systems. The configuration of local systems can cause the
  // ordering to be different.
  ksort($files, SORT_STRING);
  foreach ($files as $file) {
    $file->status = 1;
    $destination_path = 'public://';
    if ($destination_base_path) {
      $destination_path .= $destination_base_path;
    }
    if ($retain_path) {
      // Break apart the file uri to find the relative path.
      $file_path = $file->uri;
      // We don't want the base path.
      $file_path = str_replace($source_base_path . '/', '', $file_path);
      // Discard the filename.
      $file_path_parts = explode('/', $file_path);
      array_pop($file_path_parts);
      $file_path = implode('/', $file_path_parts);
      // We only need to prepend a path separator if a destination base path has
      // been added.
      $destination_path .= $destination_base_path ? "/$file_path" : $file_path;
    }
    if (!file_prepare_directory($destination_path)) {
      drupal_mkdir($destination_path, NULL, TRUE);
    }
    file_copy($file, $destination_path, FILE_EXISTS_REPLACE);
  }
}
