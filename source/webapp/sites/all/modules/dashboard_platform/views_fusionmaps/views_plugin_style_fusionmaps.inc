<?php
// $Id: views_plugin_style_json.inc,v 1.1.2.11 2010/07/15 07:34:48 allisterbeharry Exp $

/**
 * @file
 * Implementation of views_plugin_style for views_fusionmaps
 */


/**
 * Implementation of views_plugin_style
 */
class views_plugin_style_fusionmaps extends views_plugin_style {
	/**
	 * Implementation of views_plugin_style::option_definition
	 */
	public function option_definition() {

		$options = parent::option_definition();

		$options['map'] = array('default' => array(), 'translatable' => FALSE);
		$options['data'] = array('default' => array(), 'translatable' => FALSE);
		$options['ranges'] = array('default' => array(), 'translatable' => FALSE);

		return $options;
	}

	/**
	 * Provide a form for setting options.
	 *
	 * @param $form
	 * @param $form_state
	 */
	public function options_form(&$form, &$form_state) {

		$fieldNames = $this->display->handler->get_field_labels();
		$fieldNames = array_merge(array(null => 'Select'), $fieldNames);

		$availableMaps = array(
			'FCMap_USA.swf' => 'USA',
			'FCMap_USA.swf_withBoxes' => 'USA with boxes for small states',
			'FCMap_USA2.swf' => 'USA with outlying territories',
			'FCMap_USARegion.swf' => 'USA by region',
			'FCMap_USACentralRegion.swf' => 'USA - Central Region',
			'FCMap_USANorthEastRegion.swf' => 'USA - Northeast Region',
			'FCMap_USANorthWestRegion.swf' => 'USA - Northwest Region',
			'FCMap_USASouthEastRegion.swf' => 'USA - Southeast Region',
			'FCMap_USASouthWestRegion.swf' => 'USA - Southwest Region',
			'FCMap_Canada.swf' => 'Canada',
			'FCMap_NorthAmerica_WOCentral.swf'=> 'North America',
			'FCMap_NorthAmerica.swf' => 'North America (including Caribbean and Central America)',
			'FCMap_CentralAmerica.swf' => 'Central America',
			'FCMap_SouthAmerica.swf' => 'South America',
			'FCMap_Africa.swf' => 'Africa',
			'FCMap_Asia.swf' => 'Asia',
			'FCMap_MiddleEast.swf' => 'Asia (Middle East)',
			'FCMap_Asia3.swf' => 'Asia (South and East)',
			'FCMap_Australia.swf' => 'Australia',
			'FCMap_Europe.swf' => 'Europe by country',
			'FCMap_EuropeRegion.swf' => 'Europe by region',
		);

		$enableDisableOptions = array(1 => 'Enabled',
		                              0 => 'Disabled');

		$yesNoOptions = array(1 => 'Yes',
		                      0 => 'No');

		$form['map'] = array(
    		'#type' => 'fieldset',
    		'#title' => t('Map'),
			'#description' => t('Options regarding the map area as well as general map options.'),
    		'#tree' => true,
  		);

		$form['map']['map_file'] = array(
	    	'#type' => 'select',
	    	'#title' => t('Map'),
	    	'#default_value' => isset($this->options['map']['map_file']) ? $this->options['map']['map_file'] : 'FCMap_USA1.swf',
	    	'#description' => ('The map.'),
	        '#size' => 1,
		    '#options' => $availableMaps,
	        '#multiple' => FALSE,
			);

		$form['map']['width']  = array(
	    	'#type' => 'textfield',
	    	'#title' => t('Width'),
	    	'#default_value' => isset($this->options['map']['width']) ? $this->options['map']['width'] : 500,
	    	'#description' => t('An explicit width for the map.'),
			);

		$form['map']['height']  = array(
	    	'#type' => 'textfield',
	    	'#title' => t('Height'),
	    	'#default_value' => isset($this->options['map']['height']) ? $this->options['map']['height'] : 300,
	    	'#description' => t('An explicit height for the map.'),
			);

		$form['map']['debug_mode']  = array(
	    	'#type' => 'select',
	    	'#title' => t('Debug Mode'),
	    	'#default_value' => isset($this->options['map']['debug_mode']) ? $this->options['map']['debug_mode'] : 0,
	    	'#description' => t('Enable debug mode for the map.'),
	        '#size' => 1,
		    '#options' => $enableDisableOptions,
	        '#multiple' => FALSE,
			);

		$form['map']['hoverColor'] = array(
			'#type' => 'textfield',
			'#title' => 'Hover color',
			'#default_value' => isset($this->options['map']['hoverColor']) ? $this->options['map']['hoverColor'] : NULL,
			'#description' => t('A state or entity will turn this color when you hover over it. Should be a hex triplet without the leading hashmark.'),
			'#maxlength' => 6,
			'#size' => 7
			);

		$form['map']['register_with_js']  = array(
	    	'#type' => 'select',
	    	'#title' => t('Register with JS'),
	    	'#default_value' => isset($this->options['map']['register_with_js']) ? $this->options['map']['register_with_js'] : 1,
	    	'#description' => t('Register this map with javascript events/callbacks.'),
	        '#size' => 1,
		    '#options' => $enableDisableOptions,
	        '#multiple' => FALSE,
			);

		$form['map']['showLabels']  = array(
	    	'#type' => 'select',
	    	'#title' => t('Show Labels'),
	    	'#default_value' => isset($this->options['map']['showLabels']) ? $this->options['map']['showLabels'] : 1,
	    	'#description' => t("It sets the configuration whether the entity labels will be displayed or not."),
	        '#size' => 1,
		    '#options' => $yesNoOptions,
	        '#multiple' => FALSE,
			);

		$form['map']['includeNameInLabels']  = array(
	    	'#type' => 'select',
	    	'#title' => t('Include Name in Labels'),
	    	'#default_value' => isset($this->options['map']['includeNameInLabels']) ? $this->options['map']['includeNameInLabels'] : 0,
	    	'#description' => t("If you've opted to show map labels, this attributes lets you control whether to show entity names as a part of map labels?"),
	        '#size' => 1,
		    '#options' => $yesNoOptions,
	        '#multiple' => FALSE,
			);

		$form['map']['includeValueInLabels']  = array(
	    	'#type' => 'select',
	    	'#title' => t('Include Value in Labels'),
	    	'#default_value' => isset($this->options['map']['includeValueInLabels']) ? $this->options['map']['includeValueInLabels'] : 0,
	    	'#description' => t("Whether to show entity values on the map as a part of map label?"),
	        '#size' => 1,
		    '#options' => $yesNoOptions,
	        '#multiple' => FALSE,
			);
			
		$form['map']['borderColor'] = array(
			'#type' => 'textfield',
			'#title' => t('Border Color'),
			'#description' => t('Hex triplet of color to use for region borders. Please omit the leading hashmark.'),
			'#size' => 8,
			'#maxlength' => 6,
			'#default_value' => isset($this->options['map']['borderColor']) ? $this->options['map']['borderColor'] : '000000',
			'#element_validate' => array('views_fusionmaps_validate_color')
		);

		//DATA section

		$form['data'] = array(
    		'#type' => 'fieldset',
    		'#title' => t('Data'),
			'#description' => t('Options regarding the map data.'),
    		'#tree' => true,
  		);

		$form['data']['entity_id_column'] = array(
	    	'#type' => 'select',
	    	'#title' => t('Entity ID Column'),
	    	'#default_value' => isset($this->options['data']['entity_id_column']) ? $this->options['data']['entity_id_column'] : null,
	    	'#description' => t('The column in the view containing the entity id (for example, in USA maps, an entity is a state, and the entity id would be the two-letter postal code for that state).'),
	        '#size' => 1,
		    '#options' => $fieldNames,
	        '#multiple' => FALSE,
			);

		$form['data']['entity_value_column'] = array(
	    	'#type' => 'select',
	    	'#title' => t('Entity Value Column'),
	    	'#default_value' => isset($this->options['data']['entity_value_column']) ? $this->options['data']['entity_value_column'] : null,
	    	'#description' => t('The column in the view containing a numeric range governing the color of the entity. See "Ranges" below.'),
	        '#size' => 1,
		    '#options' => $fieldNames,
	        '#multiple' => FALSE,
			);

		$form['data']['entity_displayVal_column'] = array(
	    	'#type' => 'select',
	    	'#title' => t('Entity Display Value Column'),
	    	'#default_value' => isset($this->options['data']['entity_displayVal_column']) ? $this->options['data']['entity_displayVal_column'] : null,
	    	'#description' => t('The column in the view containing the entity display value, generally a state or country name.'),
	        '#size' => 1,
		    '#options' => $fieldNames,
	        '#multiple' => FALSE,
			);

		$form['data']['entity_link_column'] = array(
	    	'#type' => 'select',
	    	'#title' => t('Entity Link Column'),
	    	'#default_value' => isset($this->options['data']['entity_link_column']) ? $this->options['data']['entity_link_column'] : null,
	    	'#description' => t('The column in the view containing the URL invoked when the user clicks on an entity.'),
	        '#size' => 1,
		    '#options' => $fieldNames,
	        '#multiple' => FALSE,
			);

		$form['data']['entity_tooltip_column'] = array(
	    	'#type' => 'select',
	    	'#title' => t('Entity Tooltip Column'),
	    	'#default_value' => isset($this->options['data']['entity_tooltip_column']) ? $this->options['data']['entity_tooltip_column'] : null,
	    	'#description' => t('The column in the view containing the tooltip which appears when the user hovers over an entity.'),
	        '#size' => 1,
		    '#options' => $fieldNames,
	        '#multiple' => FALSE,
			);

		//Color Ranges section

		$form['ranges'] = array(
    		'#type' => 'fieldset',
    		'#title' => t('Ranges'),
			'#description' => t('Options regarding the data ranges.'),
    		'#tree' => true,
  		);

  		for($i = 0; $i < 4; $i++) {
  			$this->create_range($form, $i);
  		}
	}

	private function create_range(&$form, $index) {
		$form['ranges'][$index]['min'] = array(
	    	'#type' => 'textfield',
	    	'#title' => t('Range !index - Min', array('!index' => $index + 1)),
	    	'#default_value' => isset($this->options['ranges'][$index]['min']) ? $this->options['ranges'][$index]['min'] : null,
	    	'#description' => t('The minimum for this range.'),
			);

		$form['ranges'][$index]['max'] = array(
	    	'#type' => 'textfield',
	    	'#title' => t('Range !index - Max', array('!index' => $index + 1)),
	    	'#default_value' => isset($this->options['ranges'][$index]['max']) ? $this->options['ranges'][$index]['max'] : null,
	    	'#description' => t('The maximum for this range.'),
			);

		$form['ranges'][$index]['color'] = array(
	    	'#type' => 'textfield',
	    	'#title' => t('Range !index - Color', array('!index' => $index + 1)),
	    	'#default_value' => isset($this->options['ranges'][$index]['color']) ? $this->options['ranges'][$index]['color'] : null,
	    	'#description' => t('The color for this range.'),
			);
	}


	/**
	 * Implementation of view_style_plugin::theme_functions(). Returns an array of theme functions to use
	 * for the current style plugin
	 * @return array
	 */
	public function theme_functions() {
		return views_theme_functions('views_views_fusionmaps_style', $this->view, $this->display);
	}

	/**
	 * Implementation of views_style_plugin::additional_theme_functions(). Returns empty array.
	 * @return array
	 */
	public function additional_theme_functions() {
		return array();
	}
}
