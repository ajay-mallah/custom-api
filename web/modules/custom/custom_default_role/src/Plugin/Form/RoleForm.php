<?php

namespace Drupal\custom_default_role\Plugin\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\user\Entity\Role;

/**
 * Class RoleForm
 *  Provides a config form to set default role for the user while registering.
 */
class RoleForm extends ConfigFormBase {

  /**
   * Config settins.
   *
   * @var string
   */
  const SETTING = 'custom_default_role.settings';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'custom_default_role.role_form';
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

    $form['role_block']['role'] = [
      '#type' => 'radios',
      '#title' => 'set default value',
      '#default_value' => $config->get('role') ?? 'authenticated',
      '#options' => $this->getRoleList(),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config(static::SETTING);

    $config->set('role', $form_state->getValue('role'));
    $config->save();

    parent::submitForm($form, $form_state);
  }

  /**
   * Returns the role list.
   *
   * @return array
   */
  public function getRoleList() {
    // Drupal\user\Entity\Role.
    $roles = Role::loadMultiple();

    $role_list = [];
    foreach ($roles as $role_id => $role) {
      $role_list[$role_id] = $role->label();
    }

    return $role_list;
  }

}
