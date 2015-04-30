<?php

$update_free_access = FALSE;
$drupal_hash_salt = 'CHANGE ME BRO';

ini_set('session.gc_probability', 1);
ini_set('session.gc_divisor', 100);
ini_set('session.gc_maxlifetime', 200000);
ini_set('session.cookie_lifetime', 2000000);

$conf['404_fast_paths_exclude'] = '/\/(?:styles)\//';
// Note that we have added web font file extensions to Fast404. You're welcome.
$conf['404_fast_paths'] = '/^(?!sitemap.xml).*\.(?:txt|png|gif|jpe?g|css|js|ico|swf|flv|cgi|bat|pl|dll|exe|asp|woff|otf|svg|ttf|eot|xml)$/i';
$conf['404_fast_html'] = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML+RDFa 1.0//EN" "http://www.w3.org/MarkUp/DTD/xhtml-rdfa-1.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><title>404 Not Found</title></head><body><h1>Not Found</h1><p>The requested URL "@path" was not found on this server.</p></body></html>';

/**
 * Set various settings based on the environment.
 */
if (isset($_ENV['SITE_ENVIRONMENT'])) {
  switch($_ENV['SITE_ENVIRONMENT']) {
    case 'prod':
      // Ensure no errors are shown on production.
      ini_set('display_errors', 0);
      break;
    case 'test':
      break;
    case 'dev':
      break;
  }
}
// Set anything important for your local environment, like ensuring the
// production Salesforce API isn't being hit.
else {
}

$conf['acquia_identifier'] = '';
$conf['acquia_key'] = '';

/**
 * Platform settings file where your platform DB credentials live.
 *
 * Doing this keeps them out of version control.
 */
if (file_exists('/var/www/site-php')) {
  require '/var/www/site-php/PROJECT/PROJECT-settings.inc';
}

/**
 * Secret settings file.
 *
 * This file should NEVER be committed to version control.
 */
if (file_exists('./' . conf_path() . '/local.settings.php')) {
  require './' . conf_path() . '/local.settings.php';
}

// We'll use php_sapi_name to check if php is running from the command line.
// This is necessary because some drush calls may trigger a fatal by exceeding
// the memory limit. In case drush is running drupal, we want to give it a
// generous memory limit.
if (php_sapi_name() == 'cli') {
  ini_set('memory_limit', '550M');
}
