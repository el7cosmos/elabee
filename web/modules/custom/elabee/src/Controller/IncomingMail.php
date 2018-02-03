<?php

namespace Drupal\elabee\Controller;

use Drupal\Core\Controller\ControllerBase;
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
   * @var \Drupal\migrate\Plugin\MigrationPluginManagerInterface
   */
  protected $migrationPluginManager;

  /**
   * @var \Drupal\migrate\Plugin\MigrationInterface
   */
  protected $migration;

  /**
   * Constructs the controller object.
   *
   * @param \Mailgun\Mailgun $mailgun
   * @param \Raven_Client $raven_client
   * @param \Symfony\Component\HttpFoundation\RequestStack $request_stack
   */
  public function __construct(Mailgun $mailgun, Raven_Client $raven_client, RequestStack $request_stack) {
    $this->mailgun = $mailgun;
    $this->raven = $raven_client;
    $this->request = $request_stack->getCurrentRequest();
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('elabee.mailgun'),
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

    return new JsonResponse([]);
  }

}
