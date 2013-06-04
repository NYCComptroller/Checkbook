<?php

class view_join_views_filter_view_column_equality extends views_handler_filter_equality {
  /**
   * Provide simple equality operator
   */
  function operator_options() {
    return array(
      '=' => t('Is equal to'),
    );
  }

  function query() {
    if (isset($this->value)) {
      $this->query->add_parameter($this->options['view_col'], $this->value);
    }
  }
  

  function option_definition() {
    $options = parent::option_definition();
	$options['view_col'] = array('default' => '=');
	return $options;
  }
  
  function options_form(&$form, &$form_state) {
  	
  	parent::options_form($form, $form_state);
  	
  	$options = _view_join_views_options_list_all_view_fields($this->view);
  	
    $form['view_col'] = array(
      '#type' => 'select',
      '#title' => t('View Column'),
      '#size' => 1,
	  '#options' => $options,
      '#multiple' => FALSE,
      '#default_value' => $this->options['view_col'],    
    ); 
  }
  
}
