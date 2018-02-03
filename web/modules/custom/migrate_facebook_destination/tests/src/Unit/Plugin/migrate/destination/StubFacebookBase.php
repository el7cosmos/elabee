<?php

namespace Drupal\Tests\migrate_facebook_destination\Unit\Plugin\migrate\destination;

use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\Row;
use Drupal\migrate_facebook_destination\Plugin\migrate\destination\FacebookBase;

class StubFacebookBase extends FacebookBase {

  /**
   * {@inheritdoc}
   */
  public function fields(MigrationInterface $migration = NULL) {
  }

  /**
   * {@inheritdoc}
   */
  public function import(Row $row, array $old_destination_id_values = []) {
  }

  /**
   * {@inheritdoc}
   */
  public function getIds(): array {
    return parent::getIds();
  }

}
