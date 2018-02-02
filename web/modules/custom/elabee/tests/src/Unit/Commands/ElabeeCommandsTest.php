<?php

namespace Drupal\Tests\elabee\Unit\Commands;

use Drupal\elabee\Commands\ElabeeCommands;
use Drupal\s3fs\S3fsServiceInterface;
use Drupal\Tests\UnitTestCase;

class ElabeeCommandsTest extends UnitTestCase {

  public function testRelease() {
    $config_factory = $this->getConfigFactoryStub([
      's3fs.settings' => [],
    ]);

    $s3fs = $this->createMock(S3fsServiceInterface::class);
    $s3fs->expects(self::any())
      ->method('validate')
      ->willReturn(TRUE);

    $commands = new ElabeeCommands($config_factory, $s3fs);
    self::assertNull($commands->release());
  }

}

namespace Drupal\elabee\Commands;

if (!function_exists('drush_invoke_process')) {
  function drush_invoke_process() {
  }
}
