<?php

namespace Drupal\Tests\address_format\Kernel;

use Drupal\entity_test\Entity\EntityTest;
use Drupal\Tests\address\Kernel\Formatter\FormatterTestBase;

/**
 * Tests the custom_format formatter.
 *
 * @group custom_format
 */
class CustomFormatAddressFormatterTest extends FormatterTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = ['address_format'];

  /**
   * Address test fixture.
   *
   * @var string[]
   */
  protected $address = [
    'given_name' => 'Joe',
    'family_name' => 'Blogs',
    'organization' => 'Access Training(SW) Ltd',
    'address_line1' => '7 Tregarne Terrace',
    'address_line2' => 'Address Line 2',
    'locality' => 'St Austell',
    'postal_code' => 'PL25 4BE',
    'country_code' => 'GB',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->createField('address', 'custom_format');
  }

  /**
   * Tests default address format setting.
   */
  public function testDefaultFormat() {
    $entity = EntityTest::create([]);
    $entity->{$this->fieldName} = $this->address;

    $this->renderEntityFields($entity, $this->display);

    $expected = '<p class="address" translate="no">Joe Blogs<br/>Access Training(SW) Ltd<br/>7 Tregarne Terrace, Address Line 2<br/>St Austell, PL25 4BE</p>';
    $this->assertRaw($expected, 'Default address format setting is properly formatted.');
  }

  /**
   * Tests that non-value placeholders do not exist in final output.
   */
  public function testNonValuePlaceholders() {
    $this->display->setComponent($this->fieldName, [
      'type' => 'custom_format',
      'settings' => [
        'format' => '%givenName %nonExistingPlaceholder',
      ],
    ]);

    $entity = EntityTest::create([]);
    $entity->{$this->fieldName} = $this->address;

    $this->renderEntityFields($entity, $this->display);

    $expected = '<p class="address" translate="no">Joe </p>';
    $this->assertRaw($expected, 'Non-value placeholders should not exist in final output.');
  }

  /**
   * Tests that no double formatting marks are present.
   */
  public function testDoubleMarkings() {
    $this->display->setComponent($this->fieldName, [
      'type' => 'custom_format',
      'settings' => [
        'format' => '%givenName %familyName
%organization
%addressLine1, %addressLine2, %locality,
%postalCode, %country',
      ],
    ]);

    $entity = EntityTest::create([]);

    $entity->{$this->fieldName} = [
      'given_name' => '',
      'family_name' => '',
      'organization' => '',
    ] + $this->address;
    $this->renderEntityFields($entity, $this->display);

    $expected = '<p class="address" translate="no">7 Tregarne Terrace, Address Line 2, St Austell,<br/>PL25 4BE, United Kingdom</p>';
    $this->assertRaw($expected, 'Multiple line breaks should be coalesced into a single line break.');

    $entity->{$this->fieldName} = [
      'address_line2' => '',
      'locality' => '',
    ] + $this->address;
    $this->renderEntityFields($entity, $this->display);

    $expected = '<p class="address" translate="no">Joe Blogs<br/>Access Training(SW) Ltd<br/>7 Tregarne Terrace, <br/>PL25 4BE, United Kingdom</p>';
    $this->assertRaw($expected, 'Multiple commas should be coalesced into a single line comma.');

    $entity->{$this->fieldName} = [
      'address_line1' => '',
      'address_line2' => '',
    ] + $this->address;
    $this->renderEntityFields($entity, $this->display);

    $expected = '<p class="address" translate="no">Joe Blogs<br/>Access Training(SW) Ltd<br/>St Austell,<br/>PL25 4BE, United Kingdom</p>';
    $this->assertRaw($expected, 'Commas at the start of the line should be removed.');
  }

  /**
   * Test markup escaping.
   *
   * @dataProvider markupEscapingProvider
   */
  public function testMarkupEscaping($address, $expected) {
    $entity = EntityTest::create([]);
    $entity->{$this->fieldName} = $address;

    $build = $this->display->build($entity);
    $output = $this->container->get('renderer')->renderPlain($build);
    $this->setRawContent($output);

    $this->assertRaw(sprintf('<p class="address" translate="no">%s</p>', $expected));
  }

  /**
   * Provides test cases for markup escaping tests.
   */
  public function markupEscapingProvider() {
    return [
      'HTML entity' => [
        [
          'organization' => 'Jack & Jill',
          'country_code' => 'GB',
        ],
        'Jack &amp; Jill',
      ],
      'HTML tag' => [
        [
          'organization' => '<script>Foo</script> Bar Baz',
          'country_code' => 'GB',
        ],
        '&lt;script&gt;Foo&lt;/script&gt; Bar Baz',
      ],
    ];
  }

}
