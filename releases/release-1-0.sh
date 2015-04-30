#!/bin/bash

# Be able to report execution time of release script.
RELEASE_START=$(date +%s)

# Used for file paths for invoking Drush scripts.
RELEASE_TAG=`basename $0`
RELEASE_TAG="${RELEASE_TAG%.*}"

# Install clean Drupal. This will complain that it can't set settings.php, but
# that is fine as we don't need any changes to our settings.php. We will change
# this password during release, since it's stored in git.
drush site-install standard --account-name=ADMIN_USERNAME_THAT_ISNT_ADMIN --account-pass=SET_A_PASSWORD_YOU_WILL_CHANGE_LATER -y

drush php-script 'bootstrap.php' --script-path="../releases/${RELEASE_TAG}" --user=ADMIN_USERNAME_THAT_ISNT_ADMIN

# Time we ended.
RELEASE_END=$(date +%s)
RELEASE_DURATION=$(( ($RELEASE_END - $RELEASE_START)/60 ))
echo "Deployment script completed! It took $RELEASE_DURATION minutes."
