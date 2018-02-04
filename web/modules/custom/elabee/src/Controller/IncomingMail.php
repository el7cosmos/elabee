<?php

namespace Drupal\elabee\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\migrate\MigrateMessage;
use Drupal\migrate\Plugin\MigrationPluginManagerInterface;
use Drupal\migrate_tools\MigrateExecutable;
use Mailgun\Mailgun;
use Raven_Client;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Returns responses for eL Abee routes.
 */
class IncomingMail extends ControllerBase {

  /**
   * @var \Raven_Client
   */
  protected $raven;

  /**
   * @var null|\Symfony\Component\HttpFoundation\Request
   */
  protected $request;

  /**
   * @var \Mailgun\Mailgun
   */
  protected $mailgun;

  /**
   * @var \Drupal\migrate\Plugin\MigrationInterface
   */
  protected $migration;

  /**
   * Constructs the controller object.
   *
   * @param \Mailgun\Mailgun $mailgun
   * @param \Drupal\migrate\Plugin\MigrationPluginManagerInterface $migration_plugin_manager
   * @param \Raven_Client $raven_client
   * @param \Symfony\Component\HttpFoundation\RequestStack $request_stack
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   */
  public function __construct(Mailgun $mailgun, MigrationPluginManagerInterface $migration_plugin_manager, Raven_Client $raven_client, RequestStack $request_stack) {
    $this->mailgun = $mailgun;
    $this->migration = $migration_plugin_manager->createInstance('weeklydrop');
    $this->raven = $raven_client;
    $this->request = $request_stack->getCurrentRequest();
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('elabee.mailgun'),
      $container->get('plugin.manager.migration'),
      $container->get('elabee.raven'),
      $container->get('request_stack')
    );
  }

  /**
   * Builds the response.
   */
  public function weeklydrop(): Response {
    $body = $this->request->request->all();
    $valid = $this->mailgun->webhooks()->verifyWebhookSignature($body['timestamp'], $body['token'], $body['signature']);

    if (!$valid) {
      throw new AccessDeniedHttpException();
    }

    $this->raven->captureMessage(__METHOD__, [], ['level' => Raven_Client::DEBUG]);

    $executable = new MigrateExecutable($this->migration, new MigrateMessage());
    $executable->import();

    return new JsonResponse();
  }

}
