<?php

namespace Drupal\Tests\elabee\Unit\EventSubscriber;

use Drupal\Core\Render\HtmlResponse;
use Drupal\elabee\EventSubscriber\ElabeeEventSubscriber;
use Drupal\Tests\UnitTestCase;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class ElabeeEventSubscriberTest extends UnitTestCase {

  public function testGetSubscribedEvents() {
    self::assertInternalType('array', ElabeeEventSubscriber::getSubscribedEvents());
  }

  public function testFilterResponse() {
    $subcriber = new ElabeeEventSubscriber();

    $response = $this->createMock(HtmlResponse::class);
    $response->headers = $this->createMock(ResponseHeaderBag::class);

    $event = $this->createMock(FilterResponseEvent::class);
    $event->expects(self::any())
      ->method('getResponse')
      ->willReturn($response);

    self::assertNull($subcriber->filterResponse($event));
  }

}
