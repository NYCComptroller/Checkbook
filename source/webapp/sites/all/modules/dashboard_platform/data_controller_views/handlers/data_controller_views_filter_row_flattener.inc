<?php
 

   Inspired by World Bank API
*/

class data_controller_views_filter_row_flattener extends views_handler_filter {

    function query() {

        $rowFormatterConfig->group_by_columns = $this->selected_columns($this->options['group_by_columns']);
        $rowFormatterConfig->enumeration_column = $this->options['enumeration_column'];
        $rowFormatterConfig->subject_columns = $this->selected_columns($this->options['subject_columns']);
        $rowFormatterConfig->aggregate_column = $this->options['aggregate_column'];
        $this->query->add_filter($this->table, $this->definition['dcapi']['filter_key'], $rowFormatterConfig);

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

        return 'Group By Columns: ' . var_export($this->selected_columns($this->options['group_by_columns']), TRUE) .
            '<br/>' . 'Enumeration Columns: ' . $this->options['enumeration_column'] .
            '<br/>' . 'Aggregate Columns: ' . $this->options['aggregate_column'] .
            '<br/>' . 'Subject Columns: ' . var_export($this->selected_columns($this->options['subject_columns']), TRUE);
    }

    function option_definition() {
        $options = parent::option_definition();
        $options['group_by_columns'] = array('default' => array());
        $options['enumeration_column'] = array('default' => '');
        $options['subject_columns'] = array('default' => array());
        $options['aggregate_column'] = array('default' => array());
        return $options;
    }

    function options_form(&$form, &$form_state) {

        parent::options_form($form, $form_state);

        $options = _data_controller_views_options_list_all_dataset_fields($this->view);

        $form['group_by_columns'] = array(
      '#type' => 'checkboxes',
      '#title' => t('Group By Columns'),
      '#options' => $options,
      '#default_value' => $this->options['group_by_columns'],
        );

        $form['enumeration_column'] = array(
      '#type' => 'select',
      '#title' => t('Enumeration Column'),
      '#size' => 1,
      '#options' => $options,
      '#multiple' => FALSE,
      '#default_value' => $this->options['enumeration_column'],
        );


        $form['subject_columns'] = array(
      '#type' => 'checkboxes',
      '#title' => t('Subject Columns'),
      '#options' => $options,
      '#default_value' => $this->options['subject_columns'],
        );

        $form['aggregate_column'] = array(
      '#type' => 'select',
      '#title' => t('Aggregate Columns'),
      '#size' => 1,
      '#options' => array(1 => 'Yes', 0 => 'No'),
      '#multiple' => FALSE,
      '#default_value' => $this->options['aggregate_column'],
        );

    }
}
