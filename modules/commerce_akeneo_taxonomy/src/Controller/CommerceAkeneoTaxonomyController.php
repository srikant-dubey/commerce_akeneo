<?php
 /**
  * @file
  * Contains Drupal\commerce_akeneo_taxonomy\Controller\CommerceAkeneoTaxonomyController.
  */
 namespace Drupal\commerce_akeneo_taxonomy\Controller;
 
 use Drupal\commerce_akeneo\Controller\CommerceAkeneo;
 use Drupal\Component\Utility\Xss;
 use Drupal\Component\Utility\SafeMarkup;

 /**
  * Class CommerceAkeneoTaxonomyController.
  * @package Drupal\commerce_akeneo_taxonomy\Controller
  */
class CommerceAkeneoTaxonomyController extends CommerceAkeneo {

	/**
 	 * {@innerDoc}
	 */
	protected $commereceModule;
	/**
	 * Constructor function
	 */
	public function __construct(){

	}

	/**
	 * Handle taxonomy service exposed.
	 *
	 * @param string $code
	 *   Code.
	 * @param string $type
	 *   Type (option|category).
	 * @param array  $labels
	 *   Labels.
	 * @param array  $values
	 *   Values.
	 *
	 * @throws \CommerceAkeneoException
	 * @throws \Exception
	 */
	public function commerce_akeneo_taxonomy_handle_service($code, $type, $labels, $values) {
	  $code = SafeMarkup::checkPlain($code);

	  // Store locally details about Akeneo select attribute.
	  if (!$taxonomy = $this->commerce_akeneo_taxonomy_load($code, $type)) {
	    $vocabulary = CommerceAkeneo::commerce_akeneo_get_machine_name($code, $type);
	    $taxonomy   = array(
	      'code'       => $code,
	      'type'       => $type,
	      'vocabulary' => $vocabulary,
	    );
	  }

	  $taxonomy['label'] = Xss::filter(CommerceAkeneo::commerce_akeneo_get_language($labels));

	  $this->commerce_akeneo_taxonomy_save($taxonomy);

	  // Save product to Drupal Queue for future Migrate handle.
	  $queue = CommerceAkeneo::commerce_akeneo_queue_load($type . ':' . $code, TRUE);

	  // Add elements to queue for migrate import.
	  foreach ($values as $key => $item) {
	    $item['machine_name'] = $key;

	    if (!isset($item['parent']) || $item['parent'] == $code) {
	      $item['parent'] = NULL;
	    }

	    if (!$queue->createItem($item)) {
	      throw new CommerceAkeneoException('Error while trying to add item to queue.', 500);
	    }
	  }

	  // Reload migrate job list.
	  //migrate_static_registration();
	}

	/**
	 * Load taxonomy record.
	 *
	 * @param string $code
	 *   Code.
	 * @param string $type
	 *   Type.
	 *
	 * @return mixed
	 *   Taxonomy.
	 */
	public function commerce_akeneo_taxonomy_load($code, $type = 'option') {
	  static $taxonomy = array();

	  if (!isset($taxonomy[$type][$code])) {
	    $result = db_select('commerce_akeneo_taxonomy', 't')
	      ->fields('t')
	      ->condition('code', $code)
	      ->condition('type', $type)
	      ->execute()
	      ->fetchAssoc();

	    if ($result) {
	      return ($taxonomy[$type][$code] = (array) $result);
	    }
	    else {
	      return NULL;
	    }
	  }

	  return $taxonomy[$type][$code];
	}

	/**
	 * Load all taxonomy records.
	 *
	 * @param string $type
	 *   Type (option|category).
	 *
	 * @return array
	 *   Taxonomy records.
	 */
	public function commerce_akeneo_taxonomy_load_all($type = 'option') {
	  $results = db_select('commerce_akeneo_taxonomy', 't')
	    ->fields('t')
	    ->condition('type', $type)
	    ->execute()
	    ->fetchAllAssoc('tid');

	  $taxonomies = array();

	  foreach ($results as $key => $result) {
	    $taxonomies[$key] = (array) $result;
	  }

	  return $taxonomies;
	}

	/**
	 * Create or update taxonomy record.
	 *
	 * @param array $taxonomy
	 *   Taxonomy.
	 *
	 * @return bool
	 *   TRUE if correctly saved.
	 *
	 * @throws \Exception
	 */
	public function commerce_akeneo_taxonomy_save(&$taxonomy) {
	  // Check required arguments.
	  if (!isset($taxonomy['code'])) {
	    throw new \Exception('Invalid argument');
	  }

	  // Set default values.
	  $taxonomy += array(
	    'tid'     => NULL,
	    'label'   => $taxonomy['code'],
	    'created' => REQUEST_TIME,
	    'changed' => REQUEST_TIME,
	  );

	  if (empty($taxonomy['tid'])) {
	    unset($taxonomy['tid']);

	    $tid = db_insert('commerce_akeneo_taxonomy')
	      ->fields(array_keys($taxonomy))
	      ->values(array_values($taxonomy))
	      ->execute();

	    $taxonomy['tid'] = $tid;
	  }
	  else {
	    unset($taxonomy['created']);

	    db_update('commerce_akeneo_taxonomy')
	      ->condition('tid', $taxonomy['tid'])
	      ->fields($taxonomy)
	      ->execute();
	  }

	  return TRUE;
	}

	/**
	 * Delete taxonomy record.
	 *
	 * @param string $code
	 *   Code.
	 */
	public function commerce_akeneo_taxonomy_delete($code) {
	  db_delete('commerce_akeneo_taxonomy')->condition('code', $code)->execute();
	}

	/**
	 * Implements hook_taxonomy_vocabulary_delete().
	 */
	public function commerce_akeneo_taxonomy_taxonomy_vocabulary_delete($vocabulary) {
	  db_delete('commerce_akeneo_taxonomy')->condition('vocabulary', $vocabulary->machine_name)->execute();
	}

}
