<?php

class view_join_views_filter_view extends views_handler_filter {

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
      
      $views = $this->_get_views();
      
      foreach ($views as $name => $view) {
        $options[$name] = t($name);
      }
      $this->value_options = $options;
    }
  }	
  
  function _get_views() {
    $result = db_query("SELECT DISTINCT v.* FROM {views_view} v");
    $views = [];

    // Load all the views.
    while ($data = db_fetch_object($result)) {
      $view = new view;
      $view->load_row($data);
      $view->loaded = TRUE;
      $view->type = t('Normal');
      $views[$view->name] = $view;
      $names[$view->vid] = $view->name;
    }
    
      // Get all default views.
    $status = variable_get('views_defaults', array());

    foreach (views_discover_default_views($reset) as $view) {
      // Determine if default view is enabled or disabled.
      if (isset($status[$view->name])) {
        $view->disabled = $status[$view->name];
      }

      // If overridden, also say so.
      if (!empty($views[$view->name])) {
        $views[$view->name]->type = t('Overridden');
      }
      else {
        $view->type = t('Default');
        $views[$view->name] = $view;
      }
    }

    

    return $views;
  }
}
