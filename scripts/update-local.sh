#!/bin/bash

# @file
# Update your local environment from an Acquia production environment.

VAGRANT="$1"

ACCOUNT_NAME="example"
# Set this to "dev" or "stg" to pull from those environments instead. Blank
# means prod.
ENV_SOURCE=""
SSH_SOURCE="example@abc-12345.prod.hosting.acquia.com"
BACKUPS_PREFIX="prod"
VAGRANT_IP="192.168.50.10"
VAGRANT_DB="vagrant"
VAGRANT_USER="vagrant"
VAGRANT_PASS="vagrant"
VAGRANT_HOST="local.example.com"
MEMCACHE_PORT="11211"

FILES_DIR="/mnt/files/$ACCOUNT_NAME$ENV_SOURCE/sites/default/files"
BACKUPS_DIR="/mnt/gfs/$ACCOUNT_NAME$ENV_SOURCE/backups"
DB_REMOTE_NAME="$BACKUPS_DIR/$BACKUPS_PREFIX-$ACCOUNT_NAME-$ACCOUNT_NAME$ENV_SOURCE-`date +%Y-%m-%d`.sql.gz"

# Download the latest daily backup.
scp $SSH_SOURCE:$DB_REMOTE_NAME ~/$ACCOUNT_NAME-$ENV_SOURCE-db.sql.gz

if [ $? -ne 0 ]
then
  echo 'Could not download the daily backup.  Please verify that you have a private key'
  echo 'file in ~/.ssh which matches a public key that you have uploaded to Acquia.'
  exit
fi

# We need to drop our database to address use cases where modules are enabled
# on local but not on prod that may have created tables locally.
echo 'Importing database into your local...'

mysqldump -u $VAGRANT_USER -h $VAGRANT_IP --password=$VAGRANT_PASS --add-drop-table --no-data $VAGRANT_DB | grep ^DROP | mysql -u $VAGRANT_USER -h $VAGRANT_IP --password=$VAGRANT_PASS -D $VAGRANT_DB
gunzip ~/$ACCOUNT_NAME-$ENV_SOURCE-db.sql.gz
echo "flush_all" | nc $VAGRANT_IP $MEMCACHE_PORT
mysql -u $VAGRANT_USER -h $VAGRANT_IP --password=$VAGRANT_PASS -D $VAGRANT_DB < ~/$ACCOUNT_NAME-$ENV_SOURCE-db.sql

echo 'Downloading files....'
rsync -r --exclude 'js/*' --exclude 'css/*' $SSH_SOURCE:$FILES_DIR/* sites/default/files

echo 'Setting up local dev....'
drush golocal -y
drush cc all

chmod u+w ~/$ACCOUNT_NAME-$ENV_SOURCE-db.sql
rm ~/$ACCOUNT_NAME-$ENV_SOURCE-db.sql

drush uli --uri="http://$VAGRANT_HOST"

echo 'Finished!'
