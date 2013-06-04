<?php

class view_join_views_argument_view_column_equality extends views_handler_argument {

	function query() {
		if (isset($this->argument)) {
			$this->query->add_parameter($this->options['view_col'], $this->argument);
		}
	}

	function option_definition() {
		$options = parent::option_definition();
		$options['view_col'] = array('default' => '');
		$options['pass_through'] = array('default' => array());
		return $options;
	}

	function options_form(&$form, &$form_state) {

		parent::options_form($form, $form_state);

		// this will only work for one arguemnt
		//second one will overwrite first
		$remoteOnlyOption = array('_none' => 'NONE', '_none2' => 'NONE2', '_none3' => 'NONE3');
		$localColOptions = _view_join_views_options_list_all_view_fields($this->view);
		$options = array_merge($remoteOnlyOption, $localColOptions);

		$form['view_col'] = array(
      '#type' => 'select',
      '#title' => t('Local View Column'),
      '#size' => 1,
	  '#options' => $options,
      '#multiple' => FALSE,
      '#default_value' => $this->options['view_col'],
		);

		$form['pass_through'] = array(
    		'#type' => 'fieldset',
    		'#title' => t('Pass-through settings'),
    		'#tree' => true,
  		);

		//now for each view all the user to map it to an argument
		//if specified we will try to pass the argument through
		$innerViewNames = _view_join_views_list_all_views($this->view);
		foreach ($innerViewNames as $innerViewName) {

			$innerView = views_get_view($innerViewName);
			$noPassThroughOption = array('_none' => 'NONE');
			$argumentOptions = _view_join_views_options_list_view_arguments($innerView);
			$allArgumentOptions = array_merge($noPassThroughOption, $argumentOptions);

			$form['pass_through'][$innerViewName] = array(
	      '#type' => 'select',
	      '#title' => t('View - ' . $innerViewName),
	      '#size' => 1,
		  '#options' => $allArgumentOptions,
	      '#multiple' => FALSE,
	      '#default_value' => $this->options['pass_through'][$innerViewName],
			);

		}
	}

}
