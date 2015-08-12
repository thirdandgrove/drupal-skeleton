<?php

// Allow people to register without admin approval.
variable_set('user_register', USER_REGISTER_VISITORS);
variable_set('user_email_verification', TRUE);

// Create some accounts.
$accounts = array();
$accounts[] = array(
  'username',
  'user@example.com',
  array('administrator'),
);

foreach ($accounts as $account) {
  list($username, $mail, $account_roles) = $account;

  $roles = array();
  foreach ($account_roles as $role_name) {
    $rid = array_search($role_name, user_roles());
    $roles[$rid] = $role_name;
  }
  $roles[DRUPAL_AUTHENTICATED_RID] = 'authenticated user';

  $account = new stdClass;
  $account->is_new = TRUE;
  $edit = array(
    'name' => $username,
    'mail' => $mail,
    'pass' => user_password(25),
    'language' => LANGUAGE_NONE,
    'status' => 1,
    'init' => $mail,
    'roles' => $roles,
  );
  user_save($account, $edit);
}

drush_log(dt('Users created'), 'ok');
