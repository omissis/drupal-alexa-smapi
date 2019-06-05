<?php declare(strict_types=1);

namespace Drupal\alexa_smapi\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
//use Omissis\AlexaSdk\Sdk;

/**
 * Implements the skill update form.
 */
class SkillUpdateForm extends FormBase
{

  /**
   * {@inheritdoc}
   */
  public function getFormId()
  {
    return 'alexa_smapi_skill_update_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state)
  {
    $products = \Drupal::entityTypeManager()
      ->getStorage('commerce_product')
      ->loadByProperties();

    $header = [
      'id' => t('Id'),
      'product' => t('Product'),
      'price' => t('Price'),
    ];

    $form['mytable'] = [
      '#type' => 'table',
      '#header' => $header,
      '#empty' => t('No Products found'),
      // TableSelect: Injects a first column containing the selection widget into
      // each table row.
      // Note that you also need to set #tableselect on each form submit button
      // that relies on non-empty selection values (see below).
      '#tableselect' => TRUE,
    ];

    foreach ($products as $id => $product) {
      $form['mytable'][$id]['id'] = [
        '#plain_text' => $product->id(),
      ];
      $form['mytable'][$id]['product'] = [
        '#plain_text' => $product->label(),
      ];
      $form['mytable'][$id]['price'] = [
        '#plain_text' => 123.45,
      ];
    }

    $form['actions'] = ['#type' => 'actions'];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => t('Update Slot Values'),
      // TableSelect: Enable the built-in form validation for #tableselect for
      // this form button, so as to ensure that the bulk operations form cannot
      // be submitted without any selected items.
      '#tableselect' => TRUE,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state)
  {
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    $config = \Drupal::config('alexa_smapi.settings');
    $skillId = $config->get('skill_id');
    $stage = $config->get('stage');

    $sdk = \Drupal::service('omissis.alexa_sdk.sdk');

    $interactionModelSchema = $sdk->getInteractionModelSchema($skillId, $stage, 'en-US');

    $languageModel = $interactionModelSchema->getInteractionModel()->getLanguageModel();

    $values = [];
    foreach ([] as $todo) {
      $values[] = new Value(new Value\Name('product ' . get_random_string()), new Value\Id((string) random_int(0, 1000)));
    }

    $this->getProductNameType($languageModel)->setValues($values);

    $sdk->updateInteractionModelSchema(
      $skillId,
      $stage,
      'en-US',
      new UpdateInteractionModelSchema($interactionModelSchema->getInteractionModel(), new Description('foobar'))
    );
  }

  /**
   * @throws \LogicException
   */
  private function getProductNameType(LanguageModel $languageModel): ?Type
  {
    foreach ($languageModel->getTypes() as $type) {
      if ($type->getName() === 'ProductName') {
        return $type;
      }
    }

    throw new \LogicException('No ProductName Type was found in the skill');
  }
}
