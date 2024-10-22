<?php

namespace Drupal\checkbook_path_validator\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * PathSettingsForm class.
 */
class PathSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'checkbook_path_validator.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'checkbook_path_validator_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $data = $this->config('checkbook_path_validator.settings')->get();

    $form['status'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Status'),
      '#description' => $this->t('Path and Query validation is enabled / disabled. If the validation fails, the user will be redirected to 404 page.'),
      '#default_value' => $data['status'] ?? FALSE,
    ];

    $form['regex_delimiter'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Regex delimiter'),
      '#description' => $this->t('Often used delimiters are forward slashes (/), hash signs (#) and tildes (~). '),
      '#required' => TRUE,
      '#default_value' => $data['regex_delimiter'] ?? '/',
    ];

    // Define draggable table.
    $form['table-row'] = [
      '#type' => 'table',
      '#header' => [
        $this->t(''),
        $this->t('Parameter'),
        $this->t('Regex (without delimiters)'),
        $this->t('Operations'),
        $this->t('Weight'),
      ],
      '#tabledrag' => [
        [
          'action' => 'order',
          'relationship' => 'sibling',
          'group' => 'table-sort-weight',
        ],
      ],
      '#prefix' => '<div id="wrapper">',
      '#suffix' => '</div>',
    ];

    // Calculate the number of rows.
    // If there is no "num_of_rows" state value, count number of config items,
    // and it there is no config items, set the number of rows to 1,
    // so there is at least one row.
    if (!$num_of_rows = $form_state->get('num_of_rows')) {
      $num_of_rows = count($data['items'] ?? []) ?: 1;
      $form_state->set('num_of_rows', $num_of_rows);
    }

    // Build the table rows and columns.
    for ($i = 0; $i < $num_of_rows; $i++) {
      $form['table-row'][$i]['#attributes']['class'][] = 'draggable';

      // Sort the table row according to its weight.
      $form['table-row'][$i]['#weight'] = $i;

      // This is needed to avoid writing additional css to make all inline.
      $form['table-row'][$i]['id'] = [
        '#markup' => ''
      ];

      $form['table-row'][$i]['parameter'] = [
        '#type' => 'textfield',
        '#default_value' => $data['items'][$i]['parameter'] ?? '',
      ];

      $form['table-row'][$i]['regex'] = [
        '#type' => 'textfield',
        '#default_value' => $data['items'][$i]['regex'] ?? '',
      ];

      $form['table-row'][$i]['op'] = [
        '#type' => 'submit',
        '#name' => $i . '-row',
        '#value' => $this->t('Remove'),
        '#disabled' => $num_of_rows <= 1,
        '#submit' => ['::removeSubmit'],
        '#ajax' => [
          'callback' => '::addRemoveCallback',
          'wrapper' => 'wrapper',
        ],
      ];

      // Weight element.
      $form['table-row'][$i]['weight'] = [
        '#type' => 'weight',
        '#title_display' => 'invisible',
        '#default_value' => $i,
        // Classify the weight element for #tabledrag.
        '#attributes' => [
          'class' => [
            'table-sort-weight'
          ]
        ],
      ];

      $form['add_another'] = [
        '#type' => 'submit',
        '#value' => $this->t('Add another'),
        '#submit' => ['::addAnotherSubmit'],
        '#ajax' => [
          'callback' => '::addRemoveCallback',
          'wrapper' => 'wrapper',
        ],
      ];
    }

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
    $data = $form_state->getValues();

    // Filter data.
    $items = [];
    foreach ($data['table-row'] as $item) {
      if (!empty($item['parameter']) && !empty($item['regex'])) {
        $items[] = [
          'parameter' => $item['parameter'],
          'regex' => $item['regex'],
        ];
      }
    }

    // Save config.
    $this->config('checkbook_path_validator.settings')
      ->setData([
        'status' => $data['status'],
        'regex_delimiter' => $data['regex_delimiter'],
        'items' => $items,
      ])
      ->save();
  }

  /**
   * Callback for add another button.
   */
  public function addRemoveCallback(array &$form, FormStateInterface $form_state) {
    return $form['table-row'];
  }

  /**
   * Submit handler for the "Add another" button.
   *
   * Increments the counter and causes a rebuild.
   */
  public function addAnotherSubmit(array &$form, FormStateInterface $form_state) {
    // Increase number of rows count.
    $num_of_rows = $form_state->get('num_of_rows');
    $form_state->set('num_of_rows', $num_of_rows + 1);

    // Rebuild form.
    $form_state->setRebuild();
  }

  /**
   * Submit handler for the "Remove" button.
   */
  public function removeSubmit(array &$form, FormStateInterface $form_state) {
    // Get the row id of the remove input.
    $row_id = $form_state->getTriggeringElement()['#parents'][1];

    // Remove the row from the user input and reindex table-row data.
    $input = $form_state->getUserInput();
    unset($input['table-row'][$row_id]);
    $input['table-row'] = array_values($input['table-row']);

    // Update user input and data object.
    $form_state->setUserInput($input);
    $form_state->set('data', $input['table-row']);

    // Decrease number of rows count.
    $num_of_rows = $form_state->get('num_of_rows');
    $form_state->set('num_of_rows', $num_of_rows - 1);

    // Rebuild form state.
    $form_state->setRebuild();
  }

}
