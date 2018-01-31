<?php

use Drupal\Core\Form\FormStateInterface;

/**
 * Implements hook_form_system_theme_settings_alter().
 *
 * @param $form
 */
function vibe_form_system_theme_settings_alter(&$form) {
  $form['vibe'] = [
    '#type' => 'vertical_tabs',
    '#title' => t('Vibe'),
    '#default_tab' => 'edit-background',
    '#weight' => -1,
  ];
  $form['background'] = [
    '#type' => 'details',
    '#open' => TRUE,
    '#title' => t('Background'),
    '#group' => 'vibe',
    '#tree' => TRUE,
  ];
  $form['background']['path'] = [
    '#type' => 'textfield',
    '#title' => t('Path to custom background'),
    '#default_value' => theme_get_setting('background.path'),
    '#tree' => TRUE,
  ];
  $form['background']['background_upload'] = [
    '#type' => 'file',
    '#title' => t('Uppload background image'),
    '#tree' => FALSE,
  ];

  $form['#validate'][] = 'vibe_form_system_theme_settings_validate';
}

function vibe_form_system_theme_settings_validate(array &$form, FormStateInterface $form_state) {
  if (\Drupal::moduleHandler()->moduleExists('file')) {
    // Handle file uploads.
    $validators = ['file_validate_is_image' => []];

    // Check for a new uploaded background.
    $file = file_save_upload('background_upload', $validators, FALSE, 0);
    if (isset($file)) {
      // File upload was attempted.
      if ($file) {
        // Put the temporary file in form_values so we can save it on submit.
        // $form_state->setValue('background_upload', $file);
        $directory = 'public://vibe';
        file_prepare_directory($directory, FILE_CREATE_DIRECTORY);
        $filename = file_unmanaged_copy($file->getFileUri(), $directory);
        $form_state->setValue('background.path', $filename);
      }
      else {
        // File upload failed.
        $form_state->setError($form['background']['background_upload'], t('The background could not be uploaded.'));
      }
    }
  }

  $form_state->addCleanValueKey('background_upload');
}
