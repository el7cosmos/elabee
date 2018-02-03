<?php

namespace Drupal\Tests\migrate_facebook_destination\Unit\Plugin\migrate\destination;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\Tests\UnitTestCase;
use Facebook\Facebook;

class FacebookBaseTest extends UnitTestCase {

  public function testGetIds() {
    $facebook = $this->createMock(Facebook::class);

    $container = new ContainerBuilder();
    $container->set('facebook.client', $facebook);

    $migration = $this->createMock(MigrationInterface::class);
    $destination = StubFacebookBase::create($container, [], '', '', $migration);
    self::assertArrayHasKey('id', $destination->getIds());
  }

}
