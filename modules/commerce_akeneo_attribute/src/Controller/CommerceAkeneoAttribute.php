<?php 
/**
 * @file
 * Contains Drupal\commerce_akeneo_attribute\Controller\CommerceAkeneoAttribute
 */
namespace Drupal\commerce_akeneo_attribute\Controller;

use Drupal\Component\Utility\Unicode;
/**
 * Class CommerceAkeneoController
 * @package Drupal\commerce_akeneo_attribute\Controller
 */

 class CommerceAkeneoAttribute {
 	/**
	 * Expires data from the cache for attribute mapping.
	 */
	public function commerce_akeneo_attribute_cache_clear() {
	  cache_clear_all('commerce_akeneo:attribute:mapping');
	}

	/**
	 * List mapping fields vs attributes.
	 *
	 * @param bool $refresh
	 *   Force refresh.
	 *
	 * @return array
	 *   Mappings.
	 */
	public function commerce_akeneo_attribute_mapping($refresh = FALSE) {
	  $cid = 'commerce_akeneo:attribute:mapping';

	  if (($cache = \Drupal::cache()->get($cid)) && !$refresh) {
	    //return $cache->data;
	  }

	  $attributes = \Drupal::moduleHandler()->invokeAll('commerce_akeneo_attribute_mapping_info');
	 
	  // Add default properties.
	  foreach ($attributes as $key => $attribute) {
	    $attributes[$key] += array(
	      'names'        => array(),
	      'section'      => 'attribute',
	      'skip'         => FALSE,
	      'match'        => FALSE,
	      'type'         => NULL,
	      'field_name'   => NULL,
	      'field_locked' => FALSE,
	    );
	  }

	  \Drupal::moduleHandler()->alter('commerce_akeneo_attribute_mapping_info', $attributes);

	  \Drupal::cache()->set($cid, $attributes);

	  return $attributes;
	}

	/**
	 * Dispatch source attributes by destination fields.
	 *
	 * @param array $attributes
	 *   Attributes.
	 *
	 * @return array
	 *   Attributes by fields.
	 * @throws \Exception
	 */
	public function commerce_akeneo_attribute_dispatch_by_fields($attributes) {
	  $fields   = array();
	  $mappings = $this->commerce_akeneo_attribute_mapping();

	  foreach ($attributes as $attribute) {
	    $found = FALSE;

	    foreach ($mappings as $mapping) {
	      if (in_array($attribute['code'], $mapping['names']) ||
	        (!empty($mapping['match']) && preg_match($mapping['match'], $attribute['code']))
	      ) {
	        $found = TRUE;

	        if (!$mapping['skip'] && !is_null($mapping['field_name'])) {
	          if (!isset($fields[$mapping['field_name']])) {
	            $fields[$mapping['field_name']] = array(
	              'field_name'   => $mapping['field_name'],
	              'field_locked' => $mapping['field_locked'],
	              'attributes'   => array(),
	            );
	          }

	          $fields[$mapping['field_name']]['attributes'][$attribute['code']] = $attribute;
	        }

	        break;
	      }
	    }

	    if (!$found) {
	      $fields[$attribute['field_name']] = array(
	        'field_name'   => $attribute['field_name'],
	        'field_locked' => FALSE,
	        'attributes'   => array($attribute['code'] => $attribute),
	      );
	    }
	  }

	  return $fields;
	}

	/**
	 * Load attribute.
	 *
	 * @param string $family
	 *   Family.
	 * @param string $section
	 *   Section.
	 * @param string $code
	 *   Code.
	 *
	 * @return array|NULL
	 *   Attribute.
	 */
	public function commerce_akeneo_attribute_load($family, $section, $code) {
	  $result = db_select('commerce_akeneo_attribute', 't')
	    ->fields('t')
	    ->condition('family', $family)
	    ->condition('section', $section)
	    ->condition('code', $code)
	    ->execute()
	    ->fetchAssoc();

	  if ($result) {
	    $attribute = (array) $result;

	    $attribute['settings'] = unserialize($attribute['settings']);

	    return $attribute;
	  }
	  else {
	    return NULL;
	  }
	}

	/**
	 * Load all attributes.
	 *
	 * @param string $family
	 *   Family.
	 * @param string $section
	 *   Section.
	 * @param string $code
	 *   Code.
	 *
	 * @return array
	 *   Attributes.
	 */
	public function commerce_akeneo_attribute_load_all($family = NULL, $section = NULL, $code = NULL) {
	  $query = db_select('commerce_akeneo_attribute', 't')->fields('t');

	  if (!is_null($family)) {
	    $query->condition('family', $family);
	  }

	  if (!is_null($section)) {
	    $query->condition('section', $section);
	  }

	  if (!is_null($code)) {
	    $query->condition('code', $code);
	  }

	  $results = $query->execute()->fetchAllAssoc('aid');

	  $attributes = array();

	  foreach ($results as $pos => $result) {
	    $attribute = (array) $result;

	    $attribute['settings'] = unserialize($attribute['settings']);

	    $attributes[] = $attribute;
	  }

	  return $attributes;
	}

	/**
	 * Create of update attribute.
	 *
	 * @param array $attribute
	 *   Attribute to save.
	 *
	 * @return bool
	 *   TRUE.
	 * @throws \Exception
	 */
	public function commerce_akeneo_attribute_save(&$attribute) {
	  // Check required arguments.
	  if (!isset($attribute['family']) || !isset($attribute['code']) || !isset($attribute['type'])) {
	    throw new \Exception('Invalid argument');
	  }

	  // Set default values.
	  $attribute += array(
	    'aid'      => NULL,
	    'section'  => '',
	    'label'    => $attribute['code'],
	    'settings' => array(),
	    'checksum' => '',
	    'required' => 0,
	    'created'  => REQUEST_TIME,
	    'changed'  => REQUEST_TIME,
	  );

	  // Serialize for database storing.
	  $settings              = $attribute['settings'];
	  $attribute['settings'] = serialize($attribute['settings']);
	  $attribute['required'] = $attribute['required'] ? 1 : 0;

	  if (empty($attribute['aid'])) {
	    unset($attribute['aid']);

	    $aid = db_insert('commerce_akeneo_attribute')
	      ->fields(array_keys($attribute))
	      ->values($attribute)
	      ->execute();

	    $attribute['aid'] = $aid;
	  }
	  else {
	    unset($attribute['created']);

	    db_update('commerce_akeneo_attribute')
	      ->condition('aid', $attribute['aid'])
	      ->fields($attribute)
	      ->execute();
	  }

	  // Restore settings.
	  $attribute['settings'] = $settings;

	  return TRUE;
	}

	/**
	 * Delete attribute from database.
	 *
	 * @param string $family
	 *   Family.
	 * @param string $section
	 *   Section.
	 * @param string $code
	 *   Code.
	 *
	 * @return \DatabaseStatementInterface
	 *   Query result.
	 */
	public function commerce_akeneo_attribute_delete($family = NULL, $section = NULL, $code = NULL) {
	  $query = db_delete('commerce_akeneo_attribute');

	  $query->condition('family', $family);
	  $query->condition('section', $section);
	  $query->condition('code', $code);

	  return $query->execute();
	}

	/**
	 * Implements hook_commerce_akeneo_family_delete().
	 */
	public function commerce_akeneo_attribute_commerce_akeneo_family_delete($family) {
	  db_delete('commerce_akeneo_attribute')->condition('family', $family)->execute();
	}

	/**
	 * Implements hook_field_delete_field().
	 */
	public function commerce_akeneo_attribute_field_delete_field($field) {
	  // Remove any attribute pointing at deleted $field.
	  db_delete('commerce_akeneo_attribute')->condition('field_name', $field['field_name'])->execute();
	}

	/**
	 * Implements hook_field_delete_instance().
	 */
	public function commerce_akeneo_attribute_field_delete_instance($instance) {
		
	  // Remove attribute pointing at deleted instance field.
	  if ($instance['entity_type'] == 'commerce_product') {
	    $query = db_select('commerce_akeneo_attribute', 'a');
	    $query->join('commerce_akeneo_family', 'f', 'f.code = a.family');

	    $query->fields('a', array('aid'))
	      ->condition('f.product_type', $instance['bundle'])
	      ->condition('a.field_name', $instance['field_name']);

	    $attribute = $query->execute()->fetchAssoc();

	    if ($attribute) {
	      db_delete('commerce_akeneo_attribute')->condition('aid', $attribute['aid'])->execute();
	    }
	  }
	}

 }