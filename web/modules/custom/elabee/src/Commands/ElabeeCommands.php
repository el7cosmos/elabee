<?php

namespace Drupal\elabee\Commands;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\s3fs\S3fsServiceInterface;
use Drush\Commands\DrushCommands;

/**
 * A Drush commandfile.
 *
 * In addition to this file, you need a drush.services.yml
 * in root of your module, and a composer.json file that provides the name
 * of the services file to use.
 *
 * See these files for an example of injecting Drupal services:
 *   - http://cgit.drupalcode.org/devel/tree/src/Commands/DevelCommands.php
 *   - http://cgit.drupalcode.org/devel/tree/drush.services.yml
 */
class ElabeeCommands extends DrushCommands {

  /**
   * @var \Drupal\s3fs\S3fsServiceInterface
   */
  protected $s3fs;

  /**
   * @var array|mixed|null
   */
  protected $settings;

  public function __construct(ConfigFactoryInterface $config_factory, S3fsServiceInterface $s3fs) {
    parent::__construct();

    $this->s3fs = $s3fs;
    $this->settings = $config_factory->get('s3fs.settings')->get();
  }

  /**
   * Heroku release.
   *
   * @command heroku:release
   *
   * @throws \Exception
   */
  public function release(): void {
    drush_invoke_process('@self', 'cache:rebuild');
    drush_invoke_process('@self', 'updatedb');
    drush_invoke_process('@self', 'entity:updates');
    drush_invoke_process('@self', 'config-split:import');

    if ($this->s3fs->validate($this->settings)) {
      $this->s3fs->refreshCache($this->settings);
    }
  }

}
