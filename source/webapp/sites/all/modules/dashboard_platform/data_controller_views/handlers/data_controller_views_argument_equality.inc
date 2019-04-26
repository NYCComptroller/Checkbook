<?php


   Inspired by World Bank API
*/

class data_controller_views_argument_equality extends views_handler_argument {

  function query() {
    if (isset($this->argument)) {
      $this->query->add_parameter($this->options['data_controller_col'], $this->argument, $this->options['data_controller_operator']);
    }
  }

    /**
     * Add column name to display
     */
    function admin_summary() {

        $summary = parent::admin_summary();
        return $this->options['data_controller_col'] . $summary;
    }

    function option_definition() {
        $options = parent::option_definition();
        $options['data_controller_col'] = array('default' => '');
        $options['data_controller_operator'] = array('default' => 'Equal');
        return $options;
    }

    function options_form(&$form, &$form_state) {

        parent::options_form($form, $form_state);

        $options = _data_controller_views_options_list_all_dataset_fields($this->view);

        $form['data_controller_col'] = array(
      '#type' => 'select',
      '#title' => t('Data Controller Column'),
      '#size' => 1,
      '#options' => $options,
      '#multiple' => FALSE,
      '#default_value' => $this->options['data_controller_col'],
        );

        $operatorConfig = data_controller_get_operator_factory_instance();
        $supportedOperators = $operatorConfig->getSupportedOperators();
        $operator_options = [];
        foreach ($supportedOperators as $supportedName => $operatorDescription) {
            $operator_options[$supportedName] = $operatorDescription;
        }

        $form['data_controller_operator'] = array(
      '#type' => 'select',
      '#title' => t('Operator'),
      '#size' => 1,
      '#options' => $operator_options,
      '#multiple' => FALSE,
      '#default_value' => $this->options['data_controller_operator'],
        );
    }

}
