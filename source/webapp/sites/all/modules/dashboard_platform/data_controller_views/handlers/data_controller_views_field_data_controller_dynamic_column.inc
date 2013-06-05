<?php
 

   Inspired by World Bank API
*/

class data_controller_views_field_data_controller_dynamic_column extends views_handler_field {

  function option_definition() {
    $options = parent::option_definition();
    $options['data_controller_col'] = array('default' => '');

    return $options;
  }

  function options_form(&$form, &$form_state) {

      parent::options_form($form, $form_state);

      $options = _data_controller_views_options_list_all_dataset_fields($this->view);

    $form['data_controller_col'] = array(
      '#type' => 'textfield',
      '#title' => t('Dynamic Column'),
      '#size' => 50,
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

  //this type of field does not alter the query, it exists
  //so that a column dymamically added by the controller can
  //be found by the rest of views
  function query() {
      $this->field_alias = $this->options['data_controller_col'];
      $this->field_alias = str_replace('.', '_', $this->field_alias);
  }

}
