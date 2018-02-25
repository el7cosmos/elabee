<?php

namespace Drupal\s3fs_asset\Asset;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Asset\AssetCollectionGrouperInterface;
use Drupal\Core\Asset\AssetDumperInterface;
use Drupal\Core\Asset\AssetOptimizerInterface;
use Drupal\Core\Asset\CssCollectionOptimizer;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\State\StateInterface;

class S3fsAssetCssCollectionOptimizer extends CssCollectionOptimizer {

  /**
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  protected $config;

  /**
   * @var \Drupal\Component\Datetime\TimeInterface
   */
  protected $time;

  public function __construct(AssetCollectionGrouperInterface $grouper, AssetOptimizerInterface $optimizer, AssetDumperInterface $dumper, ConfigFactoryInterface $config_factory, StateInterface $state, TimeInterface $time) {
    parent::__construct($grouper, $optimizer, $dumper, $state);

    $this->config = $config_factory->get('system.performance');
    $this->time = $time;
  }

  /**
   * {@inheritdoc}
   */
  public function deleteAll() {
    $this->state->delete('drupal_css_cache_files');

    $delete_stale = function ($uri) {
      // Default stale file threshold is 30 days.
      if ($this->time->getRequestTime() - filemtime($uri) > $this->config->get('stale_file_threshold')) {
        file_unmanaged_delete($uri);
      }
    };
    file_scan_directory('s3://css', '/.*/', ['callback' => $delete_stale]);
  }

}
