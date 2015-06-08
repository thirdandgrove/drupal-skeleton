<?php

/**
 * Imports Basic Page content.
 *
 * Usage:
 *   $files = file_scan_directory(ExampleHTMLImport::$source_base_path, '/.*\.html/');
 *   foreach ($files as $file) {
 *     $page = new FastlyPageImport($file);
 *     $page->import();
 *   }
 */

require_once('../releases/utils/dom.php');

class ExampleHTMLImport extends TAGHTMLNodeImport {
  // Set this to the base directory where you're importing from. This part of
  // the path will be stripped from the filename.
  public static $source_base_path = '../releases/release-1-0/example/pages';
  // The path of the file relative to $source_base_path.
  protected $path;

  /**
   * Adds a path property that stores the path prefix for image files.
   */
  function __construct($file) {
    parent::__construct($file);
    // Calculate the path relative to the base path.
    $this->path = $file->uri;
    $this->path = str_replace($source_base_path . '/', '', $this->path);
    $this->path = str_replace($file->filename, '', $this->path);
  }

  /**
   * Parses out the data for a basic page.
   */
  protected function parseHTML() {
    // This query will grab all elements with class containing 'node'.
    $results = $this->xpath->query("//*[contains(@class, 'node')]");
    // In this example we're only using the first matching element.
    $body_element = reset($results);
    $body = tag_get_inner_html($body_element);
    // Fix image path.
    $body = str_replace('/img/', '/sites/default/files/example-pages/', $body);

    // Get the page title from the <title> tag.
    $title_element = reset($this->xpath->query("/html/head/title"));
    // Titles pulled from the <title> tag are formatted like:
    // "My Page | ExampleSite" so we strip off the extra.
    $title = reset(explode('|', $title_element->nodeValue));

    // Finally, populate the node.
    $this->node->title = $title;
    $this->node->body[$this->node->language][0]['value'] = $body;
    $this->node->body[$this->node->language][0]['summary'] = text_summary($body);
    $this->node->body[$this->node->language][0]['format'] = 'full_html';
  }

  /**
   * Adds redirects from the old paths to the new node URL.
   */
  protected function postImport() {
    // Set up redirects from old paths.
    $redirect = new stdClass();
    redirect_object_prepare(
      $redirect,
      array(
        'source' => $this->path,
        'source_options' => array(),
        'redirect' => 'node/' . $this->node->nid,
        'redirect_options' => array(),
        'language' => LANGUAGE_NONE,
      )
    );
    redirect_save($redirect);
    $redirect = new stdClass();
    redirect_object_prepare(
      $redirect,
      array(
        'source' => $this->path . 'index.html',
        'source_options' => array(),
        'redirect' => 'node/' . $this->node->nid,
        'redirect_options' => array(),
        'language' => LANGUAGE_NONE,
      )
    );
    redirect_save($redirect);
  }
}
