<?php

namespace Drupal\migrate_facebook_destination;

use Drupal\Core\Config\ConfigFactoryInterface;
use Facebook\Facebook as FacebookBase;

/**
 * Facebook service.
 */
class Facebook extends FacebookBase {

  /**
   * Constructs a Facebook object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *
   * @throws \Facebook\Exceptions\FacebookSDKException
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    parent::__construct($config_factory->get('migrate_facebook_destination.settings')->get());
  }

}
