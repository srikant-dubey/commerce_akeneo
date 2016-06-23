<?php
/**
 * @file
 * Contains Drupal\commerce_akeneo_field\Includes\CommerceAkeneoFieldInterface interface file.
 */
namespace Drupal\commerce_akeneo_field\Includes;

/**
 * Interface CommerceAkeneoFieldInterface
 * @package Drupal\commerce_akeneo_field\Includes
 */
interface CommerceAkeneoFieldInterface {
  /**
   * Map source and destination fields.
   *
   * @param Migration $migrate
   *   Migrate script.
   * @param array     $source_fields
   *   Source fields.
   *
   * @return bool
   *   TRUE if valid.
   */
  public function addFieldMappingToMigrate($migrate, &$source_fields);

  /**
   * Prepare source row to match mapping.
   *
   * @param Migration $migrate
   *   Migrate script.
   * @param string    $row
   *   Source row.
   * @param array     $attributes
   *   Attributes.
   *
   * @return bool
   *   TRUE if valid.
   */
  public function prepareRow($migrate, $row, $attributes);

  /**
   * Prepare destination entity.
   *
   * @param Migration $migrate
   *   Migrate script.
   * @param stdclass  $entity
   *   Destination entity.
   * @param string    $row
   *   Source row.
   * @param array     $attributes
   *   Attributes.
   *
   * @return bool
   *   TRUE if valid.
   */
  public function prepare($migrate, $entity, $row, $attributes);

  /**
   * Complete destination entity process.
   *
   * @param Migration $migrate
   *   Migrate script.
   * @param stdclass  $entity
   *   Destination entity.
   * @param string    $row
   *   Source row.
   * @param array     $attributes
   *   Attributes.
   *
   * @return bool
   *   TRUE if valid.
   */
  public function complete($migrate, $entity, $row, $attributes);
}
