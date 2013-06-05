<?php
 

   Inspired by World Bank API
*/

class data_controller_views_filter_weighted_grader extends views_handler_filter {

    function query() {

        $weightedGraderConfig->value_column = $this->options['value_column'];
        $weightedGraderConfig->grade_column = $this->options['grade_column'];


        $this->query->add_filter($this->table, $this->definition['dcapi']['filter_key'], $weightedGraderConfig);

    }

    function admin_summary() {

        $summary = parent::admin_summary();

        return 'Value Column: ' . $this->options['value_column'] .
               ' Grade Column: ' . $this->options['grade_column'];
    }

    function option_definition() {
        $options = parent::option_definition();
        $options['value_column'] = array('default' => '');
        $options['grade_column'] = array('default' => '');
        return $options;
    }

    function options_form(&$form, &$form_state) {

        parent::options_form($form, $form_state);

        $options = _data_controller_views_options_list_all_dataset_fields($this->view);

        $form['value_column'] = array(
      '#type' => 'textfield',
      '#title' => t('Amount Column'),
      '#size' => 40,
      '#default_value' => $this->options['value_column'],
        );

        $form['grade_column'] = array(
      '#type' => 'textfield',
      '#title' => t('Grade Column'),
      '#size' => 40,
      '#default_value' => $this->options['grade_column'],
        );
    }
}