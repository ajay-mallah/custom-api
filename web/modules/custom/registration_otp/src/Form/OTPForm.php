<?php

namespace Drupal\registration_otp\Form;

use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\user\UserData;
use Drupal\user\UserDataInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class generic form to handle otp validation.
 */
class OTPForm extends FormBase {

  /**
   * @var Drupal\user\UserDataInterface
   */
  protected $userData;

  /**
   * @var Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityManager;

  /**
   * Constructor.
   * 
   * @param Drupal\user\UserDataInterface $userData
   * 
   * @param Drupal\Core\Entity\EntityTypeManager $entityTypeManager
   */
  public function __construct(UserDataInterface $userData, EntityTypeManager $entityTypeManager) {
    $this->userData = $userData;
    $this->entityManager = $entityTypeManager;
  }

  /**
   * @param Symfony\Component\DependencyInjection\ContainerInterface $container
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('user.data'),
      $container->get('entity_type.manager'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'registration_otp.otp_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['otp_form']['otp'] = [
      '#type' => 'textfield',
      '#title' => t('Enter otp'),
      '#size' => 6,
    ];

    $form['actions'] = ['#type' => 'action'];

    $form['actions']['verify'] = [
      '#type' => 'submit',
      '#value' => t('verify')
    ];

    $form['actions']['resend_otp'] = [
      '#type' => 'submit',
      '#submit' => ['::resendOTP'],
      '#value' => t('resend otp')
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    $uid = $_SESSION['uid'];
    $account = $this->entityManager->getStorage('user')->load($uid);
    $account->set('field_verified', TRUE);
    $account->save();
    user_login_finalize($account);
    $form_state->setRedirect('user.page');
  }

  /**
   * Resend otp to the current user email address.
   * 
   * @param array $form
   *  
   * @param FormStateInterface $form_state
   */
  public function resendOTP(array &$form, FormStateInterface $form_state) {

  }

  /**
   * Verifies user entered otp.
   * 
   * @param array $form
   *  
   * @param FormStateInterface $form_state
   */
  public function verifyOTP(array &$form, FormStateInterface $form_state) {
    if (isset($_SESSION['uid'])) {
      $uid = $_SESSION['uid'];
      $otp_string = $this->userData->get('registration_otp', $uid, 'otp_string');
      dump($otp_string);
      if ($form_state->getValue('otp') == $otp_string) {
        dd("verified");
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if (isset($_SESSION['uid'])) {
      $uid = $_SESSION['uid'];
      $otp_string = $this->userData->get('registration_otp', $uid, 'otp_string');
      if (!($form_state->getValue('otp') == $otp_string)) {
        $form_state->setErrorByName('otp', t('Invalid otp, please enter valid otp.'));
      }
    }
    
    parent::validateForm($form, $form_state);
  }
}