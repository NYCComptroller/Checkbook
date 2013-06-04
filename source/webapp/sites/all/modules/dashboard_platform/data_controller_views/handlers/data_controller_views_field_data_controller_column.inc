<?php
 

   Inspired by World Bank API
*/

class data_controller_views_field_data_controller_column extends views_handler_field {

  function option_definition() {
    $options = parent::option_definition();
    $options['data_controller_col'] = array('default' => '');

    return $options;
  }

  function options_form(&$form, &$form_state) {

      parent::options_form($form, $form_state);

      $options = _data_controller_views_options_list_all_dataset_fields($this->view);

    $form['data_controller_col'] = array(
      '#type' => 'select',
      '#title' => t('View Column'),
      '#size' => 1,
      '#options' => $options,
      '#multiple' => FALSE,
      '#default_value' => $this->options['data_controller_col'],
    );

  }

  function render($values) {
      //need to change . to _
      $colname = $this->options['data_controller_col'];
      $colname = str_replace('.', '_', $colname);
    $value = $values->{$colname};
    return $value;
  }

  function query() {
    $this->ensure_my_table();
    // Add the field.
    $this->field_alias = $this->query->add_field($this->table_alias, $this->options['data_controller_col']);

    $this->add_additional_fields();
  }

  function click_sort($order) {
    $this->query->add_orderby($this->table, $this->options['data_controller_col'], $order);
  }
}
