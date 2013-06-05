<?php

class view_join_views_filter_join_type extends views_handler_filter {

  function query() {

  	$this->query->add_filter($this->table, $this->definition['dcapi']['filter_key'], $this->value);

  }

  /**
   * Provide a simple drop-down for datasets
   */
  function value_form(&$form, &$form_state) {

    $form['value'] = array(
      '#type' => 'select',
      '#title' => t('View'),
      '#size' => 1,
	  '#options' => array('Inner' => 'Inner', 'Full' => 'Full', 'LeftOuter' => 'LeftOuter', 'RightOuter' => 'RightOuter', 'Cross' => 'Cross', 'Union' => 'Union'),
      '#multiple' => FALSE,
    );
  }

}
