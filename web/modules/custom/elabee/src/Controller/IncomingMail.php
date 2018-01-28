<?php

namespace Drupal\elabee\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Session\AccountProxyInterface;
use Mailgun\Mailgun;
use Raven_Client;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Returns responses for eL Abee routes.
 */
class IncomingMail extends ControllerBase {

  /**
   * @var string
   */
  protected $environment;

  /**
   * @var null|\Symfony\Component\HttpFoundation\Request
   */
  protected $request;

  /**
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $user;

  /**
   * Constructs the controller object.
   *
   * @param \Symfony\Component\HttpFoundation\RequestStack $request_stack
   * @param $environment
   */
  public function __construct(AccountProxyInterface $account_proxy, RequestStack $request_stack, $environment) {
    $this->user = $account_proxy->getAccount();
    $this->environment = $environment;
    $this->request = $request_stack->getCurrentRequest();
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('current_user'),
      $container->get('request_stack'),
      $container->getParameter('kernel.environment')
    );
  }

  /**
   * Builds the response.
   */
  public function weeklydrop() {
    $body = $this->request->request->all();
    $mailgun = Mailgun::create(getenv('MAILGUN_API_KEY'));
    $valid = $mailgun->webhooks()->verifyWebhookSignature($body['timestamp'], $body['token'], $body['signature']);

    if (!$valid) {
      throw new AccessDeniedHttpException();
    }

    $options = [
      'curl_method' => 'async',
      'dsn' => getenv('SENTRY_DSN'),
      'environment' => $this->environment,
      'name' => $this->config('system.site')->get('name'),
      'processorOptions' => [
        'Raven_SanitizeDataProcessor' => [
          'fields_re' => '/(SESS|pass|authorization|password|passwd|secret|password_confirmation|card_number|auth_pw)/i',
        ],
      ],
      'site' => $this->request->getHost(),
    ];
    $client = new Raven_Client($options);
    $client->user_context([
      'id' => $this->user->id(),
      'ip_address' => $this->request->getClientIp(),
      'name' => $this->user->getAccountName(),
    ]);
    $client->captureMessage(__METHOD__, [], ['level' => Raven_Client::DEBUG]);

    return new JsonResponse([]);
  }

}
