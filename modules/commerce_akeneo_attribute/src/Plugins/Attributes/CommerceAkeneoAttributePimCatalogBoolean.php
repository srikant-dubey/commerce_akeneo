<?php
/**
 * @file
 * Contains Drupal\commerce_akeneo_attribute\Plugin\Attributes\CommerceAkeneoAttributePimCatalogBoolean.
 */
namespace Drupal\commerce_akeneo_attribute\Plugins\Attributes;

use Drupal\commerce_akeneo_attribute\Controller\CommerceAkeneoAttributeAbstract;

/**
 * Class CommerceAkeneoAttributePimCatalogBoolean
 * @package Drupal\commerce_akeneo_attribute\Plugin\Attributes
 */
class CommerceAkeneoAttributePimCatalogBoolean extends CommerceAkeneoAttributeAbstract {
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
    $parameters  = isset($attribute['settings']['parameters']) ? $attribute['settings']['parameters'] : array();
    $localizable = !empty($parameters['localizable']);

    // Generic initialization.
    $field_details = $this->prepareFieldSettings(
      $field_name,
      $bundle,
      'list_boolean',
      'options_onoff',
      $field_label,
      $localizable
    );

    // Custom part based on Akeneo information.
    $field_details['field_instance']['widget']['settings']['display_label']  = TRUE;
    $field_details['field_instance']['widget']['settings']['allowed_values'] = array(
      0 => '',
      1 => $field_label,
    );

    return $field_details;
  }
}
