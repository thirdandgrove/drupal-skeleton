<?php
/**
 *  @file
 *  Helper to move files to Drupal's files directory.
 */

/**
 *  Upload files to drupals file directory.
 *
 *  @param string $files_dir
 *    The path to the directory containing the files.
 */
function tag_import_files($files_dir) {
  $files = file_scan_directory($files_dir, '/[.*\.jpg]|[.*\.png]|[.*\.gif]|[.*\.pdf]$/');

  // Sorting what is returned helps ensure that the file order is the same when
  // run on different systems. The configuration of local systems can be cause
  // the ordering to be different.
  ksort($files, SORT_STRING);
  foreach ($files as $file) {
    $file->status = 1;
    file_copy($file, 'public://', FILE_EXISTS_REPLACE);
  }
}
