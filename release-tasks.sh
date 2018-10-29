#!/usr/bin/env bash

drush cache:rebuild
drush updatedb
drush entity:updates
drush config:import
drush cache:rebuild
drush heroku:release
