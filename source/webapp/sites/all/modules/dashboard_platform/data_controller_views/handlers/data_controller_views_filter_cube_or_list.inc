<?php
//
//
//   Inspired by World Bank API
//*/
//
//class data_controller_views_filter_cube_or_list extends views_handler_filter {
//
//  function query() {
//
//      $this->query->add_filter($this->table, $this->definition['dcapi']['filter_key'], $this->value);
//
//  }
//
//  /**
//   * Provide a simple drop-down for cube/list
//   */
//  function value_form(&$form, &$form_state) {
//
//    $form['value'] = array(
//      '#type' => 'select',
//      '#title' => t('Cube/List'),
//      '#size' => 1,
//      '#options' => array('List' => 'List', 'Cube' => 'Cube'),
//      '#multiple' => FALSE,
//    );
//  }
//
//}
