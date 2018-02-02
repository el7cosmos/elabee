<?php

namespace Drupal\elabee;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Raven service.
 */
class Raven extends \Raven_Client {

  /**
   * Constructs a Raven object.
   *
   * @param \Drupal\Core\Session\AccountProxyInterface $account_proxy
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   * @param \Symfony\Component\HttpFoundation\RequestStack $request_stack
   * @param $environment
   */
  public function __construct(AccountProxyInterface $account_proxy, ConfigFactoryInterface $config_factory, RequestStack $request_stack, $environment) {
    parent::__construct(self::ravenOptions());
    $this->environment = $environment;
    $this->name = $config_factory->get('system.site')->get('name');
    $this->site = $request_stack->getCurrentRequest()->getHost();
    $this->user_context([
      'id' => $account_proxy->id(),
      'ip_address' => $request_stack->getCurrentRequest()->getClientIp(),
      'name' => $account_proxy->getAccountName(),
    ]);
  }

  public static function ravenOptions(): array {
    return [
      'curl_method' => 'async',
      'dsn' => getenv('SENTRY_DSN'),
      'processorOptions' => [
        'Raven_SanitizeDataProcessor' => [
          'fields_re' => '/(SESS|pass|authorization|password|passwd|secret|password_confirmation|card_number|auth_pw)/i',
        ],
      ],
    ];
  }

}
