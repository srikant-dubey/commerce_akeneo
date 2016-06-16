<?php
/**
 * @file
 * Contains Drupal\commerce_akeneo_taxonomy\Plugins\Services\CommerceAkeneoTaxonomyCategoryService.
 */
namespace Drupal\commerce_akeneo_taxonomy\Plugins\Services;

use Drupal\commerce_akeneo_taxonomy\Controller\CommerceAkeneoTaxonomyController;

/**
 * Class CommerceAkeneoTaxonomyCategoryService.
 *
 * @package Drupal\commerce_akeneo_taxonomy\Plugins\Services
 */
class CommerceAkeneoTaxonomyCategoryService extends CommerceAkeneoTaxonomyController {
	public function commerce_akeneo_taxonomy_services_category($request) {
	  if (!isset($request['code']) || !is_scalar($request['code'])) {
		return '';//return services_error('Missing code.', 406);
	  }

	  if (!isset($request['labels']) || !is_array($request['labels'])) {
		return '';//return services_error('Missing labels.', 406);
	  }

	  if (!isset($request['children']) || !is_array($request['children'])) {
		return '';//return services_error('Missing children.', 406);
	  }

	  try {
		$this->commerce_akeneo_taxonomy_handle_service($request['code'], 'category', $request['labels'], $request['children']);
	  }
	  catch (CommerceAkeneoException $e) {
		watchdog_exception('akeneo', $e);
		return '';
		//return services_error($e->getMessage(), $e->getCode() ? $e->getCode() : 500);
	  }
	  catch (Exception $e) {
		watchdog_exception('akeneo', $e);
		return '';
		//return services_error('Internal Server Error', 500);
	  }

	  return (object) $request;
	}
}
