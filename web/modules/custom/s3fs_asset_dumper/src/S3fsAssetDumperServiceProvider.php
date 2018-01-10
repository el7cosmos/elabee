<?php

namespace Drupal\s3fs_asset_dumper;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderBase;
use Drupal\s3fs\Asset\S3fsCssOptimizer;
use Drupal\s3fs_asset_dumper\Asset\S3fsAssetDumper;

class S3fsAssetDumperServiceProvider extends ServiceProviderBase {

  /**
   * {@inheritdoc}
   */
  public function alter(ContainerBuilder $container) {
    $container->getDefinition('asset.css.dumper')
      ->setClass(S3fsAssetDumper::class);
    // Fix CSS static urls
    $container->getDefinition('asset.css.optimizer')
      ->setClass(S3fsCssOptimizer::class);

    $container->getDefinition('asset.js.dumper')
      ->setClass(S3fsAssetDumper::class);
  }

}
