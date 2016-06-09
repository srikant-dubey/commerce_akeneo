<?php

namespace Drupal\commerce_akeneo\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Implements an example form.
 */
class AkeneoConfigService extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'commerce_akeneo_service_config_form';
  }

  /*
   * {@inheritdoc}
   */
  public function getEditableConfigNames(){
	return ['commerce_akeneo.service_config'];  
  }
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
	  
	  $config = $this->config('commerce_akeneo.service_config');
    
	  $form['commerce_akeneo_machine_name'] = array(
		'#type'        => 'fieldset',
		'#title'       => t('Machine names prefix'),
		'#description' => t("Generally prefix added to code can't exceed 32 chars length."),
		'#collapsible' => TRUE,
		'#collapsed'   => FALSE,
	  );

	  $form['commerce_akeneo_machine_name']['commerce_akeneo_prefix_product'] = array(
		'#type'          => 'textfield',
		'#title'         => t('Product type prefix'),
		'#size'          => 20,
		'#default_value' => $config->get('commerce_akeneo_prefix_product'),
		'#description'   => t('Corresponds to the product type.'),
	  );

	  $form['commerce_akeneo_machine_name']['commerce_akeneo_prefix_field'] = array(
		'#type'          => 'textfield',
		'#title'         => t('Field prefix'),
		'#size'          => 20,
		'#default_value' => $config->get('commerce_akeneo_prefix_field'),
		'#description'   => t('Corresponds to the default field.'),
	  );

	  $form['commerce_akeneo_machine_name']['commerce_akeneo_prefix_field_category'] = array(
		'#type'          => 'textfield',
		'#title'         => t('Field association prefix'),
		'#size'          => 20,
		'#default_value' => $config->get('commerce_akeneo_prefix_field_category'),
		'#description'   => t("Corresponds to the category's catalog field."),
	  );

	  $form['commerce_akeneo_machine_name']['commerce_akeneo_prefix_field_association'] = array(
		'#type'          => 'textfield',
		'#title'         => t('Field association prefix'),
		'#size'          => 20,
		'#default_value' => $config->get('commerce_akeneo_prefix_field_association'),
		'#description'   => t('Corresponds to the product association field.'),
	  );

	  $form['commerce_akeneo_machine_name']['commerce_akeneo_prefix_group'] = array(
		'#type'          => 'textfield',
		'#title'         => t('Group prefix'),
		'#size'          => 20,
		'#default_value' => $config->get('commerce_akeneo_prefix_group'),
		'#description'   => t('Corresponds to the field group used to create vertical tabs.'),
	  );

	  $form['commerce_akeneo_machine_name']['commerce_akeneo_prefix_category'] = array(
		'#type'          => 'textfield',
		'#title'         => t('Category prefix'),
		'#size'          => 20,
		'#default_value' => $config->get('commerce_akeneo_prefix_category'),
		'#description'   => t('Corresponds to the category taxonomy vocabulary.'),
	  );

	  $form['commerce_akeneo_machine_name']['commerce_akeneo_prefix_option'] = array(
		'#type'          => 'textfield',
		'#title'         => t('Option prefix'),
		'#size'          => 20,
		'#default_value' => $config->get('commerce_akeneo_prefix_option'),
		'#description'   => t('Corresponds to the default taxonomy vocabulary.'),
	  );

	  return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    
	  if (!preg_match('/^[a-z0-9\_]*$/', $form_state->getValue('commerce_akeneo_prefix_product'))) {
		$form_state->setErrorByName(('commerce_akeneo_prefix_product'), $this->t('The product type prefix must contain only lowercase letters, numbers, and underscores.'));
	  }

	  if (!preg_match('/^[a-z0-9\_]*$/', $form_state->getValue('commerce_akeneo_prefix_field'))) {
		  
		  $form_state->setErrorByName(('commerce_akeneo_prefix_field'), $this->t('The field prefix must contain only lowercase letters, numbers, and underscores.'));
	  }

	  if (!preg_match('/^[a-z0-9\_]*$/', $form_state->getValue('commerce_akeneo_prefix_category'))) {
	
		  $form_state->setErrorByName(('commerce_akeneo_prefix_category'), $this->t('The category prefix must contain only lowercase letters, numbers, and underscores.'));
	  }

	  if (!preg_match('/^[a-z0-9\_]*$/', $form_state->getValue('commerce_akeneo_prefix_option'))) {
		  
		  $form_state->setErrorByName(('commerce_akeneo_prefix_option'), $this->t('The option prefix must contain only lowercase letters, numbers, and underscores.'));
	  }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('commerce_akeneo.service_config')
      ->set('commerce_akeneo_prefix_product', $form_state->getValue('commerce_akeneo_prefix_product'))
      ->set('commerce_akeneo_prefix_field', $form_state->getValue('commerce_akeneo_prefix_field'))
      ->set('commerce_akeneo_prefix_category', $form_state->getValue('commerce_akeneo_prefix_category'))
      ->set('commerce_akeneo_prefix_option', $form_state->getValue('commerce_akeneo_prefix_option'))
      ->set('commerce_akeneo_prefix_group', $form_state->getValue('commerce_akeneo_prefix_group'))
      ->set('commerce_akeneo_prefix_field_association', $form_state->getValue('commerce_akeneo_prefix_field_association'))
      ->set('commerce_akeneo_prefix_field_category', $form_state->getValue('commerce_akeneo_prefix_field_category'))
      ->save();
    parent::submitForm($form, $form_state);
  }

}
