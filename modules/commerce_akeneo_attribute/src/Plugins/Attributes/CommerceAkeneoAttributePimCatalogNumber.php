<?php
/**
 * @file
 * Contains Drupal\commerce_akeneo_attribute\Plugins\Attributes\CommerceAkeneoAttributePimCatalogNumber
 */
namespace Drupal\commerce_akeneo_attribute\Plugins\Attributes;

use Drupal\commerce_akeneo_attribute\Controller\CommerceAkeneoAttributeAbstract;

/**
 * Class CommerceAkeneoAttributePimCatalogNumber
 * @package Drupal\commerce_akeneo_attribute\Plugins\Attributes
 */
class CommerceAkeneoAttributePimCatalogNumber extends CommerceAkeneoAttributeAbstract {
  /**
   * Perform matching for Akeneo attribute type with Drupal fields.
   *
   * @param array  $attribute
   *   Attribute.
   * @param string $bundle
   *   Bundle.
   * @param string $field_name
   *   Field name.
   * @param string $field_label
   *   Field label.
   *
   * @return array|bool
   *   Field details.
   */
  public function getFieldSettings($attribute, $bundle, $field_name, $field_label) {
    $parameters     = isset($attribute['settings']['parameters']) ? $attribute['settings']['parameters'] : array();
    $localizable    = !empty($parameters['localizable']);
    $allow_decimale = isset($parameters['allow_decimale']) ? $parameters['allow_decimale'] : FALSE;
    $type           = $allow_decimale ? 'number_decimale' : 'number_integer';

    // Generic initialization.
    $field_details = $this->prepareFieldSettings(
      $field_name,
      $bundle,
      $type,
      'number',
      $field_label,
      $localizable
    );

    // Custom part based on Akeneo information.
    $field_details['field_instance']['settings']['min'] = isset($parameters['min_value']) ? $parameters['min_value'] : '';
    $field_details['field_instance']['settings']['max'] = isset($parameters['max_value']) ? $parameters['max_value'] : '';

    $field_details['field_instance']['default'] = array(
      array('value' => isset($parameters['default_value']) ? $parameters['default_value'] : ''),
    );

    return $field_details;
  }
}
