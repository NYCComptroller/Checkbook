<?php
 

   Inspired by World Bank API
*/

class data_controller_views_sort extends views_handler_sort {

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

        $options = _data_controller_views_options_list_all_dataset_fields($this->view);

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
