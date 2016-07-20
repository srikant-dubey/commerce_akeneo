<?php
/**
 * @file 
 * Contains Drupal\commerce_akeneo_family\Controller\CommerceAkeneoFamily
 */
namespace Drupal\commerce_akeneo_family\Controller;
/**
 * Class CommerceAkeneoFamily
 * @package Drupal\commerce_akeneo_family\Controller 
 */

class CommerceAkeneoFamily {
	
	/**
	 * Load family.
	 *
	 * @param string $code
	 *   Code.
	 *
	 * @return mixed
	 *   Family.
	 */
	public function commerce_akeneo_family_load($code) {
	  $result = db_select('commerce_akeneo_family', 't')
		->fields('t')
		->condition('code', $code)
		->execute()
		->fetchAssoc();

	  if ($result) {
		return (array) $result;
	  }
	  else {
		return NULL;
	  }
	}

	/**
	 * Load all families.
	 *
	 * @return array
	 *   Families.
	 */
	public function commerce_akeneo_family_load_all() {
	  $results = db_select('commerce_akeneo_family', 't')
		->fields('t')
		->execute()
		->fetchAllAssoc('fid');

	  return $results;
	}

	/**
	 * Create or update family.
	 *
	 * @param array $family
	 *   Family.
	 *
	 * @return bool
	 *   TRUE.
	 * @throws \Exception
	 */
	public function commerce_akeneo_family_save(&$family) {
	  // Check required arguments.
	  if (!isset($family['code'])) {
		throw new \Exception('Invalid argument');
	  }

	  // Set default values.
	  $family += array(
		'fid'      => NULL,
		'label'    => $family['code'],
		'settings' => array(),
		'created'  => REQUEST_TIME,
		'changed'  => REQUEST_TIME,
	  );

	  // Serialize for database storing.
	  $settings           = $family['settings'];
	  $family['settings'] = serialize($family['settings']);

	  if (empty($family['fid'])) {
		unset($family['fid']);

		$fid = db_insert('commerce_akeneo_family')
		  ->fields(array_keys($family))
		  ->values(array_values($family))
		  ->execute();

		$family['fid'] = $fid;
	  }
	  else {
		unset($family['created']);

		db_update('commerce_akeneo_family')
		  ->condition('fid', $family['fid'])
		  ->fields($family)
		  ->execute();
	  }

	  // Restore settings.
	  $family['settings'] = $settings;

	  return TRUE;
	}

	/**
	 * Delete family.
	 *
	 * @param string $code
	 *   Code.
	 */
	public function commerce_akeneo_family_delete($code) {
	  \Drupal::moduleHandler()->invokeAll('commerce_akeneo_family_delete', $code);

	  db_delete('commerce_akeneo_family')->condition('code', $code)->execute();
	}

	/**
	 * Implements hook_commerce_product_type_delete().
	 */
	public function commerce_akeneo_family_commerce_product_type_delete($product_type, $skip_reset) {
	  // Todo: move this part to commerce_akeneo_attribute with hook.
	  if (\Drupal::moduleHandler()->moduleExists('commerce_akeneo_attribute')) {
		$family = db_select('commerce_akeneo_family', 'f')
		  ->fields('f')
		  ->condition('product_type', $product_type)
		  ->execute()
		  ->fetchAssoc();

		if ($family) {
		  db_delete('commerce_akeneo_attribute')->condition('family', $family['code'])->execute();
		}
	  }

	  db_delete('commerce_akeneo_family')->condition('product_type', $product_type)->execute();
	}


}
