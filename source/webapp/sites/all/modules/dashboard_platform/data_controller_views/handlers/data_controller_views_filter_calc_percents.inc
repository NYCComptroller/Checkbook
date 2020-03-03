<?php
 

   Inspired by World Bank API
*/

class data_controller_views_filter_calc_percents extends views_handler_filter {

    function query() {

        $percentCalculatorConfig->amount_columns = $this->selected_columns($this->options['amount_columns']);
        $percentCalculatorConfig->prefix = $this->options['generated_col_prefix'];
        $percentCalculatorConfig->suffix = $this->options['generated_col_suffix'];


        $this->query->add_filter($this->table, $this->definition['dcapi']['filter_key'], $percentCalculatorConfig);

    }

    function selected_columns($options_array) {
        $result = [];
        foreach ($options_array as $key => $val) {
            if ($val !== 0) {
                $result[] = $val;
            }
        }
        return $result;
    }

    function admin_summary() {

        $summary = parent::admin_summary();

        return 'Amount Columns: ' . var_export($this->selected_columns($this->options['amount_columns']), TRUE) .
            '<br/>' . 'Prefix: ' . $this->options['generated_col_prefix'] .
            '<br/>' . 'Suffix: ' . $this->options['generated_col_suffix'];
    }

    function option_definition() {
        $options = parent::option_definition();
        $options['amount_columns'] = array('default' => array());
        $options['generated_col_prefix'] = array('default' => '');
        $options['generated_col_suffix'] = array('default' => '');
        return $options;
    }

    function options_form(&$form, &$form_state) {

        parent::options_form($form, $form_state);

        $options = _data_controller_views_options_list_all_dataset_fields($this->view);

        $form['amount_columns'] = array(
      '#type' => 'checkboxes',
      '#title' => t('Amount Columns'),
      '#options' => $options,
      '#default_value' => $this->options['amount_columns'],
        );

        $form['generated_col_prefix'] = array(
      '#type' => 'textfield',
      '#title' => t('Prefix'),
      '#size' => 40,
      '#default_value' => $this->options['generated_col_prefix'],
        );

        $form['generated_col_suffix'] = array(
      '#type' => 'textfield',
      '#title' => t('Suffix'),
      '#size' => 40,
      '#default_value' => $this->options['generated_col_suffix'],
        );

    }

}
