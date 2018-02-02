<?php

namespace Drupal\Tests\elabee\Unit\Controller;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\elabee\Controller\IncomingMail;
use Drupal\Tests\UnitTestCase;
use Mailgun\Api\Webhook;
use Mailgun\Mailgun;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class IncomingMailTest extends UnitTestCase {

  public static function weeklyDropProvider() {
    return [
      [FALSE],
      [TRUE],
    ];
  }

  /**
   * @param bool $case
   *
   * @dataProvider weeklyDropProvider
   */
  public function testWeeklydrop(bool $case) {
    if (!$case) {
      $this->setExpectedException(AccessDeniedHttpException::class);
    }

    $config_factory = $this->getConfigFactoryStub([
      'system.site' => [
        'name' => $this->randomMachineName(),
      ],
    ]);

    $account = $this->createMock(AccountInterface::class);
    $current_user = $this->createMock(AccountProxyInterface::class);
    $current_user->expects(self::any())
      ->method('getAccount')
      ->willReturn($account);

    $webhook = $this->createMock(Webhook::class);
    $webhook->expects(self::any())
      ->method('verifyWebhookSignature')
      ->willReturn($case);
    $mailgun = $this->createMock(Mailgun::class);
    $mailgun->expects(self::any())
      ->method('webhooks')
      ->willReturn($webhook);

    $raven_client = $this->createMock(\Raven_Client::class);

    $request = $this->createMock(Request::class);
    $request->request = $this->createMock(ParameterBag::class);
    $request_stack = $this->createMock(RequestStack::class);
    $request_stack->expects(self::any())
      ->method('getCurrentRequest')
      ->willReturn($request);

    $container = new ContainerBuilder();
    $container->set('config.factory', $config_factory);
    $container->set('elabee.mailgun', $mailgun);
    $container->set('elabee.raven', $raven_client);
    $container->set('current_user', $current_user);
    $container->set('request_stack', $request_stack);
    $container->setParameter('kernel.environment', 'test');
    \Drupal::setContainer($container);

    $controller = IncomingMail::create($container);

    self::assertInstanceOf(Response::class, $controller->weeklydrop());
  }

}
