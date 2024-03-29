<?php declare(strict_types=1);

namespace Drupal\alexa_smapi\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

final class SettingsForm extends ConfigFormBase
{
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'alexa_smapi_config';
  }

  /**
   * {@inheritDoc}
   */
  protected function getEditableConfigNames() {
    return [
      'alexa_smapi.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['container'] = [
      '#type' => 'container',
      '#attributes' => ['id' => 'alexa-smapi-config-container'],
    ];

    $form['container']['api_base_url'] = [
      '#type' => 'url',
      '#title' => $this->t('API Base Url'),
      '#attributes' => ['placeholder' => 'https://api.amazonalexa.com/v1'],
      '#default_value' => $this->config('alexa_smapi.settings')->get('api_base_url') ?? 'https://api.amazonalexa.com/v1',
    ];

    $form['container']['skill_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Skill Id'),
      '#attributes' => ['placeholder' => 'amzn1.ask.skill.[...]'],
      '#default_value' => $this->config('alexa_smapi.settings')->get('skill_id'),
    ];

    $form['container']['stage'] = [
      '#type' => 'textfield', # todo: make this a select
      '#title' => $this->t('Stage'),
      '#attributes' => ['placeholder' => 'development'],
      '#default_value' => $this->config('alexa_smapi.settings')->get('stage') ?? 'development',
    ];

    $form['container']['oauth_token'] = [
      '#type' => 'textarea',
      '#title' => $this->t('OAuth Token'),
      '#attributes' => ['placeholder' => 'Atza|[...]'],
      '#default_value' => $this->config('alexa_smapi.settings')->get('oauth_token'),
      '#description' => $this->t('Run <strong>make token</strong> in <strong>omissis/php-ask-sdk</strong> to obtain a token and paste it here.'),
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->config('alexa_smapi.settings')
      ->set('api_base_url', $form_state->getValue('api_base_url'))
      ->set('skill_id', $form_state->getValue('skill_id'))
      ->set('stage', $form_state->getValue('stage'))
      ->set('oauth_token', $form_state->getValue('oauth_token'))
      ->save();
  }
}
