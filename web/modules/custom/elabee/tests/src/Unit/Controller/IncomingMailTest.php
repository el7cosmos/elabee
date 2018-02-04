<?php

namespace Drupal\Tests\elabee\Unit\Controller;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\elabee\Controller\IncomingMail;
use Drupal\migrate\Plugin\MigrateIdMapInterface;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\Plugin\MigrationPluginManagerInterface;
use Drupal\Tests\UnitTestCase;
use Mailgun\Api\Webhook;
use Mailgun\Mailgun;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
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

    $logger_channel = $this->createMock(LoggerChannelInterface::class);
    $logger_factory = $this->createMock(LoggerChannelFactoryInterface::class);
    $logger_factory->expects(self::any())
      ->method('get')
      ->willReturn($logger_channel);

    $request = $this->createMock(Request::class);
    $request->request = $this->createMock(ParameterBag::class);
    $request_stack = $this->createMock(RequestStack::class);
    $request_stack->expects(self::any())
      ->method('getCurrentRequest')
      ->willReturn($request);

    $migration = $this->createMock(MigrationInterface::class);
    $migration->expects(self::any())
      ->method('getIdMap')
      ->willReturn($this->createMock(MigrateIdMapInterface::class));
    $migration->expects(self::any())
      ->method('getStatusLabel')
      ->willReturn('Label');
    $migration_plugin_manager = $this->createMock(MigrationPluginManagerInterface::class);
    $migration_plugin_manager->expects(self::any())
      ->method('createInstance')
      ->willReturn($migration);

    $container = new ContainerBuilder();
    $container->set('config.factory', $config_factory);
    $container->set('current_user', $current_user);
    $container->set('elabee.mailgun', $mailgun);
    $container->set('elabee.raven', $raven_client);
    $container->set('event_dispatcher', $this->createMock(EventDispatcherInterface::class));
    $container->set('logger.factory', $logger_factory);
    $container->set('request_stack', $request_stack);
    $container->set('string_translation', $this->getStringTranslationStub());
    $container->set('plugin.manager.migration', $migration_plugin_manager);
    $container->setParameter('kernel.environment', 'test');
    \Drupal::setContainer($container);

    $controller = IncomingMail::create($container);

    self::assertInstanceOf(Response::class, $controller->weeklydrop());
  }

}
