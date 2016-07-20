<?php
/**
 * @file
 * Contains Drupal\commerce_akeneo_attribute\Plugins\Attributes\CommerceAkeneoAttributePimCatalogFile
 */
namespace Drupal\commerce_akeneo_attribute\Plugins\Attributes;

use Drupal\commerce_akeneo_attribute\Controller\CommerceAkeneoAttributeAbstract;

/**
 * Class CommerceAkeneoAttributePimCatalogFile
 * @package Drupal\commerce_akeneo_attribute\Plugins\Attributes
 */
class CommerceAkeneoAttributePimCatalogFile extends CommerceAkeneoAttributeAbstract {
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
      'file',
      'file_generic',
      $field_label,
      $localizable
    );

    // Custom part based on Akeneo information.
    if (count($parameters['allowed_extensions'])) {
      $field_details['field_instance']['file_extensions'] = implode(' ', $parameters['allowed_extensions']);
    }

    return $field_details;
  }
}
