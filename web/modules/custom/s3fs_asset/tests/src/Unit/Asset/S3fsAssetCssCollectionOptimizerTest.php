<?php

namespace Drupal\Tests\s3fs_asset\Unit\Asset;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Asset\AssetCollectionGrouperInterface;
use Drupal\Core\Asset\AssetDumperInterface;
use Drupal\Core\Asset\AssetOptimizerInterface;
use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\State\StateInterface;
use Drupal\s3fs_asset\Asset\S3fsAssetCssCollectionOptimizer;
use Drupal\Tests\UnitTestCase;

class S3fsAssetCssCollectionOptimizerTest extends UnitTestCase {

  public function testDeleteAll() {
    $grouper = $this->createMock(AssetCollectionGrouperInterface::class);
    $optimizer = $this->createMock(AssetOptimizerInterface::class);
    $dumper = $this->createMock(AssetDumperInterface::class);
    $state = $this->createMock(StateInterface::class);
    $time = $this->createMock(TimeInterface::class);
    $time->expects(self::any())
      ->method('getRequestTime')
      ->willReturn(time());

    $collection_optimizer = new S3fsAssetCssCollectionOptimizer($grouper, $optimizer, $dumper, $state, $time);
    self::assertEmpty($collection_optimizer->deleteAll());
  }

  protected function setUp() {
    parent::setUp();

    $config_factory = $this->getConfigFactoryStub([
      'system.performance' => [
        'stale_file_threshold' => 0,
      ],
    ]);
    $container = new ContainerBuilder();
    $container->set('config.factory', $config_factory);
    \Drupal::setContainer($container);
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
