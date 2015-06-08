<?php
/**
 * @file
 * DOM-related functionality.
 */

/**
 * Helper function to convert node element into flat HTML string.
 */
function tag_get_inner_html(DOMNode $node) {
  $innerHTML= '';
  $children = $node->childNodes;
  foreach ($children as $child) {
      $innerHTML .= $child->ownerDocument->saveXML($child);
  }
  return $innerHTML;
}

/**
 * Parses an HTML file and saves it as a node.
 *
 * Extend this class and implement parseHTML() to build an importer. Using the
 * supplied DOMXPath object (in $this->xpath) you can quickly scan the HTML and
 * populate $this->node with the data you care about.
 *
 * @see examples/html_import.php for a working example.
 */
abstract class TAGHTMLNodeImport {
  // Set this to a node type.
  public $node_type = 'page';
  // Set this to a valid text filter.
  public $node_body_format = 'full_html';
  // Stores the file object passed to the constructor.
  protected $file;
  // Stores the text of the source file.
  protected $source;
  // The DOMDocument object representing the source file.
  protected $dom;
  // A DOMXPath instance for searching the source file.
  protected $xpath;
  // The node data that will be saved.
  protected $node;

  /**
   * Parses the HTML and stores the data in $this->node.
   */
  abstract protected function parseHTML();

  /**
   * Instantiates necessary objects and load the file data.
   *   @param object $file
   *     An object like the ones returned from file_scan_directory(). Must have
   *     the following properties:
   *       - uri The full path to the file, including filename.
   */
  function __construct($file) {
    $this->file = $file;
    $this->source = file_get_contents($file->uri);
    $this->dom = new DOMDocument;
    // HTML is rarely perfect and loadHTML() is chatty.
    @$this->dom->loadHTML($this->source);
    // Use this for queries.
    $this->xpath = new DOMXPath($this->dom);
    $this->prepareNode();
  }

  /**
   * Builds out an empty node object to populate.
   *
   * Override this to specify different default options.
   */
  protected function prepareNode() {
    global $user;
    $this->node = (object) array(
      'type' => $this->node_type,
      'language' => LANGUAGE_NONE,
      'uid' => $user->uid,
      'name' => $user->name,
      'title' => '',
      'status' => NODE_PUBLISHED,
      'promote' => NODE_NOT_PROMOTED,
      'body' => array(LANGUAGE_NONE => array(array(
        'value' => '',
        'format' => $this->node_body_format,
      ))),
    );
  }

  /**
   * Parses HTML into a node object and saves it.
   */
  public function import() {
    $this->parseHTML();
    node_object_prepare($this->node);
    node_save($this->node);
    $this->postImport();
  }

  /**
   * Performs actions after an import has been completed.
   *
   * Override this method to do things after the import has been completed.
   */
  protected function postImport() {
  }
}
