<?php

namespace Drupal\Tests\elabee\Unit;

use Drupal\Core\Session\AccountProxyInterface;
use Drupal\elabee\Raven;
use Drupal\Tests\UnitTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class RavenTest extends UnitTestCase {

  public function testRaven() {
    $account_proxy = $this->createMock(AccountProxyInterface::class);
    $config = $this->getConfigFactoryStub(['system.site' => ['name' => 'name']]);
    $request_stack = $this->createMock(RequestStack::class);
    $request_stack->expects(self::any())
      ->method('getCurrentRequest')
      ->willReturn($this->createMock(Request::class));

    $raven = new Raven($account_proxy, $config, $request_stack, 'test');
    self::assertInstanceOf(\Raven_Client::class, $raven);
  }

}
