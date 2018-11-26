<?php

namespace Drupal\address_format\Plugin\Field\FieldFormatter;

use Drupal\address\AddressInterface;
use Drupal\address\Plugin\Field\FieldFormatter\AddressDefaultFormatter;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;

/**
 * Plugin implementation of the 'custom_format' formatter.
 *
 * @FieldFormatter(
 *   id = "custom_format",
 *   label = @Translation("Custom Format"),
 *   field_types = {
 *     "address",
 *   },
 * )
 */
class CustomFormatAddressFormatter extends AddressDefaultFormatter {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return parent::defaultSettings() + [
      'format' => "%givenName %familyName\n%organization\n%addressLine1, %addressLine2\n%locality, %administrativeArea, %postalCode",
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $tokens = [
      '%givenName: ' . $this->t('First name'),
      '%familyName: ' . $this->t('Surname'),
      '%organization: ' . $this->t('Company'),
      '%addressLine1: ' . $this->t('Address Line 1'),
      '%addressLine2: ' . $this->t('Address Line 2'),
      '%locality: ' . $this->t('Town/City'),
      '%administrativeArea: ' . $this->t('County/State'),
      '%postalCode: ' . $this->t('Postcode'),
      '%country: ' . $this->t('Country'),
    ];

    return parent::settingsForm($form, $form_state) + [
      'format' => [
        '#type' => 'textarea',
        '#title' => $this->t('Address Format'),
        '#default_value' => $this->getSetting('format'),
        '#required' => TRUE,
        '#description' => [
          [
            '#prefix' => '<p>',
            '#markup' => $this->t('Specify a custom display format for addresses. The following tokens are available.'),
            '#suffix' => '</p>',
          ],
          [
            '#theme' => 'item_list',
            '#items' => $tokens,
          ],
        ]
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function viewElement(AddressInterface $address, $langcode) {
    $element = parent::viewElement($address, $langcode);

    // Reduce span tags around address components.
    foreach (Element::getVisibleChildren($element) as $key) {
      $field = $element[$key];

      $element[$key] = [
        '#plain_text' => $field['#value'],
        '#placeholder' => $field['#placeholder'],
      ];
    }

    $element['#display_format'] = $this->getSetting('format');

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public static function postRender($content, array $element) {
    $format_string = $element['#display_format'];

    $replacements = [];
    foreach (Element::getVisibleChildren($element) as $key) {
      $child = $element[$key];
      if (isset($child['#placeholder'])) {
        $replacements[$child['#placeholder']] = $child['#markup'] ?: '';
      }
    }
    $content = self::replacePlaceholders($format_string, $replacements);

    // Remove leading and multiple commas.
    $content = preg_replace(
      ['/^(?:, )+/', '/(?:, ){2,}/'],
      ['', ', '],
      $content
    );

    return nl2br($content, FALSE);
  }

}
