<?php

namespace Drupal\Tests\elabee\Unit;

use Drupal\elabee\Mailgun;
use Drupal\Tests\UnitTestCase;
use GuzzleHttp\ClientInterface;
use Mailgun\Mailgun as MailgunBase;

class MailgunTest extends UnitTestCase {

  public function testMailgun() {
    $mailgun = new Mailgun($this->createMock(ClientInterface::class));
    self::assertInstanceOf(MailgunBase::class, $mailgun);
  }

}
