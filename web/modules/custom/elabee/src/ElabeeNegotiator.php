<?php

namespace Drupal\elabee;

use Drupal\Core\Path\PathMatcherInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Theme\ThemeNegotiatorInterface;

/**
 * Class ElabeeNegotiator.
 */
class ElabeeNegotiator implements ThemeNegotiatorInterface {

  /**
   * @var \Drupal\Core\Path\PathMatcherInterface
   */
  protected $pathMatcher;

  /**
   * Constructs a new ElabeeNegotiator object.
   *
   * @param \Drupal\Core\Path\PathMatcherInterface $path_matcher
   *   The path mathcer service.
   */
  public function __construct(PathMatcherInterface $path_matcher) {
    $this->pathMatcher = $path_matcher;
  }

  /**
   * {@inheritdoc}
   */
  public function applies(RouteMatchInterface $route_match): bool {
    return $this->pathMatcher->isFrontPage();
  }

  /**
   * {@inheritdoc}
   */
  public function determineActiveTheme(RouteMatchInterface $route_match): string {
    return 'vibe';
  }

}
