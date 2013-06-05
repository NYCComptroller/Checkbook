<?php

class view_join_views_field_view_column extends views_handler_field {
	
  function option_definition() {
    $options = parent::option_definition();
    $options['view_col'] = array('default' => '');

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
  
  function render($values) {
    $value = $values->{$this->options['label']};
    return $value;
  }  

  function query() {
	$this->field_alias = $this->options['label'];
  }
  
  function click_sort($order) {
    $this->query->add_orderby($this->table, $this->options['label'], $order);
  }    
}
