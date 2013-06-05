<?php

class view_join_views_filter_join_column extends views_handler_filter {

  function query() {

  	$this->query->add_filter($this->table, $this->definition['dcapi']['filter_key'], $this->value);

  }

  function options_form(&$form, &$form_state) {
  	
  	//parent::options_form($form, $form_state);
  	
  	$options = _view_join_views_options_list_all_view_fields($this->view);
  	
    $form['value'] = array(
      '#type' => 'select',
      '#title' => t('Join Column'),
      '#size' => 1,
	  '#options' => $options,
      '#multiple' => FALSE,
      '#default_value' => $this->options['value'],    
    );  	
  	
  }
}
