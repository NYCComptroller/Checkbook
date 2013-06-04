<?php
 

   Inspired by World Bank API
*/

class data_controller_views_filter_csv_streamer extends views_handler_filter {

    function query() {

        $csvStreamer->filename = $this->options['filename'];
        $csvStreamer->header = $this->options['header'];

        $this->query->add_filter($this->table, $this->definition['dcapi']['filter_key'], $csvStreamer);

    }

    function admin_summary() {

        $summary = parent::admin_summary();

        return $summary;
    }

    function option_definition() {
        $options = parent::option_definition();

        $options['filename'] = array('default' => '');
        $options['header'] = array('default' => TRUE);

        return $options;
    }

    function options_form(&$form, &$form_state) {

        parent::options_form($form, $form_state);

        $form['filename'] = array(
      '#type' => 'textfield',
      '#title' => t('Filename'),
      '#size' => 50,
      '#default_value' => $this->options['filename'],
        );

    $form['header'] = array(
      '#type' => 'checkbox',
      '#title' => t('Make first row a list of column headers.'),
      '#default_value' => !empty($this->options['header']),
    );

    }
}
