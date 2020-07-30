#!/bin/bash

#
# Ensure only one instance of actionscheduler queue is running
#
# Parameter $1: Runner name (importer-upload-product-image or others)
# Example: sh wp-as-runner.sh importer-upload-product-image
#

wp=/usr/local/bin/wp-cli.phar
www=/var/www/vhosts/obchodiste.cz/httpdocs
outputpath=/var/www/vhosts/obchodiste.cz/httpdocs/wp-content/as.log

if [ "$1" = "importer-upload-product-image" ]; then
  hooks=importer/upload_product_image
elif [ "$1" = "others" ]; then
  hooks=action_scheduler/migration_hook,importer/import_csv,importer/import_xml,importer/insert_product,move_post_to_trash_job,term_syncer/sync_wholesaler
else
  echo "Provide correct runner name in script parameters"
  exit 1
fi

lockdir=/tmp/obchodiste-cz-$1.lock

# Create lockdir
if mkdir $lockdir 2>/dev/null
then
  # Run ActionScheduler
  $wp --path=$www action-scheduler run --hooks=$hooks >> $outputpath 2>&1
  # Remove lockdir when action scheduler finishes
  rm -rf $lockdir
else
  # Locked
  echo "Already running. Try again later."
  exit 0
fi