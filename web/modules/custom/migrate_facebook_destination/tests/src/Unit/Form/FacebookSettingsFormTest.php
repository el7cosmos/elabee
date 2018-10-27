<?php

namespace Drupal\Tests\migrate_facebook_destination\Unit\Form;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\migrate_facebook_destination\Form\FacebookSettingsForm;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\migrate_facebook_destination\Form\FacebookSettingsForm
 * @covers ::<!public>
 */
class FacebookSettingsFormTest extends UnitTestCase {

  /**
   * @var \Drupal\migrate_facebook_destination\Form\FacebookSettingsForm
   */
  protected $formObject;

  /**
   * @var \Drupal\Core\Form\FormStateInterface
   */
  protected $formState;

  /**
   * @covers ::getFormId
   */
  public function testGetFormId(): void {
    self::assertEquals('facebook_settings_form', $this->formObject->getFormId());
  }

  /**
   * @covers ::buildForm
   */
  public function testBuildForm(): void {
    $subset = ['app_id', 'app_secret', 'default_access_token'];
    self::assertArraySubset($subset, array_keys($this->formObject->buildForm([], $this->formState)));
  }

  /**
   * @covers ::validateForm
   */
  public function testValidateForm(): void {
    $form = [];
    self::assertNull($this->formObject->validateForm($form, $this->formState));
  }

  /**
   * @covers ::submitForm
   */
  public function testSubmitForm(): void {
    $this->formObject->setMessenger($this->prophesize(MessengerInterface::class)->reveal());
    $form = [];
    self::assertNull($this->formObject->submitForm($form, $this->formState));
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    /** @var \Drupal\Core\Config\ConfigFactoryInterface $config_factory */
    $config_factory = $this->getConfigFactoryStub([
      'migrate_facebook_destination.settings' => [],
    ]);
    /** @var \PHPUnit_Framework_MockObject_MockObject $config */
    $config = $config_factory->getEditable('migrate_facebook_destination.settings');
    $config->method('set')->willReturnSelf();

    $container = new ContainerBuilder();
    $container->set('config.factory', $config_factory);
    $container->set('string_translation', $this->getStringTranslationStub());
    \Drupal::setContainer($container);

    $this->formObject = FacebookSettingsForm::create($container);
    $this->formState = $this->createMock(FormStateInterface::class);
    $this->formState->method('getValues')->willReturn([
      'app_id' => 'app_id',
      'app_secret' => 'app_secret',
      'default_access_token' => 'default_access_token',
    ]);
  }

}
