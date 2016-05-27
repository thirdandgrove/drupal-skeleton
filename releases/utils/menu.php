<?php
/**
 *  @file
 *  Utility functions to import menus.
 */

/**
 *  Import menus from yaml menu definitions.
 *  @see examples/example_menu.yaml.
 *
 *  @param string $filepath
 *    The path to the YAML file containing the menu definitions.
 */
function tag_import_menus($filepath)  {
  require_once('../releases/utils/helpers/Spyc.php');
  $menus = Spyc::YAMLLoad($filepath);
  foreach ($menus['menus'] as $menu) {
    tag_save_menu_items($menu['name'], $menu['items']);
  }
}

/**
 *  Recurse over an array of menu items and save all children correctly.
 *
 *  @param string $menu_name
 *    The name of the menu.
 *  @param array $items
 *    An array of menu items as defined above.
 *  @param integer $plid
 *    An optional parent id for the items. Default is NULL.
 */
function tag_save_menu_items($menu_name, $items, $plid = NULL)  {
  // This is the lowest weight Drupal offers for menu items.
  $weight = -50;
  foreach ($items as $item) {
    $item['menu_name'] = $menu_name;
    $item['link_path'] = drupal_get_normal_path($item['link_path']);
    $item['weight'] = isset($item['weight']) ? $item['weight'] : $weight;
    $item['plid'] = $plid;
    $item['expanded'] = isset($item['expanded']) ? $item['expanded'] : 0;
    $mlid = menu_link_save($item);
    if (isset($item['children'])) {
      tag_save_menu_items($menu_name, $item['children'], $mlid);
    }
    $weight++;
  }
}
