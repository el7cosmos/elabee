<?php


namespace Drupal\migrate_facebook_destination\Plugin\migrate\destination;

use Drupal\migrate\Plugin\migrate\destination\DestinationBase;
use Drupal\migrate\Plugin\MigrationInterface;
use Facebook\Facebook;

abstract class FacebookBase extends DestinationBase {

  /**
   * @var \Facebook\Facebook
   */
  protected $facebook;

  public function __construct(array $configuration, string $plugin_id, $plugin_definition, MigrationInterface $migration) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $migration);

    $this->facebook = new Facebook($configuration['facebook']['settings']);
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
