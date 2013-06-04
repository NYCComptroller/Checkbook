<?php
// $Id: wbquery_sort.inc,v 1.2 2010/06/03 20:27:07 jmiccolis Exp $

class view_join_views_sort extends views_handler_sort {

	function query() {
		$this->query->add_orderby($this->table, $this->options['sort_col'], $this->options['order']);
	}

	/**
	 * Add column name to display
	 */
	function admin_summary() {
		 
		$summary = parent::admin_summary();
		return $this->options['sort_col'] . " - " . $summary;
	}


	function option_definition() {
		$options = parent::option_definition();
		$options['sort_col'] = array('default' => '');
		return $options;
	}

	function options_form(&$form, &$form_state) {
			
		parent::options_form($form, $form_state);
			
		$options = _view_join_views_options_list_all_view_fields($this->view);
			
		$form['sort_col'] = array(
      '#type' => 'select',
      '#title' => t('Sort Column'),
      '#size' => 1,
	  '#options' => $options,
      '#multiple' => FALSE,
      '#default_value' => $this->options['sort_col'],    
		);
			
	}


}
