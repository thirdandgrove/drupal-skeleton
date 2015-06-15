<?php

/**
 * Example local settings PHP file for making drupal run on your local install.
 *
 */

// Set this value to be your email address, to forward all email to you.
$conf['reroute_email_to'] = 'support@thirdandgrove.com';

/**
 * This is configured to connect from your local to a Vagrant box running on
 * your local when running from Drush.
 */

$databases['default']['default'] = array(
  'driver' => 'mysql',
  'database' => 'vagrant',
  'username' => 'vagrant',
  'password' => 'vagrant',
  'host' => '192.168.50.10',
  'prefix' => '',
  'collation' => 'utf8_general_ci',
);

$conf['memcache_servers'] = array(
  '192.168.50.10:11211' => 'default',
);

// For local development we need to make the local solr server the default.
$conf['apachesolr_default_environment'] = 'solr';

// Force showing PHP errors.
error_reporting(E_ALL);
ini_set('display_errors', '1');

$conf['file_temporary_path'] = '/tmp';

$conf['securepages_basepath'] = 'http://local.example.com';
$conf['securepages_basepath_ssl'] = 'https://local.example.com';
