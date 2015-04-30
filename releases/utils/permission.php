<?php
/**
 *  @file
 *  Utility to import permission settings.
 */

/**
 *  Import permissions settings from a yaml permissions definition file.
 *
 *  @param string $filepath
 *    The path to the yaml file containing the permissions config.
 */
function tag_import_permissions($filepath)  {
  require_once('../releases/utils/helpers/Spyc.php');
  $perms = Spyc::YAMLLoad($filepath);
  foreach ($perms['roles'] as $role => $permissions) {
    $rid = array_search($role, user_roles());
    user_role_grant_permissions($rid, $permissions);
  }
}
