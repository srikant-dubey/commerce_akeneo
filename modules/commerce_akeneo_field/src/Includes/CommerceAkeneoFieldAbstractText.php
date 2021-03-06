<?php
/**
 * @file
 * Contains Drupal\commerce_akeneo_field\Includes\CommerceAkeneoFieldAbstractText
 */
namespace Drupal\commerce_akeneo_field\Includes;
use Drupal\commerce_akeneo_field\Includes\CommerceAkeneoFieldAbstract;

/**
 * Class CommerceAkeneoFieldAbstractText
 * @package Drupal\commerce_akeneo_field\Includes
 */
class CommerceAkeneoFieldAbstractText extends CommerceAkeneoFieldAbstract {
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
    $source_fields[$field_name . '_format'] = t('@field format', array('@field' => $this->fieldInstance['label']));
    $migrate->addFieldMapping($field_name . ':format', $field_name . '_format')->defaultValue('');

    // Settings.

    // Support for translations.
    if ($this->field['translatable']) {
      $source_fields[$field_name . '_languages'] = t(
        '@field languages',
        array('@field' => $this->fieldInstance['label'])
      );
      $migrate->addFieldMapping($field_name . ':language', $field_name . '_languages')->defaultValue(array());
    }

    return TRUE;
  }

  /**
   * Prepare source row to match mapping.
   *
   * @inheritdoc
   */
  public function prepareRow($migrate, $row, $attributes) {
    $field_name = $this->field['field_name'];

    $properties = array(
      $field_name . '_value'     => array(),
      $field_name . '_languages' => array(),
    );

    foreach ($attributes as $code => $attribute) {
      if (isset($row->values[$code])) {
        foreach ($row->values[$code] as $locale => $values) {
          $language = ($this->field['translatable'] ?
            $language = commerce_akeneo_locale_to_language($locale) :
            LANGUAGE_NONE
          );

          foreach ($values as $value) {
            $properties[$field_name . '_value'][$language][] = $value['value'];
          }

          $properties[$field_name . '_languages'][$language] = $language;
        }
      }
    }

    $this->formatPrepareRowProperties($field_name, $row, $properties);

    return TRUE;
  }
}
