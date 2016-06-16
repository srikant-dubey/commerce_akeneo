<?php
namespace Drupal\commerce_akeneo_taxonomy\Plugins\Services;
/**
 * @file
 * Contains Drupal\commerce_akeneo_taxonomy\Plugins\Services\CommerceAkeneoTaxonomyOptinService.
 */
use Drupal\commerce_akeneo_taxonomy\CommerceAkeneoTaxonomy;

/**
 * Class CommerceAkeneoTaxonomyOptinService
 * @package Drupal\commerce_akeneo_taxonomy\Plugins\Services.
 */
 class CommerceAkeneoTaxonomyOptinService extends CommerceAkeneoTaxonomy {

	public function commerce_akeneo_taxonomy_services_option($request) {
	  if (!isset($request['code']) || !is_scalar($request['code'])) {
		return services_error('Missing code.', 406);
	  }

	  if (!isset($request['labels']) || !is_array($request['labels'])) {
		return services_error('Missing labels.', 406);
	  }

	  if (!isset($request['options']) || !is_array($request['options'])) {
		return services_error('Missing options.', 406);
	  }

	  try {
		$this->commerce_akeneo_taxonomy_handle_service($request['code'], 'option', $request['labels'], $request['options']);
	  }
	  catch (CommerceAkeneoException $e) {
		watchdog_exception('akeneo', $e);

		return services_error($e->getMessage(), $e->getCode() ? $e->getCode() : 500);
	  }
	  catch (Exception $e) {
		watchdog_exception('akeneo', $e);

		return services_error('Internal Server Error', 500);
	  }

	  return (object) $request;
	}
}
