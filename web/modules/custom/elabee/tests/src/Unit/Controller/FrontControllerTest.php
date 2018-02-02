<?php

namespace Drupal\Tests\elabee\Unit\Controller;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\elabee\Controller\FrontController;
use Drupal\Tests\UnitTestCase;

class FrontControllerTest extends UnitTestCase {

  public function testFront() {
    $container = new ContainerBuilder();

    $controller = FrontController::create($container);
    self::assertInternalType('array', $controller->front());
  }

}
