<?php

namespace Drupal\migrate_facebook_destination\Plugin\migrate\destination;

use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\Row;
use Facebook\Exceptions\FacebookSDKException;

/**
 * Class FacebookGroup.
 *
 * @package Drupal\migrate_facebook_destination\Plugin\migrate\destination
 *
 * @MigrateDestination(
 *   id = "facebook:group",
 * )
 */
class FacebookGroup extends FacebookBase {

  /**
   * Indicates whether the destination can be rolled back.
   *
   * @var bool
   */
  protected $supportsRollback = TRUE;

  /**
   * {@inheritdoc}
   */
  public function fields(MigrationInterface $migration = NULL): array {
    return [
      'message' => $this->t('Message'),
      'link' => $this->t('Link'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function import(Row $row, array $old_destination_id_values = []) {
    try {
      $response = $this->facebook->post("/{$this->configuration['facebook']['group_id']}/feed", [
        'message' => $row->getDestinationProperty('message'),
        'link' => $row->getDestinationProperty('link'),
      ]);

      $graph_node = $response->getGraphNode();

      return [$graph_node->getField('id')];
    }
    catch (FacebookSDKException $exception) {
      return FALSE;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function rollback(array $destination_identifier) {
    $this->facebook->delete(reset($destination_identifier));
    parent::rollback($destination_identifier);
  }

}
