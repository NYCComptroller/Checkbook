<?php

namespace Drupal\php\Plugin\views\argument_validator;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\argument_validator\ArgumentValidatorPluginBase;

/**
 * Provide PHP code to validate whether or not an argument is ok.
 *
 * @ViewsArgumentValidator(
 *   id = "php",
 *   module = "php",
 *   title = @Translation("PHP Code")
 * )
 */
class Php extends ArgumentValidatorPluginBase {

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();
    $options['code'] = ['default' => ''];

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);
    $form['code'] = [
      '#type' => 'textarea',
      '#title' => $this->t('PHP validate code'),
      '#default_value' => $this->options['code'],
      '#description' => $this->t('Enter PHP code that returns TRUE or FALSE. No return is the same as FALSE, so be SURE to return something if you do not want to declare the argument invalid. Do not use &lt;?php ?&gt;. The argument to validate will be "$argument" and the view will be "$view". You may change the argument by setting "$handler->argument". You may change the title used for substitutions for this argument by setting "$handler->validated_title".'),
    ];

    $this->checkAccess($form, 'code');
  }

  /**
   * Permission check.
   *
   * Only let users with PHP block visibility permissions set/modify this
   * validate plugin.
   */
  public function access() {
    return \Drupal::currentUser()->hasPermission('use PHP for settings');
  }

  /**
   * {@inheritdoc}
   */
  public function validateArgument($argument) {
    // Set up variables to make it easier to reference during the argument.
    $view = &$this->view;
    $handler = &$this->argument;

    ob_start();
    $result = eval($this->options['code']);
    ob_end_clean();
    return $result;
  }

}
