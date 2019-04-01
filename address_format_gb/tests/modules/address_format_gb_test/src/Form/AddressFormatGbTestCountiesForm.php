<?php

namespace Drupal\address_format_gb_test\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use CommerceGuys\Addressing\AddressFormat\AddressField;
use CommerceGuys\Addressing\AddressFormat\FieldOverride;

/**
 * Form constructor to test Great Britain counties form element.
 *
 * @internal
 */
class AddressFormatGbTestCountiesForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'address_format_gb_test_counties';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['address'] = [
      '#type' => 'address',
      '#title' => 'Address',
      '#default_value' => [
        'country_code' => 'GB',
      ],
      '#field_overrides' => [],
    ];

    // Hide all but administrative area field.
    foreach (AddressField::getAll() as $field) {
      if ($field == AddressField::ADMINISTRATIVE_AREA) {
        continue;
      }

      $form['address']['#field_overrides'][$field] = FieldOverride::HIDDEN;
    }

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => 'Submit',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $form_state->setResponse(new JsonResponse($form_state->getValues()));
  }

}
