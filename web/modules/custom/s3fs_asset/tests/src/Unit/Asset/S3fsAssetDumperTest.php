<?php

namespace Drupal\Tests\s3fs_asset\Unit\Asset;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\StreamWrapper\StreamWrapperInterface;
use Drupal\s3fs_asset\Asset\S3fsAssetDumper;
use Drupal\Tests\UnitTestCase;

class S3fsAssetDumperTest extends UnitTestCase {

  /**
   * @var bool
   */
  protected $case;

  public static function dumpProvider() {
    return [
      [FALSE],
      [TRUE],
    ];
  }

  /**
   * @param bool $case
   *
   * @dataProvider dumpProvider
   */
  public function testDump(bool $case) {
    $this->case = $case;

    if (!defined('FILE_CREATE_DIRECTORY')) {
      define('FILE_CREATE_DIRECTORY', 1);
    }
    if (!defined('FILE_EXISTS_REPLACE')) {
      define('FILE_EXISTS_REPLACE', 1);
    }

    $wrappers = stream_get_wrappers();
    if (array_search('s3', $wrappers) === FALSE) {
      $stream_wrapper = $this->createMock(StreamWrapperInterface::class);
      stream_wrapper_register('s3', get_class($stream_wrapper));
    }

    $dumper = new S3fsAssetDumper(\Drupal::service('config.factory'));
    self::assertInternalType('string', $dumper->dump($case, 'css'));
  }

  protected function setUp() {
    parent::setUp();

    $config_factory = $this->getConfigFactoryStub([
      's3fs.settings' => [],
      'system.performance' => [
        'css.gzip' => TRUE,
        'js.gzip' => TRUE,
      ],
    ]);

    $container = new ContainerBuilder();
    $container->set('config.factory', $config_factory);
    \Drupal::setContainer($container);
  }

}

namespace Drupal\s3fs_asset\Asset;

if (!function_exists('file_prepare_directory')) {
  function file_prepare_directory() {
  }
}
if (!function_exists('file_unmanaged_save_data')) {
  function file_unmanaged_save_data($data) {
    if (extension_loaded('zlib')) {
      $data = gzdecode($data);
    }
    return $data;
  }
}
