#!/bin/bash

# @file
# Set up your local environment.

# Clear your local Memcached so old cache entries from the previous version of
# the site don't blow up this release script. Clear this on both your local and
# Vagrant, so we cover both use cases.
echo "flush_all" | nc localhost 11211
echo "flush_all" | nc localhost 11221

../releases/release-1-0.sh

# This command ensures your local copy is ready for development.
drush golocal -y
