<?php
/**
 * @file
 * Contains Drupal\commerce_akeneo_taxonomy\Plugins\Attribute\CommerceAkeneoAttributePimCatalogProductCategory.
 */
namespace Drupal\commerce_akeneo_taxonomy\Plugins\Attribute;

/**
 * Class CommerceAkeneoAttributePimCatalogProductCategory
 * @package Drupal\commerce_akeneo_taxonomy\Plugins\Attribute
 */
class CommerceAkeneoAttributePimCatalogProductCategory extends CommerceAkeneoAttributePimCatalogMultiSelectAbstract {
  /**
   * Create taxonomy vocabulary if missing.
   *
   * @param string $code
   *   Code.
   * @param string $type
   *   Type.
   *
   * @return string
   *   Vocabulary machine name.
   */
  protected function loadVocabulary($code, $type = 'option') {
    return parent::loadVocabulary($code, 'category');
  }
}
