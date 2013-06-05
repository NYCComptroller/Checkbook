<?php
// $Id: views_plugin_style_json.inc,v 1.1.2.11 2010/07/15 07:34:48 allisterbeharry Exp $

/**
 * @file
 * Implementation of views_plugin_style for views_json
 */


/**
 * Implementation of views_plugin_style
 */
class views_plugin_style_highcharts extends views_plugin_style {
	/**
	 * Implementation of views_plugin_style::option_definition
	 */
	function option_definition() {

		$options = parent::option_definition();

		$options['ranking'] = array('default' => array(), 'translatable' => FALSE);
		return $options;
	}



	/**
	 * Provide a form for setting options.
	 *
	 * @param $form
	 * @param $form_state
	 */
	function options_form(&$form, &$form_state) {

		$fieldNames = $this->display->handler->get_field_labels();
		$fieldNames = array_merge(array(null => 'Select'), $fieldNames);
		log_debug("Field Names", $fieldNames);

		$form['ranking'] = array(
    		'#type' => 'fieldset',
    		'#title' => t('Ranking'),
			'#description' => t('Ranking Options.'),
    		'#tree' => true,
  		);
		$form['ranking']['column'] = array(
	    	'#type' => 'select',
	    	'#title' => ('Column'),
	    	'#default_value' => $this->options['ranking']['column'],
	    	'#description' => ('Key Column'),
	        '#size' => 1,
		    '#options' => $fieldNames,
	        '#multiple' => FALSE,
			);
		$form['ranking']['argument']= array(
    		'#type' => 'text',
    		'#title' => t('Argument Number'),
			'#description' => t('Argument.'),
	    	'#default_value' => isset($this->options['ranking']['argument']) ?$this->options['ranking']['argument'] : null,
	    	'#description' => t('Use argument.')
		);
		$form['ranking']['key']= array(
    		'#type' => 'text',
    		'#title' => t('Argument'),
			'#description' => t('Argument.'),
	    	'#default_value' => isset($this->options['ranking']['key']) ?$this->options['ranking']['key'] : null,
	    	'#description' => t('Use hardcoded key')
		);
	}



	/**
	 * Implementation of view_style_plugin::theme_functions(). Returns an array of theme functions to use
	 * for the current style plugin
	 * @return array
	 */
	function theme_functions() {
		return views_theme_functions('views_views_ranking_style', $this->view, $this->display);
	}

	/**
	 * Implementation of views_style_plugin::additional_theme_functions(). Returns empty array.
	 * @return array
	 */
	function additional_theme_functions() {
		return array();
	}



}
