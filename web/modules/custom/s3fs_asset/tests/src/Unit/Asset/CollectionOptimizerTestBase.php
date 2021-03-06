<?php

namespace Drupal\Tests\s3fs_asset\Unit\Asset;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Asset\AssetCollectionGrouperInterface;
use Drupal\Core\Asset\AssetDumperInterface;
use Drupal\Core\Asset\AssetOptimizerInterface;
use Drupal\Core\State\StateInterface;
use Drupal\Tests\UnitTestCase;

abstract class CollectionOptimizerTestBase extends UnitTestCase {

  protected $sut = '';

  public function testDeleteAll() {
    $grouper = $this->createMock(AssetCollectionGrouperInterface::class);
    $optimizer = $this->createMock(AssetOptimizerInterface::class);
    $dumper = $this->createMock(AssetDumperInterface::class);
    $config_factory = $this->getConfigFactoryStub([
      'system.performance' => [
        'stale_file_threshold' => 0,
      ],
    ]);
    $state = $this->createMock(StateInterface::class);
    $time = $this->createMock(TimeInterface::class);
    $time->expects(self::any())
      ->method('getRequestTime')
      ->willReturn(time());

    /** @var \Drupal\Core\Asset\AssetCollectionOptimizerInterface $collection_optimizer */
    $collection_optimizer = new $this->sut($grouper, $optimizer, $dumper, $config_factory, $state, $time);
    self::assertEmpty($collection_optimizer->deleteAll());
  }

}

namespace Drupal\s3fs_asset\Asset;

if (!function_exists('file_scan_directory')) {
  function file_scan_directory($dir, $mask, $options = []) {
    call_user_func($options['callback'], __FILE__);
  }
}

if (!function_exists('file_unmanaged_delete')) {
  function file_unmanaged_delete() {
  }
}
