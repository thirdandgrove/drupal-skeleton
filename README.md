README
=

Installation
-

1. Clone the repo

1. At the repo root:
> 'drush make drupal7.make docroot'

1. Install [Vagrant](https://www.vagrantup.com/downloads.html)

1. Configure Vagrant host file
> `vagrant plugin install vagrant-hostsupdater`

1. Move `docroot-goodies\global_custom_module` to `docroot/sites/all/modules/custom` and name the module `[projectcode]_global`. For example, `nglcc_global`. Update `releases/release-1-0/bootstrap.php` with the module name you picked.

1. Move `example.local.settings.php` to `docroot/sites/default`. Alter the this file for any needed local configuration changes than make a copy of the file in the same folder called `local.settings.php`. This file will be ignored by git.

1. Copy `docroot-goodies/settings.php` to `docroot/sites/default/settings.php` and change `$drupal_hash_salt`.

1. Boot up the Vagrant machine
> `vagrant up`

1. From `docroot` run the local setup script:
> `scripts/setup-local.sh`

Release Helpers
-

Take a look at `releases/release-1-0.sh`. This is a starting template for a the first site release script. After initial release (or when the marketing team starts to use the CMS before launch), you can switch to a new release script format that matches the initial template except for the `drush site-install` line.

See `releases/utils` for a variety of helper functions for importing data during releases and `releases/utils/examples` for specific release examples from previous projects.

Apache Solr
-
This Drupal starter contains Apache solr 3.5, but it has not been configured for any Drupal modules yet. To see it in action visit: `http://192.168.50.10:8080/solr` after running `vagrant up`. Schema file is located at `/usr/share/solr/example/solr/conf`. You can replace the schema.xml with whatever schema file your drupal implementation suggests.


Issues? Features?
-
We welcome fixes and new features. Just fork the repo and submit a pull request!
