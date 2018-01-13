<?php

namespace Drupal\migrate_facebook_destination\Plugin\migrate\destination;

use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\migrate\Plugin\migrate\destination\DestinationBase;
use Drupal\migrate\Plugin\MigrationInterface;
use Facebook\Facebook;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class FacebookBase.
 *
 * @package Drupal\migrate_facebook_destination\Plugin\migrate\destination
 */
abstract class FacebookBase extends DestinationBase implements ContainerFactoryPluginInterface {

  /**
   * The facebook client.
   *
   * @var \Facebook\Facebook
   */
  protected $facebook;

  /**
   * Constructs a Facebook destination plugin.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\migrate\Plugin\MigrationInterface $migration
   *   The migration.
   * @param \Drupal\Core\Config\ImmutableConfig $config
   *   The facebook configuration.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration, ImmutableConfig $config) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $migration);

    $this->facebook = new Facebook($config->get());
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration = NULL) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $migration,
      $container->get('config.factory')->get('migrate_facebook_destination.settings')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'id' => [
        'type' => 'string',
        'max_lenght' => 255,
      ],
    ];
  }

}
