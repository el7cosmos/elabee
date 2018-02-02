<?php

namespace Drupal\elabee;

use GuzzleHttp\ClientInterface;
use Http\Adapter\Guzzle6\Client;
use Mailgun\Mailgun as MailgunBase;

/**
 * Mailgun service.
 */
class Mailgun extends MailgunBase {

  /**
   * Constructs a Mailgun object.
   *
   * @param \GuzzleHttp\ClientInterface $client
   */
  public function __construct(ClientInterface $client) {
    $http_client = new Client($client);
    parent::__construct(getenv('MAILGUN_API_KEY'), $http_client);
  }

}
