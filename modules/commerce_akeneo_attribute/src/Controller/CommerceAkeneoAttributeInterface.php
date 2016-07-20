<?php
/**
 * @file
 * Drupal\commerce_akeneo_attribute\Controller\CommerceAkeneoAttributeInterface interface file.
 */
namespace Drupal\commerce_akeneo_attribute\Controller;
/**
 * Interface CommerceAkeneoAttributeInterface
 * @package Drupal\commerce_akeneo_attribute\Controller
 */
interface CommerceAkeneoAttributeInterface {
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
   *   Fields details.
   */
  public function getFieldSettings($attribute, $bundle, $field_name, $field_label);
}
