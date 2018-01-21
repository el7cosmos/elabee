<?php

/**
 * @file
 */

function heroku_drush_command() {
  $items = [];

  $items['heroku-release'] = [
    'description' => "Heroku release command",
    'bootstrap' => DRUSH_BOOTSTRAP_DRUPAL_FULL,
  ];

  return $items;
}

function drush_heroku_release() {
  drush_config_import();
  drush_cache_rebuild();
  drush_s3fs_refresh_cache();
}
