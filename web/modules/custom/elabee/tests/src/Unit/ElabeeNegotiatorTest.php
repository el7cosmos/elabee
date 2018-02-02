<?php

namespace Drupal\Tests\elabee\Unit;

use Drupal\Core\Path\PathMatcherInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\elabee\ElabeeNegotiator;
use Drupal\Tests\UnitTestCase;

class ElabeeNegotiatorTest extends UnitTestCase {

  /**
   * @var \Drupal\Core\Path\PathMatcherInterface
   */
  protected $pathMatcher;

  /**
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  public static function appliesProvider() {
    return [
      [FALSE],
      [TRUE],
    ];
  }

  /**
   * @param bool $case
   *
   * @dataProvider appliesProvider
   */
  public function testApplies(bool $case) {
    $this->pathMatcher->expects(self::any())
      ->method('isFrontPage')
      ->willReturn($case);

    $negotiator = new ElabeeNegotiator($this->pathMatcher);
    self::assertEquals($case, $negotiator->applies($this->routeMatch));
  }

  public function testDetermineActiveTheme() {
    $negotiator = new ElabeeNegotiator($this->pathMatcher);
    self::assertInternalType('string', $negotiator->determineActiveTheme($this->routeMatch));
  }

  protected function setUp() {
    parent::setUp();

    $this->pathMatcher = $this->createMock(PathMatcherInterface::class);
    $this->routeMatch = $this->createMock(RouteMatchInterface::class);
  }

}
