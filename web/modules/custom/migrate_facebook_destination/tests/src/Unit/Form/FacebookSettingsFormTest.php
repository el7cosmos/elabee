<?php

namespace Drupal\Tests\migrate_facebook_destination\Unit\Form;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\Form\FormStateInterface;
use Drupal\migrate_facebook_destination\Form\FacebookSettingsForm;
use Drupal\Tests\UnitTestCase;

class FacebookSettingsFormTest extends UnitTestCase {

  /**
   * @var \Drupal\migrate_facebook_destination\Form\FacebookSettingsForm
   */
  protected $formObject;

  /**
   * @var \Drupal\Core\Form\FormStateInterface
   */
  protected $formState;

  public function testGetFormId() {
    self::assertEquals('facebook_settings_form', $this->formObject->getFormId());
  }

  public function testBuildForm() {
    $subset = ['app_id', 'app_secret', 'default_access_token'];
    self::assertArraySubset($subset, array_keys($this->formObject->buildForm([], $this->formState)));
  }

  public function testValidateForm() {
    $form = [];
    self::assertNull($this->formObject->validateForm($form, $this->formState));
  }

  public function testSubmitForm() {
    $form = [];
    self::assertNull($this->formObject->submitForm($form, $this->formState));
  }

  protected function setUp() {
    parent::setUp();

    $config_factory = $this->getConfigFactoryStub([
      'migrate_facebook_destination.settings' => [],
    ]);
    /** @var \PHPUnit_Framework_MockObject_MockObject $config */
    $config = $config_factory->getEditable('migrate_facebook_destination.settings');
    $config->expects(self::any())
      ->method('set')
      ->willReturnSelf();

    $container = new ContainerBuilder();
    $container->set('config.factory', $config_factory);
    $container->set('string_translation', $this->getStringTranslationStub());
    \Drupal::setContainer($container);

    $this->formObject = FacebookSettingsForm::create($container);
    $this->formState = $this->createMock(FormStateInterface::class);
    $this->formState->expects(self::any())
      ->method('getValues')
      ->willReturn([
        'app_id' => 'app_id',
        'app_secret' => 'app_secret',
        'default_access_token' => 'default_access_token',
      ]);
  }

}

namespace Drupal\Core\Form;

if (!function_exists('drupal_set_message')) {
  function drupal_set_message() {
  }
}
