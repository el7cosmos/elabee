<?php

namespace Drupal\migrate_facebook_destination\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Facebook\Facebook;

/**
 * Class FacebookSettingsForm.
 */
class FacebookSettingsForm extends ConfigFormBase {

  const CONFIG_NAME = 'migrate_facebook_destination.settings';

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      self::CONFIG_NAME,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'facebook_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config(self::CONFIG_NAME);
    $form['app_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Application ID'),
      '#maxlength' => 64,
      '#default_value' => $config->get('app_id'),
      '#required' => TRUE,
    ];
    $form['app_secret'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Application secret'),
      '#maxlength' => 64,
      '#default_value' => $config->get('app_secret'),
      '#required' => TRUE,
    ];
    $form['default_access_token'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Default access token'),
      '#maxlength' => 255,
      '#default_value' => $config->get('default_access_token'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    $values = $form_state->getValues();

    $fb = new Facebook([
      'app_id' => $values['app_id'],
      'app_secret' => $values['app_secret'],
      'default_access_token' => $values['default_access_token'],
    ]);

    try {
      $fb->get($form_state->getValue('app_id'));
    }
    catch (\Exception $exception) {
      $form_state->setError($form, $exception->getMessage());
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->config(self::CONFIG_NAME)
      ->set('app_id', $form_state->getValue('app_id'))
      ->set('app_secret', $form_state->getValue('app_secret'))
      ->set('default_access_token', $form_state->getValue('default_access_token'))
      ->save();
  }

}
