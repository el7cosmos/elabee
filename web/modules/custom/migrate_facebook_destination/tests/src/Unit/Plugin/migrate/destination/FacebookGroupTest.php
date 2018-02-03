<?php

namespace Drupal\Tests\migrate_facebook_destination\Unit\Plugin\migrate\destination;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\Row;
use Drupal\migrate_facebook_destination\Plugin\migrate\destination\FacebookGroup;
use Drupal\Tests\UnitTestCase;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;
use Facebook\FacebookResponse;
use Facebook\GraphNodes\GraphNode;

/**
 * Class FacebookGroupTest
 *
 * @package Drupal\Tests\migrate_facebook_destination\Unit\Plugin\migrate\destination
 *
 * @coversDefaultClass \Drupal\migrate_facebook_destination\Plugin\migrate\destination\FacebookGroup
 */
class FacebookGroupTest extends UnitTestCase {

  const CONFIGURATION = [
    'facebook' => [
      'group_id' => 'group_id',
    ],
  ];

  /**
   * @var \Drupal\migrate\Plugin\MigrateDestinationInterface
   */
  protected $destination;

  /**
   * @var \Drupal\migrate\Plugin\MigrationInterface
   */
  protected $migration;

  public static function importProvider() {
    return [
      [FALSE],
      [[NULL]],
    ];
  }

  /**
   * @covers ::fields
   */
  public function testFields() {
    $fields = $this->destination->fields($this->migration);
    self::assertInternalType('array', $fields);
    self::assertArrayHasKey('message', $fields);
    self::assertArrayHasKey('link', $fields);
  }

  /**
   * @covers ::import
   *
   * @dataProvider importProvider
   *
   * @param $case
   */
  public function testImport($case) {
    $row = $this->createMock(Row::class);
    if (!$case) {
      $row->expects(self::any())
        ->method('getDestinationProperty')
        ->willThrowException(new FacebookSDKException());
    }
    self::assertEquals($case, $this->destination->import($row, [$case]));
  }

  /**
   * @covers ::rollback
   */
  public function testRollback() {
    self::assertNull($this->destination->rollback([]));
  }

  protected function setUp() {
    parent::setUp();

    $node = $this->createMock(GraphNode::class);

    $response = $this->createMock(FacebookResponse::class);
    $response->expects(self::any())
      ->method('getGraphNode')
      ->willReturn($node);

    $facebook = $this->createMock(Facebook::class);
    $facebook->expects(self::any())
      ->method('post')
      ->willReturn($response);

    $container = new ContainerBuilder();
    $container->set('facebook.client', $facebook);
    $container->set('string_translation', $this->getStringTranslationStub());
    \Drupal::setContainer($container);

    $this->migration = $this->createMock(MigrationInterface::class);
    $this->destination = FacebookGroup::create($container, self::CONFIGURATION, '', '', $this->migration);
  }

}
