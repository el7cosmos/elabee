<?php

namespace Drupal\Tests\migrate_facebook_destination\Unit;

use Drupal\migrate_facebook_destination\Facebook;
use Drupal\Tests\UnitTestCase;
use Facebook\Facebook as FacebookBase;

class FacebookTest extends UnitTestCase {

  /**
   * @throws \Facebook\Exceptions\FacebookSDKException
   */
  public function testFacebook() {
    $config_factory = $this->getConfigFactoryStub([
      'migrate_facebook_destination.settings' => [
        'app_id' => 'app_id',
        'app_secret' => 'app_secret',
      ],
    ]);
    $facebook = new Facebook($config_factory);
    self::assertInstanceOf(FacebookBase::class, $facebook);
  }

}
