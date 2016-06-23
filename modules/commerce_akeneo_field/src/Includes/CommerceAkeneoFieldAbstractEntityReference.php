<?php

/**
 * @file
 * Contains Drupal\commerce_akeneo_field\Includes\CommereceAkeneoFieldAbstractEntityReffrence.
 */
namespace Drupal\commerce_akeneo_field\Includes;
use Drupal\commerce_akeneo_field\Includes\CommerceAkeneoFieldAbstract;

/**
 * Class CommerceAkeneoFieldAbstractEntityReference
 * @package Drupal\commerce_akeneo_field\Includes
 */
class CommerceAkeneoFieldAbstractEntityReference extends CommerceAkeneoFieldAbstract {
  /**
   * Map source and destination fields.
   *
   * @inheritdoc
   */
  public function addFieldMappingToMigrate($migrate, &$source_fields) {
    $field_name = $this->field['field_name'];

    // Default value field.
    $source_fields[$field_name . '_value'] = t('@field value', array('@field' => $this->fieldInstance['label']));
    $migrate->addFieldMapping($field_name, $field_name . '_value');

    return TRUE;
  }
}
