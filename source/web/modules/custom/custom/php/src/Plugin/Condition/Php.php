<?php

namespace Drupal\php\Plugin\Condition;

use Drupal\Core\Condition\ConditionPluginBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'Php' condition.
 *
 * @Condition(
 *   id = "php",
 *   label = @Translation("PHP")
 * )
 */
class Php extends ConditionPluginBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    // By default the PHP snippet need to return TRUE or blocks will silently
    // disappear after the module has been enabled and/or a block has been
    // configured without configuring a PHP snippet.
    return ['php' => '<?php return TRUE; ?>'] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form['php'] = [
      '#type' => 'textarea',
      '#title' => $this->t('When the following PHP return TRUE (experts only)'),
      '#default_value' => $this->configuration['php'],
      '#description' => $this->t('Enter PHP code between &lt;?php ?&gt;. Note that executing incorrect PHP code can break your Drupal site. Return TRUE in order for this condition to evaluate as TRUE.'),
      '#access' => \Drupal::currentUser()->hasPermission('use PHP for settings'),
    ];

    return parent::buildConfigurationForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    $this->configuration['php'] = $form_state->getValue('php');
    parent::submitConfigurationForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function summary() {
    if (!empty($this->configuration['php'])) {
      return t('When the given PHP evaluates as @state.', ['@state' => !empty($this->configuration['negate']) ? 'FALSE' : 'TRUE']);
    }
    else {
      return t('No PHP code has been provided.');
    }
  }

  /**
   * {@inheritdoc}
   */
  public function evaluate() {
    return php_eval($this->configuration['php']);
  }

}
