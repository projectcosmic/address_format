<?php

namespace Drupal\Tests\address_format_gb\Functional;

use Drupal\Component\Serialization\Json;
use Drupal\Tests\BrowserTestBase;

/**
 * Tests Great Britain counties in forms.
 */
class AddressFormatGbCountyTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'address',
    'address_format_gb',
    'address_format_gb_test',
  ];

  /**
   * Tests GB country address has a county input.
   */
  public function testGbCountyInput() {
    $this->drupalGet('address-format-gb-test/counties');
    $this->assertSession()->fieldExists('address[administrative_area]')->selectOption('Devon');
    $this->getSession()->getPage()->pressButton('Submit');
    $values = Json::decode($this->getSession()->getPage()->getContent());
    $this->assertEqual($values['address']['administrative_area'], 'Devon');
  }

}
