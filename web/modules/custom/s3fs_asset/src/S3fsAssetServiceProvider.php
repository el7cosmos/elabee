<?php

namespace Drupal\s3fs_asset;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderBase;
use Drupal\s3fs\Asset\S3fsCssOptimizer;
use Drupal\s3fs_asset\Asset\S3fsAssetCssCollectionOptimizer;
use Drupal\s3fs_asset\Asset\S3fsAssetDumper;
use Drupal\s3fs_asset\Asset\S3fsAssetJsCollectionOptimizer;
use Symfony\Component\DependencyInjection\Reference;

class S3FsAssetServiceProvider extends ServiceProviderBase {

  /**
   * {@inheritdoc}
   */
  public function alter(ContainerBuilder $container) {
    $container->getDefinition('asset.css.collection_optimizer')->setClass(S3fsAssetCssCollectionOptimizer::class)->addArgument(new Reference('datetime.time'));
    $container->getDefinition('asset.css.dumper')->setClass(S3fsAssetDumper::class)->addArgument(new Reference('config.factory'));
    // Fix CSS static urls
    $container->getDefinition('asset.css.optimizer')->setClass(S3fsCssOptimizer::class);

    $container->getDefinition('asset.js.collection_optimizer')->setClass(S3fsAssetJsCollectionOptimizer::class)->addArgument(new Reference('datetime.time'));
    $container->getDefinition('asset.js.dumper')->setClass(S3fsAssetDumper::class)->addArgument(new Reference('config.factory'));
  }

}
