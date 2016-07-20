<?php
/**
 * @file
 * Contians Drupal\commerce_akeneo_field\Plugins\Fields\CommerceAkeneoFieldImage.
 */
namespace Drupal\commerce_akeneo_field\Plugins\Fields;

use Drupal\commerce_akeneo_field\Includes\CommerceAkeneoFieldAbstract;
use Drupal\commerce_akeneo\Controller\CommerceAkeneo;
use Drupal\Component\Utility\Unicode;

/**
 * Class CommerceAkeneoFieldCommercePrice
 * @package Drupal\commerce_akeneo_field\Plugins\Fields\CommerceAkeneoFieldImage
 */
class CommerceAkeneoFieldImage extends CommerceAkeneoFieldAbstract {
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

    // Settings.
    $migrate->addFieldMapping($field_name . ':file_class')->defaultValue(NULL);
    $migrate->addFieldMapping($field_name . ':preserve_files')->defaultValue(TRUE);
    $migrate->addFieldMapping($field_name . ':file_replace')->defaultValue(FILE_EXISTS_REPLACE);
    $migrate->addFieldMapping($field_name . ':source_dir')->defaultValue(NULL);
    $migrate->addFieldMapping($field_name . ':urlencode')->defaultValue(TRUE);

    $uri_scheme      = $this->field['settings']['uri_scheme'];
    $file_directory  = $this->fieldInstance['settings']['file_directory'];
    $destination_dir = file_stream_wrapper_uri_normalize($uri_scheme . '://' . $file_directory);
    $migrate->addFieldMapping($field_name . ':destination_dir')->defaultValue($destination_dir);

    $source_fields[$field_name . '_destination_file'] = t(
      '@field destination file',
      array('@field' => $this->fieldInstance['label'])
    );
    $migrate->addFieldMapping($field_name . ':destination_file', $field_name . '_destination_file')->defaultValue(NULL);

    $source_fields[$field_name . '_alt'] = t('@field alt', array('@field' => $this->fieldInstance['label']));
    $migrate->addFieldMapping($field_name . ':alt', $field_name . '_alt')->defaultValue(NULL);

    $source_fields[$field_name . '_title'] = t('@field title', array('@field' => $this->fieldInstance['label']));
    $migrate->addFieldMapping($field_name . ':title', $field_name . '_title')->defaultValue(NULL);

    // Support for translations.
    $source_fields[$field_name . '_languages'] = t(
      '@field languages',
      array('@field' => $this->fieldInstance['label'])
    );

    $migrate->addFieldMapping($field_name . ':language', $field_name . '_languages')->defaultValue(array());

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
      $field_name . '_value'            => array(),
      $field_name . '_destination_file' => array(),
      $field_name . '_alt'              => array(),
      $field_name . '_title'            => array(),
      $field_name . '_languages'        => array(),
    );

    foreach ($attributes as $code => $attribute) {
      if (isset($row->values[$code])) {
        foreach ($row->values[$code] as $locale => $values) {
          $language = ($this->field['translatable'] ?
            $language = CommerceAkeneo::commerce_akeneo_locale_to_language($locale) :
            LANGUAGE_NONE
          );

          foreach ($values as $value) {
            switch ($attribute['type']) {
              case 'pim_catalog_file':
              case 'pim_catalog_image':
                // Change url protocol to enable Akeneo stream wrapper.
                $url = 'akeneo://' . ltrim($value['filename'], '/');

                $original_filename = Unicode::strtolower($value['filename_original']);
                $filename = pathinfo($original_filename, PATHINFO_FILENAME);

                $destination_file = 'akeneo/';
                $destination_file .= $value['attribute_id'] . '/';
                $destination_file .= implode('/', str_split(substr($filename, 0, 3)));
                $destination_file .= '/' . $original_filename;

                $properties[$field_name . '_value'][$language][]            = $url;
                $properties[$field_name . '_destination_file'][$language][] = $destination_file;
                $properties[$field_name . '_alt'][$language][]              = $original_filename;
                $properties[$field_name . '_title'][$language][]            = $original_filename;
                break;

              default:
                // Not supported.
            }
          }

          $properties[$field_name . '_languages'][$language] = $language;
        }
      }
    }

    $this->formatPrepareRowProperties($field_name, $row, $properties);

    return TRUE;
  }
}
