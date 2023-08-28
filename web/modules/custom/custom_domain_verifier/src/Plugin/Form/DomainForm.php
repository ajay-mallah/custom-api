<?php

namespace Drupal\custom_domain_verifier\Plugin\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class DomainForm
 *  Provides a config form to set domain to verify user while registering.
 */
class DomainForm extends ConfigFormBase {

  /**
   * Config settins.
   *
   * @var string
   */
  const SETTING = 'custom_domain_verifier.settings';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'custom_domain_verifier.domain_form';
  }

  /**
   * {@inheritdoc}
   */
  public function getEditableConfigNames() {
    return [
      static::SETTING,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config(static::SETTING);

    $form['domain_block']['domain'] = [
      '#type' => 'textfield',
      '#title' => 'email domain name',
      '#default_value' => $config->get('domain') ?? '',
      '#required' => TRUE,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config(static::SETTING);

    $config->set('domain', $form_state->getValue('domain'));
    $config->save();

    parent::submitForm($form, $form_state);
  }

}
