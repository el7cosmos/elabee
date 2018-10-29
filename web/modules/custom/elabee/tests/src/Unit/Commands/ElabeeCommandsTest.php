<?php

namespace Drupal\Tests\elabee\Unit\Commands;

use Drupal\elabee\Commands\ElabeeCommands;
use Drupal\s3fs\S3fsServiceInterface;
use Drupal\Tests\UnitTestCase;

/**
 * Class ElabeeCommandsTest
 *
 * @package Drupal\Tests\elabee\Unit\Commands
 * @coversDefaultClass \Drupal\elabee\Commands\ElabeeCommands
 */
class ElabeeCommandsTest extends UnitTestCase {

  /**
   * @covers ::release
   */
  public function testRelease(): void {
    /** @var \PHPUnit\Framework\MockObject\MockBuilder|\Drupal\Core\Config\ConfigFactoryInterface $config_factory */
    $config_factory = $this->getConfigFactoryStub([
      's3fs.settings' => [],
    ]);

    /** @var \PHPUnit\Framework\MockObject\MockObject|\Drupal\s3fs\S3fsServiceInterface $s3fs */
    $s3fs = $this->createMock(S3fsServiceInterface::class);
    $s3fs->method('validate')->willReturn(TRUE);
    $s3fs->expects(self::once())->method('refreshCache')->willReturn(NULL);

    $commands = new ElabeeCommands($config_factory, $s3fs);
    $commands->release();
  }

}
