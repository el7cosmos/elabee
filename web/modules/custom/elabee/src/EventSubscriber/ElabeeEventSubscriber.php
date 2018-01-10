<?php

namespace Drupal\elabee\EventSubscriber;

use Drupal\Core\Render\HtmlResponse;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class ElabeeEventSubscriber.
 */
class ElabeeEventSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[KernelEvents::RESPONSE] = 'filterResponse';

    return $events;
  }

  public function filterResponse(FilterResponseEvent $event) {
    $reponse = $event->getResponse();
    if ($reponse instanceof HtmlResponse) {
      $reponse->headers->set('Content-Security-Policy', "script-src 'self'; object-src 'self'");
      $reponse->headers->set('X-Frame-Options', 'DENY');
      $reponse->headers->set('X-XSS-Protection', '1; mode=block');
    }
  }

}
