<?php
namespace Drupal\commerce_akeneo\Controller;

use Drupal\Core\Queue\QueueFactory;
use Drupal\Core\Queue\QueueInterface;
use Drupal\Component\Utility\Unicode;



class CommerceAkeneo {
	/**
	 * Generate a new vocabulary machine name.
	 *
	 * @param string $code
	 *   Akeneo code.
	 * @param string $type
	 *   Code (category|option|field|product).
	 *
	 * @return string
	 *   Machine name.
	 *
	 * @throws Exception
	 */
	public function commerce_akeneo_get_machine_name($code, $type = NULL) {
		$config = \Drupal::configFactory()->getEditable('commerce_akeneo.service_config');
	  if ($type == 'category') {
	    $machine_name = $config->get('commerce_akeneo_prefix_category') . $code;
	  }
	  elseif ($type == 'option') {
	    $machine_name = $config->get('commerce_akeneo_prefix_option') . $code;
	  }
	  elseif ($type == 'field') {
	    $machine_name = $config->get('commerce_akeneo_prefix_field') . $code;
	  }
	  elseif ($type == 'field_category') {
	    $machine_name = $config->get('commerce_akeneo_prefix_field_category') . $code;
	  }
	  elseif ($type == 'field_association') {
	    $machine_name = $config->get('commerce_akeneo_prefix_field_association') . $code;
	  }
	  elseif ($type == 'group') {
	    $machine_name = $config->get('commerce_akeneo_prefix_group', '') . $code;
	  }
	  elseif ($type == 'product') {
	    $machine_name = $config->get('commerce_akeneo_prefix_product') . $code;
	  }
	  else {
	    throw new \Exception(t('Unsupported type @type for machine name.', array('@type' => $type)));
	  }

	  \Drupal::moduleHandler()->alter('commerce_akeneo_get_machine_name', $machine_name, $code, $type);

	  // Match sql schema restrictions of 32 chars length.
	  // Shouldn't occur if correctly setup in Akeneo.
	  if ( Unicode::strlen($machine_name) > 32	) {
	    $machine_name_short = substr(Unicode::strtolower($machine_name), 0, 32);

	    $message = 'Machine name %machine_name has been truncated to 32 chars. ' .
	      'The shorten name %machine_name_short can smash another machine name. ' .
	      'You should use smaller ones directly in Akeneo (code: %code, type: %type)';

	    $arguments = array(
	      '%machine_name_short' => $machine_name_short,
	      '%machine_name'       => $machine_name,
	      '%type'               => $type,
	      '%code'               => $code,
	    );

	    \Drupal::logger('akeneo')->alert( $message);
	   drupal_set_message(
	      t('Machine name %machine_name has been truncated to 32 chars.', array('%machine_name' => $machine_name)),
	      'warning'
	    );

	    $machine_name = $machine_name_short;
	  }

	  return $machine_name;
	}

	/**
	 * Convert locale to language.
	 *
	 * @param string $locale
	 *   Locale to be converted.
	 *
	 * @return mixed
	 *   Language.
	 */
	public function commerce_akeneo_locale_to_language($locale) {
	  $languages = &drupal_static(__FUNCTION__);

	  if (!isset($languages[$locale])) {
	    if ($locale != LANGUAGE_NONE) {
	      list($language) = explode('_', $locale);
	    }
	    else {
	      $language = LANGUAGE_NONE;
	    }

	    Unicode::alter('commerce_akeneo_locale_to_language', $language, $locale);

	    $languages[$locale] = $language;
	  }

	  return $languages[$locale];
	}

	/**
	 * Obtain a value based on localized key.
	 *
	 * @param array  $values
	 *   Values.
	 * @param string $language
	 *   Language.
	 * @param bool   $fallback
	 *   Fallback use.
	 *
	 * @return mixed
	 *   The value localized.
	 */
	public function commerce_akeneo_get_language($values, $language = NULL, $fallback = TRUE) {
	  if (empty($values)) {
	    return FALSE;
	  }

	  if (is_null($language)) {
	     $language = language_default('language');
	  }

	  foreach ($values as $locale => $value) {
	    if ($this->commerce_akeneo_locale_to_language($locale) == $language) {
	      return $value;
	    }
	  }

	  if (isset($values[LANGUAGE_NONE])) {
	    return $values[LANGUAGE_NONE];
	  }

	  if ($fallback && count($values)) {
	    return current($values);
	  }

	  return FALSE;
	}

	/**
	 * Instance and create a generic queue.
	 *
	 * @param string $name
	 *   Name.
	 * @param bool   $create
	 *   Create the queue.
	 *
	 * @return \DrupalReliableQueueInterface
	 *   Queue.
	 */
	public function commerce_akeneo_queue_load($name, $create = FALSE) {
	  $cid = array(
	    'commerce_akeneo',
	    Unicode::strtolower($name),
	  );

	  /* @var DrupalReliableQueueInterface $queue */
	  $queue = DrupalQueue::get(implode(':', $cid));

	  if ($create) {
	    $queue->createQueue();
	  }

	  return $queue;
	}

}
