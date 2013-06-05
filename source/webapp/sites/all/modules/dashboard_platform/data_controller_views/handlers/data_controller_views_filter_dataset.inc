<?php


   Inspired by World Bank API
*/

class data_controller_views_filter_dataset extends views_handler_filter {

  function query() {

      $this->query->add_filter($this->table, $this->definition['dcapi']['filter_key'], $this->value);

  }

  /**
   * Provide a simple drop-down for datasets
   */
  function value_form(&$form, &$form_state) {

      $this->get_value_options();

    $form['value'] = array(
      '#type' => 'select',
      '#title' => t('View'),
      '#size' => 1,
      '#options' => $this->value_options,
      '#multiple' => FALSE,
    );
  }

  function get_value_options() {
    if (!isset($this->value_options)) {
      $this->value_title = t('Views');

      $metamodel = data_controller_get_metamodel();
      foreach ($metamodel->datasets as $name => $view) {
        $options[$name] = $name . ' - ' . $view->publicName;
      }

      $this->value_options = $options;
    }
  }

}
