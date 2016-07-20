<?php
/**
 * @file
 * Contians Drupal\commerce_akeneo_field\Plugins\Fields\CommerceAkeneoFieldTextWithSummary.
 */
namespace Drupal\commerce_akeneo_field\Plugins\Fields;
use Drupal\commerce_akeneo_field\Includes\CommerceAkeneoFieldAbstractText;

/**
 * Class CommerceAkeneoFieldCommercePrice
 * @package Drupal\commerce_akeneo_field\Plugins\Fields\CommerceAkeneoFieldTextWithSummary
 */
class CommerceAkeneoFieldTextWithSummary extends CommerceAkeneoFieldAbstractText {
  /**
   * Map source and destination fields.
   *
   * @inheritdoc
   */
  public function addFieldMappingToMigrate($migrate, &$source_fields) {
    $return = parent::addFieldMappingToMigrate($migrate, $source_fields);

    $field_name = $this->field['field_name'];

    // Default value field.
    $source_fields[$field_name . '_format'] = t('@field format', array('@field' => $this->fieldInstance['label']));
    $migrate->addFieldMapping($field_name . ':format', $field_name . '_format')->defaultValue('full_html');

    return $return;
  }
}
