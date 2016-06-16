<?php

/**
 * @file
 * Plugin file to handle field.
 */

$plugin = array(
  'name'       => 'text_long',
  'title'      => t('Text rich'),
  'weight'     => 10,
  'class_name' => 'CommerceAkeneoFieldTextLong',
  'settings'   => array(),
);

/**
 * Class CommerceAkeneoFieldTextLong
 */
class CommerceAkeneoFieldTextLong extends CommerceAkeneoFieldAbstractText {
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
    $migrate->addFieldMapping($field_name . ':format', $field_name . '_format', FALSE)->defaultValue('full_html');

    return $return;
  }
}
