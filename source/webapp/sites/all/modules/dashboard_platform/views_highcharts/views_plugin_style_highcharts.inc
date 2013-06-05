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

		// NOTE: you probably why this is here, as it's in the render function below
		// the reason is that in order for "view" live preview stuff to work
		// the high charts JS must be on the views definition page itself
		// putting it here makes that work (though there is probably a better way
		// to accomplish this

		$options = parent::option_definition();

		$options['chart'] = array('default' => array(), 'translatable' => FALSE);
		$options['legend'] = array('default' => array(), 'translatable' => FALSE);
		$options['plotOptions'] = array('default' => array(), 'translatable' => FALSE);
		$options['tooltip'] = array('default' => array(), 'translatable' => FALSE);
		$options['series'] = array('default' => array(), 'translatable' => FALSE);
		$options['xaxis'] = array('default' => array(), 'translatable' => FALSE);
		$options['yaxis'] = array('default' => array(), 'translatable' => FALSE);
		$options['exporting'] = array('default' => array(), 'translatable' => FALSE);
		return $options;
	}

function sanitize_columns($columns, $fields = NULL) {
    $sanitized = array();
    if ($fields === NULL) {
      $fields = $this->display->handler->get_option('fields');
    }

    // Preconfigure the sanitized array so that the order is retained.
    foreach ($fields as $field => $info) {
      // Set to itself so that if it isn't touched, it gets column
      // status automatically.
      $sanitized[$field] = $field;
    }

    foreach ($columns as $field => $column) {
      // first, make sure the field still exists.
      if (!isset($sanitized[$field])) {
        continue;
      }

      // If the field is the column, mark it so, or the column
      // it's set to is a column, that's ok
      if ($field == $column || $columns[$column] == $column && !empty($sanitized[$column])) {
        $sanitized[$field] = $column;
      }
      // Since we set the field to itself initially, ignoring
      // the condition is ok; the field will get its column
      // status back.
    }

    return $sanitized;
  }

	function _addSeries(&$form, $count) {
		$fieldNames = $this->display->handler->get_field_labels();
		$fieldNamesSelect = array_merge(array(null => 'Select'), $fieldNames);
		$availableCharts = array('area'=> 'Area','areaspline' => 'Area Spline','bar' => 'Bar',
	                             'column' => 'Column', 'line' => 'Line', 'pie' => 'Pie',
	                             'scatter' => 'Scatter', 'spline' => 'Spline');
		$form['series'][$count]['name'] = array(
	    	'#type' => 'textfield',
	    	'#title' => ('Name'),
	    	'#default_value' => isset($this->options['series'][$count]['name']) ? $this->options['series'][$count]['name'] : null,
	    	'#description' => ('The title for this chart.'),
			);
		$form['series'][$count]['chart_type'] = array(
	    	'#type' => 'select',
	    	'#title' => ('Chart Type'),
	    	'#default_value' => isset($this->options['series'][$count]['chart_type']) ? $this->options['series'][$count]['chart_type'] : 'column',
	    	'#description' => ('The chart type.'),
	        '#size' => 1,
		    '#options' => $availableCharts,
	        '#multiple' => FALSE,
			);
		$form['series'][$count]['yaxis_column'] = array(
	    	'#type' => 'select',
	    	'#title' => ('YAxis Column'),
	    	'#default_value' => isset($this->options['series'][$count]['yaxis_column']) ? $this->options['series'][$count]['yaxis_column'] : null,
	    	'#description' => ('The column in the view containing the series values.'),
	        '#size' => 1,
		    '#options' => $fieldNamesSelect,
	        '#multiple' => FALSE,
			);
		$form['series'][$count]['yaxis_point_data_column'] = array(
	    	'#type' => 'select',
	    	'#title' => ('YAxis Point Data Column'),
	    	'#default_value' => isset($this->options['series'][$count]['yaxis_point_data_column']) ? $this->options['series'][$count]['yaxis_point_data_column'] : null,
	    	'#description' => ('The column whose values you want to store in the data point.'),
	        '#size' => 2,
		    '#options' => $fieldNames,
	        '#multiple' => TRUE,
			);
		$form['series'][$count]['visible']  = array(
	    	'#type' => 'select',
	    	'#title' => ('Visible'),
	    	'#default_value' => isset($this->options['series'][$count]['visible']) ? $this->options['series'][$count]['visible'] : 1,
	    	'#description' => t('Set the initial visibility of the series.'),
	        '#size' => 1,
		    '#options' => array(1 => 'Yes', 0 => 'No'),
	        '#multiple' => FALSE,
			);
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
	    $availableCharts = array('area'=> 'Area','areaspline' => 'Area Spline','bar' => 'Bar',
	                             'column' => 'Column', 'line' => 'Line', 'pie' => 'Pie',
	                             'scatter' => 'Scatter', 'spline' => 'Spline');
		$form['chart'] = array(
    		'#type' => 'fieldset',
    		'#title' => t('Chart'),
			'#description' => t('Options regarding the chart area and plot area as well as general chart options.'),
    		'#tree' => true,
  		);
		$form['chart']['chart_title'] = array(
	    	'#type' => 'textfield',
	    	'#title' => t('Title'),
	    	'#default_value' => $this->options['chart']['chart_title'],
	    	'#description' => t('The title for this chart.'),
			);
		$form['chart']['title']= array(
    		'#type' => 'fieldset',
    		'#title' => t('Title Configuration'),
			'#description' => t('The chart\'s main title.'),
    		'#tree' => true,
 			'#collapsible' => TRUE,
  			'#collapsed' => true,
  		);
		$form['chart']['title']['align'] =	array(
	    	'#type' => 'select',
	    	'#title' => t('Align'),
	    	'#default_value' => isset($this->options['chart']['title']['align']) ?$this->options['chart']['title']['align'] : 'center',
	    	'#description' => t('The horizontal alignment of the title'),
	        '#size' => 1,
		    '#options' => array('left' => 'Left', 'center' => 'Center', 'right' => 'Right'),
	        '#multiple' => FALSE,
			);
		$form['chart']['title']['style'] = array(
	    	'#type' => 'textarea',
	    	'#title' => t('Style'),
	    	'#default_value' => isset($this->options['chart']['title']['style']) ? $this->options['chart']['title']['style'] :null,
	    	'#description' => t('CSS styles for the title. Use this for font styling, but use align, x and yfor text alignment. Defaults to:<br/>color: \'#3E576F\',<br/>fontSize: \'16px\''),
			);
		$form['chart']['title']['x'] =	array(
	    	'#type' => 'textfield',
	    	'#title' => t('X'),
	    	'#default_value' => isset($this->options['chart']['title']['x']) ?$this->options['chart']['title']['x'] : null,
	    	'#description' => t('The x position of the title relative to the alignment. Defaults to 0.')
			);
		$form['chart']['title']['y'] =	array(
	    	'#type' => 'textfield',
	    	'#title' => t('Y'),
	    	'#default_value' => isset($this->options['chart']['title']['y']) ?$this->options['chart']['title']['y'] : null,
	    	'#description' => t('The y position of the title relative to the top of the chart. Defaults to 20.')
			);
		$form['chart']['chart_subtitle'] = array(
	    	'#type' => 'textfield',
	    	'#title' => t('Subtitle'),
	    	'#default_value' => $this->options['chart']['chart_subtitle'],
	    	'#description' => t('The subtitle for this chart.'),
			);
		$form['chart']['subtitle']= array(
    		'#type' => 'fieldset',
    		'#title' => t('Subtitle Configuration'),
			'#description' => t('The chart\'s subtitle.'),
    		'#tree' => true,
 			'#collapsible' => TRUE,
  			'#collapsed' => true,
  		);
		$form['chart']['subtitle']['align'] =	array(
	    	'#type' => 'select',
	    	'#title' => t('Align'),
	    	'#default_value' => isset($this->options['chart']['subtitle']['align']) ?$this->options['chart']['subtitle']['align'] : 'center',
	    	'#description' => ('The horizontal alignment of the title'),
	        '#size' => 1,
		    '#options' => array('left' => 'Left', 'center' => 'Center', 'right' => 'Right'),
	        '#multiple' => FALSE,
			);
		$form['chart']['subtitle']['style'] = array(
	    	'#type' => 'textarea',
	    	'#title' => t('Style'),
	    	'#default_value' => isset($this->options['chart']['subtitle']['style']) ? $this->options['chart']['subtitle']['style'] :null,
	    	'#description' => t('CSS styles for the title. Use this for font styling, but use align, x and yfor text alignment. Defaults to:<br/>color: \'#3E576F\',<br/>fontSize: \'16px\''),
			);
		$form['chart']['subtitle']['x'] =	array(
	    	'#type' => 'textfield',
	    	'#title' => t('X'),
	    	'#default_value' => isset($this->options['chart']['subtitle']['x']) ?$this->options['chart']['subtitle']['x'] : null,
	    	'#description' => t('The x position of the title relative to the alignment. Defaults to 0.')
			);
		$form['chart']['subtitle']['y'] =	array(
	    	'#type' => 'textfield',
	    	'#title' => t('Y'),
	    	'#default_value' => isset($this->options['chart']['subtitle']['y']) ?$this->options['chart']['subtitle']['y'] : null,
	    	'#description' => t('The y position of the title relative to the top of the chart. Defaults to 20.')
			);
		$form['chart']['backgroundColor']  = array(
	    	'#type' => 'textfield',
	    	'#title' => ('background Color'),
	    	'#default_value' => isset($this->options['chart']['backgroundColor']) ? $this->options['chart']['backgroundColor'] : null,
	    	'#description' => t('The background color or gradient for the outer chart area. Defaults to "#FFFFFF"'),
			);
		$form['chart']['height']  = array(
	    	'#type' => 'textfield',
	    	'#title' => ('Height'),
	    	'#default_value' => isset($this->options['chart']['height']) ? $this->options['chart']['height'] : null,
	    	'#description' => t('An explicit height for the chart. By default the height is calculated from the offset height of the containing element.'),
			);
		$form['chart']['width']  = array(
	    	'#type' => 'textfield',
	    	'#title' => ('Width'),
	    	'#default_value' => isset($this->options['chart']['width']) ? $this->options['chart']['width'] : null,
	    	'#description' => t('An explicit width for the chart. By default the width is calculated from the offset width of the containing element.'),
			);
		$form['chart']['events'] = array(
    		'#type' => 'fieldset',
    		'#title' => t('Events'),
    		'#tree' => true,
  			'#collapsible' => TRUE,
  			'#collapsed' => true,
  		);
		$form['chart']['events']['addSeries'] = array(
	    	'#type' => 'textarea',
	    	'#title' => t('Add Series'),
	    	'#default_value' => isset($this->options['chart']['events']['addSeries']) ? $this->options['chart']['events']['addSeries'] : null,
	    	'#description' => t('Fires when a series is added to the chart after load time, using the addSeries method. The this keyword refers to the chart object itself. One parameter, event, is passed to the function. This contains common event information based on jQuery.'),
			);
		$form['chart']['events']['click'] = array(
	    	'#type' => 'textarea',
	    	'#title' => ('Click'),
	    	'#default_value' => isset($this->options['chart']['events']['click']) ? $this->options['chart']['events']['click'] : null,
	    	'#description' => t('Fires when clicking on the plot background. The this keyword refers to the chart object itself. One parameter, event, is passed to the function. This contains common event information based on jQuery. Information on the clicked spot can be found through event.xAxis and event.yAxis, which are arrays containing the axes of each dimension and each axis value at the clicked spot. The primary axes are event.xAxis[0] and event.yAxis[0]. Remember the unit of a datetime axis is milliseconds since 1970-01-01 00:00:00.'),
			);
		$form['chart']['events']['load'] = array(
	    	'#type' => 'textarea',
	    	'#title' => ('Load'),
	    	'#default_value' => isset($this->options['chart']['events']['load']) ? $this->options['chart']['events']['load'] : null,
	    	'#description' => t('Fires when the chart is finished loading. The this keyword refers to the chart object itself. One parameter, event, is passed to the function. This contains common event information based on jQuery.'),
			);
		$form['chart']['events']['redraw'] = array(
	    	'#type' => 'textarea',
	    	'#title' => t('Redraw'),
	    	'#default_value' => isset($this->options['chart']['events']['redraw']) ? $this->options['chart']['events']['redraw'] : null,
	    	'#description' => t('Fires when the chart is redrawn, either after a call to chart.redraw() or after an axis, series or point is modified with the redraw option set to true. The this keyword refers to the chart object itself. One parameter, event, is passed to the function. This contains common event information based on jQuery.'),
			);
		$form['chart']['events']['selection'] = array(
	    	'#type' => 'textarea',
	    	'#title' => t('Selection'),
	    	'#default_value' => isset($this->options['chart']['events']['selection']) ? $this->options['chart']['events']['selection'] : null,
	    	'#description' => t('Fires when an area of the chart has been selected. Selection is enabled by setting the charts zoomType.  The this keyword refers to the chart object itself. One parameter, event, is passed to the function. This contains common event information based on jQuery.he default action for the selection event is to zoom the chart to the selected area. It can be prevented by calling event.preventDefault()'),
			);
		$form['chart']['margin'] = array(
    		'#type' => 'fieldset',
    		'#title' => t('Margin'),
  			'#collapsible' => TRUE,
  			'#collapsed' => true,
    		'#tree' => true,
  		);
		$form['chart']['margin']['top']  = array(
	    	'#type' => 'textfield',
	    	'#title' => t('Top'),
	    	'#default_value' => isset($this->options['chart']['margin']['top']) ? $this->options['chart']['margin']['top'] : null,
	    	'#description' => t('The margin between the top outer edge of the chart and the plot area. Defaults to 50'),
			);
		$form['chart']['margin']['right']  = array(
	    	'#type' => 'textfield',
	    	'#title' => t('Right'),
	    	'#default_value' => isset($this->options['chart']['margin']['right']) ? $this->options['chart']['margin']['right'] : null,
	    	'#description' => t('The margin between the right outer edge of the chart and the plot area. Defaults to 50'),
			);
		$form['chart']['margin']['bottom']  = array(
	    	'#type' => 'textfield',
	    	'#title' => t('Bottom'),
	    	'#default_value' => isset($this->options['chart']['margin']['bottom']) ? $this->options['chart']['margin']['bottom'] : null,
	    	'#description' => t('The margin between the bottom outer edge of the chart and the plot area. Defaults to 50'),
			);
		$form['chart']['margin']['left']  = array(
	    	'#type' => 'textfield',
	    	'#title' => t('Left'),
	    	'#default_value' => isset($this->options['chart']['margin']['left']) ? $this->options['chart']['margin']['left'] : null,
	    	'#description' => t('The margin between the left outer edge of the chart and the plot area. Defaults to 50'),
			);
		$form['chart']['plot'] = array(
    		'#type' => 'fieldset',
    		'#title' => t('Plot Options'),
  			'#collapsible' => TRUE,
  			'#collapsed' => true,
    		'#tree' => true,
  		);
		$form['chart']['plot']['backgroundColor']  = array(
	    	'#type' => 'colorpicker_textfield',
	    	'#title' => t('Background Color'),
	    	'#default_value' => isset($this->options['chart']['plot']['backgroundColor']) ? $this->options['chart']['plot']['backgroundColor'] : null,
	    	'#description' => t('The background color or gradient for the plot area. Defaults to null'),
			);
		$form['chart']['plot']['backgroundImage']  = array(
	    	'#type' => 'textfield',
	    	'#title' => t('Background Image'),
	    	'#default_value' => isset($this->options['chart']['plot']['backgroundImage']) ? $this->options['chart']['plot']['backgroundImage'] : null,
	    	'#description' => t('The URL for an image to use as the plot background. To set an image as the background for the entire chart, set a CSS background image to the container element. Defaults to null'),
			);
		$form['chart']['plot']['borderColor']  = array(
	    	'#type' => 'colorpicker_textfield',
	    	'#title' => t('Border Color'),
	    	'#default_value' => isset($this->options['chart']['plot']['borderColor']) ? $this->options['chart']['plot']['borderColor'] : null,
	    	'#description' => t('The background color or gradient for the plot area. Defaults to "#C0C0C0"'),
			);
		$form['chart']['plot']['borderWidth']  = array(
	    	'#type' => 'textfield',
	    	'#title' => t('Border Width'),
	    	'#default_value' => isset($this->options['chart']['plot']['borderWidth']) ? $this->options['chart']['plot']['borderWidth'] : null,
	    	'#description' => t('The pixel width of the plot area border. Defaults to 0.'),
			);
		$form['chart']['plot']['shadow']  = array(
	    	'#type' => 'select',
	    	'#title' => ('Shadow'),
	    	'#default_value' => isset($this->options['chart']['plot']['shadow']) ? $this->options['chart']['plot']['shadow'] : 0,
	    	'#description' => t('Whether to apply a drop shadow to the plot area. Requires that plotBackgroundColor be set.'),
	        '#size' => 1,
		    '#options' => array(1 => 'Yes', 0 => 'No'),
	        '#multiple' => FALSE,
			);
		$form['chart']['advanced'] = array(
    		'#type' => 'fieldset',
    		'#title' => t('Advanced Settings'),
  			'#collapsible' => TRUE,
  			'#collapsed' => true,
    		'#tree' => true,
  		);
		$form['chart']['advanced']['alignTicks'] = array(
	    	'#type' => 'select',
	    	'#title' => t('Align Ticks'),
	    	'#default_value' => isset($this->options['chart']['advanced']['alignTicks']) ? $this->options['chart']['advanced']['alignTicks']: 0,
	    	'#description' => t('When using multiple axis, the ticks of two or more opposite axes will automatically be aligned by adding ticks to the axis or axes with the least ticks. This can be prevented by setting alignTicks to false. If the grid lines look messy, it"s a good idea to hide them for the secondary axis by setting gridLineWidth to 0. Defaults to true.'),
	        '#size' => 1,
		    '#options' => array(1 => 'Yes', 0 => 'No'),
	        '#multiple' => FALSE,
			);
		$form['chart']['advanced']['backgroundColor'] = array(
	    	'#type' => 'colorpicker_textfield',
	    	'#title' => t('Background Color'),
	    	'#default_value' => isset($this->options['chart']['advanced']['backgroundColor']) ? $this->options['chart']['advanced']['backgroundColor'] :null,
	    	'#description' => t('The background color or gradient for the outer chart area. Defaults to "#FFFFFF".'),
			);
		$form['chart']['advanced']['borderColor'] = array(
	    	'#type' => 'colorpicker_textfield',
	    	'#title' => t('Border Color'),
	    	'#default_value' => isset($this->options['chart']['advanced']['borderColor']) ? $this->options['chart']['advanced']['borderColor'] :null,
	    	'#description' => t('The color of the outer chart border. The border is painted using vector graphic techniques to allow rounded corners.'),
			);
		$form['chart']['advanced']['borderRadius'] = array(
	    	'#type' => 'textfield',
	    	'#title' => t('Border Radius'),
	    	'#default_value' => isset($this->options['chart']['advanced']['borderRadius']) ? $this->options['chart']['advanced']['borderRadius'] :'5',
	    	'#description' => t('The corner radius of the outer chart border. Defaults to 5.'),
			);
		$form['chart']['advanced']['borderWidth'] = array(
	    	'#type' => 'textfield',
	    	'#title' => ('Border Width'),
	    	'#default_value' => isset($this->options['chart']['advanced']['borderWidth']) ? $this->options['chart']['advanced']['borderWidth'] :'5',
	    	'#description' => t('The pixel width of the outer chart border. The border is painted using vector graphic techniques to allow rounded corners. Defaults to 0.'),
			);
		$form['chart']['advanced']['className'] = array(
	    	'#type' => 'textfield',
	    	'#title' => ('Class Name'),
	    	'#default_value' => isset($this->options['chart']['advanced']['className']) ? $this->options['chart']['advanced']['className'] : "",
	    	'#description' => t('A CSS class name to apply to the charts container div, allowing unique CSS styling for each chart. Defaults to ""'),
			);
		$form['chart']['advanced']['ignoreHiddenSeries']  = array(
	    	'#type' => 'select',
	    	'#title' => ('Ignore Hidden Series'),
	    	'#default_value' => isset($this->options['chart']['advanced']['ignoreHiddenSeries']) ? $this->options['chart']['advanced']['ignoreHiddenSeries'] : 1,
	    	'#description' => t('If true, the axes will scale to the remaining visible series once one series is hidden. If false, hiding and showing a series will not affect the axes or the other series. For stacks, once one series within the stack is hidden, the rest of the stack will close in around it even if the axis is not affected.'),
	        '#size' => 1,
		    '#options' => array(1 => 'Yes', 0 => 'No'),
	        '#multiple' => FALSE,
			);
		$form['chart']['advanced']['inverted']  = array(
	    	'#type' => 'select',
	    	'#title' => ('Inverted'),
	    	'#default_value' => isset($this->options['chart']['advanced']['inverted']) ? $this->options['chart']['advanced']['inverted'] : 0,
	    	'#description' => t('Whether to invert the axes so that the x axis is horizontal and y axis is vertical. When true, the x axis is reversed by default. If a bar plot is present in the chart, it will be inverted automatically. '),
	        '#size' => 1,
		    '#options' => array(1 => 'Yes', 0 => 'No'),
	        '#multiple' => FALSE,
			);
		$form['chart']['advanced']['shadow']  = array(
	    	'#type' => 'select',
	    	'#title' => ('Shadow'),
	    	'#default_value' => isset($this->options['chart']['advanced']['shadow']) ? $this->options['chart']['advanced']['shadow'] : 0,
	    	'#description' => t('Whether to apply a drop shadow to the outer chart area. Requires that backgroundColor be set.'),
	        '#size' => 1,
		    '#options' => array(1 => 'Yes', 0 => 'No'),
	        '#multiple' => FALSE,
			);
		$form['chart']['advanced']['showAxis']  = array(
	    	'#type' => 'select',
	    	'#title' => ('Show Axis'),
	    	'#default_value' => isset($this->options['chart']['advanced']['showAxis']) ? $this->options['chart']['advanced']['showAxis'] : 0,
	    	'#description' => t('Whether to show the axes initially. This only applies to empty charts where series are added dynamically, as axes are automatically added to cartesian series.'),
	        '#size' => 1,
		    '#options' => array(1 => 'Yes', 0 => 'No'),
	        '#multiple' => FALSE,
			);
		$form['chart']['advanced']['style']  = array(
	    	'#type' => 'textarea',
	    	'#title' => t('Style'),
	    	'#default_value' => isset($this->options['chart']['advanced']['style']) ? $this->options['chart']['advanced']['style'] : null,
	    	'#description' => t('Additional CSS styles to apply inline to the container div.Defaults to: <br/>fontFamily: \'"Lucida Grande", "Lucida Sans Unicode", Verdana, Arial, Helvetica, sans-serif\',<br/>fontSize: \'12px\''),
			);
		$form['chart']['advanced']['zoomType']  = array(
	    	'#type' => 'select',
	    	'#title' => ('Zoom Type'),
	    	'#default_value' => isset($this->options['chart']['advanced']['zoomType']) ? $this->options['chart']['advanced']['zoomType'] : 'x',
	    	'#description' => t('Decides in what dimentions the user can zoom by dragging the mouse. Can be one of x, y or xy'),
	        '#size' => 1,
		    '#options' => array('x' => 'X', 'y' => 'Y', 'xy' => 'XY', '' => 'None'),
	        '#multiple' => FALSE,
			);
		$form['legend'] = array(
    		'#type' => 'fieldset',
    		'#title' => t('Legend'),
    		'#tree' => true,
  			'#collapsible' => TRUE,
  			'#collapsed' => true,
  		);
		$form['legend']['align'] =	array(
	    	'#type' => 'select',
	    	'#title' => ('Align'),
	    	'#default_value' => isset($this->options['legend']['align']) ?$this->options['legend']['align'] : 'center',
	    	'#description' => ('The horizontal alignment of the legend box within the chart area.'),
	        '#size' => 1,
		    '#options' => array('left' => 'Left', 'center' => 'Center', 'right' => 'Right'),
	        '#multiple' => FALSE,
			);
		$form['legend']['enabled'] =	array(
	    	'#type' => 'select',
	    	'#title' => ('Enabled'),
	    	'#default_value' => isset($this->options['legend']['enabled']) ?$this->options['legend']['enabled'] : 1,
	    	'#description' => ('Enable or disable the legend.'),
	        '#size' => 1,
		    '#options' => array(1 => 'Yes', 0 => 'No'),
	        '#multiple' => FALSE,
			);
		$form['legend']['layout'] =	array(
	    	'#type' => 'select',
	    	'#title' => ('Layout'),
	    	'#default_value' => isset($this->options['legend']['layout']) ?$this->options['legend']['layout'] : "horizontal",
	    	'#description' => ('The layout of the legend items.'),
	        '#size' => 1,
		    '#options' => array('horizontal' => 'Horizontal', 'vertical' => 'Vertical'),
	        '#multiple' => FALSE,
			);
		$form['legend']['verticalAlign'] =	array(
	    	'#type' => 'select',
	    	'#title' => ('Vertical Align'),
	    	'#default_value' => isset($this->options['legend']['verticalAlign']) ?$this->options['legend']['verticalAlign'] : 'bottom',
	    	'#description' => ('The vertical alignment of the legend box. Can be one of "top", "middle" or "bottom". Vertical position can be further determined by the y option. Defaults to "bottom".'),
	        '#size' => 1,
		    '#options' => array('top' => 'Top', 'middle' => 'Middle', 'bottom' => 'Bottom'),
	        '#multiple' => FALSE,
			);
		$form['legend']['width'] =	array(
	    	'#type' => 'textfield',
	    	'#title' => ('Width'),
	    	'#default_value' => $this->options['legend']['width'],
	    	'#description' => ('The width of the legend box, not including style.padding. Defaults to null.')
			);
		$form['legend']['x'] =	array(
	    	'#type' => 'textfield',
	    	'#title' => ('X'),
	    	'#default_value' => isset($this->options['legend']['x']) ?$this->options['legend']['x'] : 15,
	    	'#description' => ('The x offset of the legend relative to it\'s horizontal alignment align. Negative x moves it to the left, positive x moves it to the right. The default value of 15 together with align: "center" puts it in the center of the plot area. Defaults to 15.')
			);
		$form['legend']['y'] =	array(
	    	'#type' => 'textfield',
	    	'#title' => ('Y'),
	    	'#default_value' => isset($this->options['legend']['y']) ?$this->options['legend']['y'] : 0,
	    	'#description' => ('The vertical offset of the legend relative to it\'s vertical alignment verticalAlign. Negative y moves it up, positive y moves it down. Defaults to 0.')
			);
		$form['legend']['advanced'] = array(
    		'#type' => 'fieldset',
    		'#title' => t('Advanced Settings'),
		    '#description' => t('The legend is a box containing a symbol and name for each series item or point item in the chart.'),
  			'#collapsible' => TRUE,
  			'#collapsed' => true,
    		'#tree' => true,
  		);
		$form['legend']['advanced']['backgroundColor'] = array(
	    	'#type' => 'colorpicker_textfield',
	    	'#title' => t('Background Color'),
	    	'#default_value' => isset($this->options['legend']['advanced']['backgroundColor']) ? $this->options['legend']['advanced']['backgroundColor'] :null,
	    	'#description' => t('The background color of the legend, filling the rounded corner border. Defaults to null.'),
			);
		$form['legend']['advanced']['borderColor'] = array(
	    	'#type' => 'colorpicker_textfield',
	    	'#title' => t('Border Color'),
	    	'#default_value' => isset($this->options['legend']['advanced']['borderColor']) ? $this->options['legend']['advanced']['borderColor'] :null,
	    	'#description' => t('The color of the drawn border around the legend. Defaults to #909090.'),
			);
		$form['legend']['advanced']['borderRadius'] = array(
	    	'#type' => 'textfield',
	    	'#title' => t('Border Radius'),
	    	'#default_value' => isset($this->options['legend']['advanced']['borderRadius']) ? $this->options['legend']['advanced']['borderRadius'] :null,
	    	'#description' => t('The border corner radius of the legend. Defaults to 5.'),
			);
		$form['legend']['advanced']['borderWidth'] = array(
	    	'#type' => 'textfield',
	    	'#title' => t('Border Width'),
	    	'#default_value' => isset($this->options['legend']['advanced']['borderWidth']) ? $this->options['legend']['advanced']['borderWidth'] :null,
	    	'#description' => t('The width of the drawn border around the legend. Defaults to 1'),
			);
		$form['legend']['advanced']['itemHiddenStyle'] = array(
	    	'#type' => 'textarea',
	    	'#title' => t('Item Hidden Style'),
	    	'#default_value' => isset($this->options['legend']['advanced']['itemHiddenStyle']) ? $this->options['legend']['advanced']['itemHiddenStyle'] :null,
	    	'#description' => t('CSS styles for each legend item when the corresponding series or point is hidden. Properties are inherited from style unless overridden here. Defaults to:<br/>color: \'#CCC\''),
			);
		$form['legend']['advanced']['itemHoverStyle '] = array(
	    	'#type' => 'textarea',
	    	'#title' => t('Item Hover Style'),
	    	'#default_value' => isset($this->options['legend']['advanced']['itemHoverStyle']) ? $this->options['legend']['advanced']['itemHoverStyle'] :null,
	    	'#description' => t('CSS styles for each legend item in hover mode. Properties are inherited from style unless overridden here. Defaults to:<br/>color: \'#000\''),
			);
		$form['legend']['advanced']['itemStyle'] = array(
	    	'#type' => 'textarea',
	    	'#title' => t('Item Style'),
	    	'#default_value' => isset($this->options['legend']['advanced']['itemStyle']) ? $this->options['legend']['advanced']['itemStyle'] :null,
	    	'#description' => t('CSS styles for each legend item. Defaults to:<br/>cursor: \'pointer\'<br/>color: \'#3E576F\''),
			);
		$form['legend']['advanced']['itemWidth'] = array(
	    	'#type' => 'textfield',
	    	'#title' => t('Border Width'),
	    	'#default_value' => isset($this->options['legend']['advanced']['itemWidth']) ? $this->options['legend']['advanced']['itemWidth'] :null,
	    	'#description' => t('The width for each legend item. This is useful in a horizontal layout with many items when you want the items to align vertically. Defaults to null'),
			);
		$form['legend']['advanced']['labelFormatter'] = array(
	    	'#type' => 'textarea',
	    	'#title' => t('Label Formatter'),
	    	'#default_value' => isset($this->options['legend']['advanced']['labelFormatter']) ? $this->options['legend']['advanced']['labelFormatter'] :null,
	    	'#description' => t('Callback function to format each of the series\' labels. The this keyword refers to the series object, or the point object in case of pie charts. Defaults to return this.name'),
			);
		$form['legend']['advanced']['lineHeight'] = array(
	    	'#type' => 'textfield',
	    	'#title' => t('Line Height'),
	    	'#default_value' => isset($this->options['legend']['advanced']['lineHeight']) ? $this->options['legend']['advanced']['lineHeight'] :null,
	    	'#description' => t('Line height for the legend items. Defaults to 16'),
			);
		$form['legend']['advanced']['reversed'] =	array(
	    	'#type' => 'select',
	    	'#title' => t('Reversed'),
	    	'#default_value' => isset($this->options['legend']['advanced']['reversed']) ?$this->options['legend']['advanced']['reversed'] : 0,
	    	'#description' => ('Whether to reverse the order of the legend items compared to the order of the series or points as defined in the configuration object. Defaults to false.'),
	        '#size' => 1,
		    '#options' => array(1 => 'Yes', 0 => 'No'),
	        '#multiple' => FALSE,
			);
		$form['legend']['advanced']['shadow'] =	array(
	    	'#type' => 'select',
	    	'#title' => t('Shadow'),
	    	'#default_value' => isset($this->options['legend']['advanced']['shadow']) ?$this->options['legend']['advanced']['shadow'] : 0,
	    	'#description' => t('Whether to apply a drop shadow to the legend. A backgroundColor also needs to be applied for this to take effect. Defaults to false.'),
	        '#size' => 1,
		    '#options' => array(1 => 'Yes', 0 => 'No'),
	        '#multiple' => FALSE,
			);
		$form['legend']['advanced']['style'] = array(
	    	'#type' => 'textarea',
	    	'#title' => t('Style'),
	    	'#default_value' => isset($this->options['legend']['advanced']['style']) ? $this->options['legend']['advanced']['style'] :null,
	    	'#description' => t('CSS styles for each legend area.'),
			);
		$form['legend']['advanced']['symbolPadding'] = array(
	    	'#type' => 'textfield',
	    	'#title' => t('Symbol Padding'),
	    	'#default_value' => isset($this->options['legend']['advanced']['symbolPadding']) ? $this->options['legend']['advanced']['symbolPadding'] :null,
	    	'#description' => t('The pixel padding between the legend item symbol and the legend item text. Defaults to 5.'),
			);
		$form['legend']['advanced']['symbolWidth'] = array(
	    	'#type' => 'textfield',
	    	'#title' => ('Symbol Width'),
	    	'#default_value' => isset($this->options['legend']['advanced']['symbolWidth']) ? $this->options['legend']['advanced']['symbolWidth'] :null,
	    	'#description' => t('The pixel width of the legend item symbol. Defaults to 30'),
			);
		$form['loading'] = array(
    		'#type' => 'fieldset',
    		'#title' => t('Loading'),
			'#description' => t('The loading options control the appearance of the loading screen that covers the plot area on chart operations. This screen only appears after an explicit call to chart.showLoading(). It is a utility for developers to communicate to the end user that something is going on, for example while retrieving new data via an XHR connection. The "Loading..." text itself is not part of this configuration object, but part of the lang object.'),
    		'#tree' => true,
  			'#collapsible' => TRUE,
  			'#collapsed' => true,
  			);
		$form['loading']['hideDuration'] = array(
	    	'#type' => 'textfield',
	    	'#title' => ('Hide Duration'),
	    	'#default_value' => isset($this->options['loading']['hideDuration']) ? $this->options['loading']['hideDuration'] :null,
	    	'#description' => t('The duration in milliseconds of the fade out effect. Defaults to 100'),
			);
		$form['loading']['labelStyle'] = array(
	    	'#type' => 'textarea',
	    	'#title' => ('Label Style'),
	    	'#default_value' => isset($this->options['loading']['labelStyle']) ? $this->options['loading']['labelStyle'] :null,
	    	'#description' => t('CSS styles for the loading label span. Defaults to:<br/>fontWeight: \'bold\'<br/>position: \'relative\',<br/>top: \'1em\''),
			);
		$form['loading']['showDuration'] = array(
	    	'#type' => 'textfield',
	    	'#title' => ('Show Duration'),
	    	'#default_value' => isset($this->options['loading']['showDuration']) ? $this->options['loading']['showDuration'] :null,
	    	'#description' => t('The duration in milliseconds of the fade in effect. Defaults to 100'),
			);
		$form['loading']['style'] = array(
	    	'#type' => 'textarea',
	    	'#title' => ('Style'),
	    	'#default_value' => isset($this->options['loading']['labelStyle']) ? $this->options['loading']['labelStyle'] :null,
	    	'#description' => t('CSS styles for the loading screen that covers the plot area. Defaults to:<br/>position: \'absolute\',<br/>backgroundColor: \'white\',<br/>opacity: 0.5,textAlign: \'center\''),
			);

		$form['plotOptions'] = array(
    		'#type' => 'fieldset',
    		'#title' => t('Plot Options'),
    		'#tree' => true,
  		);
		$form['plotOptions']['series'] = array(
    		'#type' => 'fieldset',
    		'#title' => t('Series'),
    		'#tree' => true,
  		);
  		$form['plotOptions']['series']['stacking']	= array(
	    	'#type' => 'select',
	    	'#title' => t('Stacking'),
	    	'#default_value' => isset($this->options['plotOptions']['series']['stacking']) ? $this->options['plotOptions']['series']['stacking']: null,
	    	'#description' => t('Whether to stack the values of each series on top of each other. Possible values are null to disable, "normal" to stack by value or "percent". Defaults to null. Defaults to null.'),
	        '#size' => 1,
		    '#options' => array(null => 'Disable', 'normal' => 'Normal', 'percent' => 'Percent'),
	        '#multiple' => FALSE,
			);
		$form['plotOptions']['series']['colorByPoint']	= array(
	    	'#type' => 'select',
	    	'#title' => ('color By Point'),
	    	'#default_value' => isset($this->options['plotOptions']['series']['colorByPoint']) ? $this->options['plotOptions']['series']['colorByPoint'] : 0,
	    	'#description' => ('This option determines whether the chart should receive one color per series or one color per point. Defaults to false.'),
	        '#size' => 1,
		    '#options' => array(1 => 'Yes', 0 => 'No'),
	        '#multiple' => FALSE,
			);
		$form['plotOptions']['series']['pointWidth']	= array(
	    	'#type' => 'textfield',
	    	'#title' => ('pointWidth'),
	    	'#default_value' => isset($this->options['plotOptions']['series']['pointWidth']) ? $this->options['plotOptions']['series']['pointWidth'] : null,
	    	'#description' => ('A pixel value specifying a fixed width for each column or bar. When null, the width is calculated from the pointPadding and groupPadding. Defaults to null.'),
			);
        $form['plotOptions']['series']['cursor']	= array(
	    	'#type' => 'textfield',
	    	'#title' => ('Cursor'),
	    	'#default_value' => isset($this->options['plotOptions']['series']['cursor']) ? $this->options['plotOptions']['series']['cursor'] : null,
	    	'#description' => ('You can set the cursor to \"pointer\" if you have click events attached to the series, to signal to the user that the points and lines can be clicked. Defaults to null.'),
			);
		 $form['plotOptions']['series']['events']	= array(
	    	'#type' => 'textarea',
	    	'#title' => ('Events click  : Function'),
	    	'#default_value' => isset($this->options['plotOptions']['series']['events']) ? $this->options['plotOptions']['series']['events'] : null,
	    	'#description' => ('Fires when the series is clicked. The this keyword refers to the series object itself. One parameter, event, is passed to the function. This contains common event information based on jQuery or MooTools depending on which library is used as the base for Highcharts. Additionally, event.point  holds a pointer to the nearest point on the graph.'),
			);

		$form['series'] = array(
    		'#type' => 'fieldset',
    		'#title' => t('Series'),
    		'#tree' => true,
  		);
  		for($i = 0 ; $i < 6; $i++) {
  			$this->_addSeries($form, $i);
  		}
  		$form['tooltip']= array(
    		'#type' => 'fieldset',
    		'#title' => t('Tooltip'),
  		    '#description' => t('Options for the tooltip that appears when the user hovers over a series or point.'),
    		'#tree' => true,
 			'#collapsible' => TRUE,
  			'#collapsed' => true,
  		);
		$form['tooltip']['enabled'] =	array(
	    	'#type' => 'select',
	    	'#title' => ('Enabled'),
	    	'#default_value' => isset($this->options['tooltip']['enabled']) ?$this->options['tooltip']['enabled'] : 1,
	    	'#description' => ('Enable or disable the legend.'),
	        '#size' => 1,
		    '#options' => array(1 => 'Yes', 0 => 'No'),
	        '#multiple' => FALSE,
			);
  		$form['tooltip']['formatter'] = array(
  			'#type' => 'textarea',
	    	'#title' => t('Formatter'),
	    	'#default_value' => isset($this->options['tooltip']['formatter']) ? $this->options['tooltip']['formatter'] : null,
	    	'#description' => ('Callback function to format the HTML of the tooltip. Return false to disable tooltip for a specific point on series.'),
  		);
  		$form['tooltip']['advanced']= array(
    		'#type' => 'fieldset',
    		'#title' => t('Advanced Section'),
    		'#tree' => true,
 			'#collapsible' => TRUE,
  			'#collapsed' => true,
  		);
		$form['tooltip']['advanced']['backgroundColor'] = array(
	    	'#type' => 'colorpicker_textfield',
	    	'#title' => t('Background Color'),
	    	'#default_value' => isset($this->options['tooltip']['advanced']['backgroundColor']) ? $this->options['tooltip']['advanced']['backgroundColor'] :null,
	    	'#description' => t('The background color or gradient for the tooltip. Defaults to "rgba(255, 255, 255, .85)"'),
			);
		$form['tooltip']['advanced']['borderColor'] = array(
	    	'#type' => 'colorpicker_textfield',
	    	'#title' => t('Border Color'),
	    	'#default_value' => isset($this->options['tooltip']['advanced']['borderColor']) ? $this->options['tooltip']['advanced']['borderColor'] :null,
	    	'#description' => t('The color of the tooltip border. When null, the border takes the color of the corresponding series or point. Defaults to "auto"'),
			);
		$form['tooltip']['advanced']['borderRadius'] = array(
	    	'#type' => 'textfield',
	    	'#title' => t('Border Radius'),
	    	'#default_value' => isset($this->options['tooltip']['advanced']['borderRadius']) ? $this->options['tooltip']['advanced']['borderRadius'] :null,
	    	'#description' => t('The border corner radius of the legend. Defaults to 5.'),
			);
		$form['tooltip']['advanced']['borderWidth'] = array(
	    	'#type' => 'textfield',
	    	'#title' => t('Border Width'),
	    	'#default_value' => isset($this->options['tooltip']['advanced']['borderWidth']) ? $this->options['tooltip']['advanced']['borderWidth'] :null,
	    	'#description' => t('The pixel width of the tooltip border. Defaults to 2.'),
			);
		$form['tooltip']['advanced']['shadow'] =	array(
	    	'#type' => 'select',
	    	'#title' => t('Shadow'),
	    	'#default_value' => isset($this->options['tooltip']['advanced']['shadow']) ?$this->options['tooltip']['advanced']['shadow'] : 1,
	    	'#description' => t('Whether to apply a drop shadow to the tooltip. Defaults to true.'),
	        '#size' => 1,
		    '#options' => array(1 => 'Yes', 0 => 'No'),
	        '#multiple' => FALSE,
			);
		$form['tooltip']['advanced']['snap'] = array(
	    	'#type' => 'textfield',
	    	'#title' => t('Snap'),
	    	'#default_value' => isset($this->options['tooltip']['advanced']['snap']) ? $this->options['tooltip']['advanced']['snap'] :null,
	    	'#description' => t('Proximity snap for graphs or single points. Does not apply to bars, columns and pie slices. Defaults to 10.'),
			);
		$form['tooltip']['advanced']['style'] = array(
	    	'#type' => 'textarea',
	    	'#title' => t('Style'),
	    	'#default_value' => isset($this->options['tooltip']['advanced']['style']) ? $this->options['tooltip']['advanced']['style'] :null,
	    	'#description' => t('CSS styles for the tooltip. The tooltip can also be styled through the CSS class .highcharts-tooltip. Default value: 	color: \'#333333\',<br/>fontSize: \'9pt\',<br/>padding: \'5px\''),
			);

		$form['xaxis']= array(
    		'#type' => 'fieldset',
    		'#title' => t('X Axis'),
    		'#tree' => true,
  		);
  		$form['xaxis']['type']	= array(
	    	'#type' => 'select',
	    	'#title' => ('Type'),
	    	'#default_value' => $this->options['xaxis']['type'],
	    	'#description' => ('The type of axis. Can be one of "linear" or "datetime". In a datetime axis, the numbers are given in milliseconds, and tick marks are placed on appropriate values like full hours or days. Defaults to "linear"'),
	        '#size' => 1,
		    '#options' => array('linear' => 'Linear', 'datetime' => 'Date Time'),
	        '#multiple' => FALSE,
			);
		$form['xaxis']['xaxis_column'] = array(
	    	'#type' => 'select',
	    	'#title' => ('XAxis Column'),
	    	'#default_value' => $this->options['xaxis']['xaxis_column'],
	    	'#description' => ('The column in the view containing the category values.'),
	        '#size' => 1,
		    '#options' => $fieldNames,
	        '#multiple' => FALSE,
			);
		$form['xaxis']['allowDecimals']	= array(
	    	'#type' => 'select',
	    	'#title' => t('Allow Decimals'),
	    	'#default_value' => isset($this->options['xaxis']['allowDecimals']) ?$this->options['xaxis']['allowDecimals'] : 1,
	    	'#description' => t('Whether to allow decimals in this axis\' ticks. When counting integers, like persons or hits on a web page, decimals must be avoided in the axis tick labels. Defaults to true.'),
	        '#size' => 1,
		    '#options' => array(1 => 'Yes', 0 => 'No'),
	        '#multiple' => FALSE,
			);
		$form['xaxis']['alternateGridColor'] = array(
	    	'#type' => 'colorpicker_textfield',
	    	'#title' => t('Alternate Grid Color'),
	    	'#default_value' => isset($this->options['xaxis']['alternateGridColor']) ? $this->options['xaxis']['alternateGridColor'] :null,
	    	'#description' => t('When using an alternate grid color, a band is painted across the plot area between every other grid line. Defaults to null.'),
			);
	    $form['xaxis']['startOnTick'] = array(
	    	'#type' => 'select',
	    	'#title' => ('startOnTick'),
	    	'#default_value' => isset($this->options['xaxis']['startOnTick']) ? $this->options['xaxis']['startOnTick'] : 0,
	    	'#description' => ('Whether to force the axis to start on a tick. Use this option with the maxPadding  option to control the axis start. Defaults to false.'),
	        '#size' => 1,
		    '#options' => array(1 => 'Yes', 0 => 'No'),
	        '#multiple' => FALSE,
			);
		$form['xaxis']['tickLength'] = array(
	    	'#type' => 'textfield',
	    	'#title' => t('tickLength'),
	    	'#default_value' => isset($this->options['xaxis']['tickLength']) ? $this->options['xaxis']['tickLength'] :5,
	    	'#description' => t('The pixel length of the main tick marks. Defaults to 5.'),
			);
		$form['xaxis']['maxPadding'] = array(
	    	'#type' => 'textfield',
	    	'#title' => t('Max Padding'),
	    	'#default_value' => isset($this->options['xaxis']['maxPadding']) ? $this->options['xaxis']['maxPadding'] :null,
	    	'#description' => t('Padding of the max value relative to the length of the axis. A padding of 0.05 will make a 100px axis 5px longer. This is useful when you don\'t want the highest data value to appear on the edge of the plot area. When the axis\' max option is set or a max extreme is set using axis.setExtremes(), the maxPadding will be ignored. Defaults to 0.01.'),
			);
		$form['xaxis']['minPadding'] = array(
	    	'#type' => 'textfield',
	    	'#title' => t('Min Padding'),
	    	'#default_value' => isset($this->options['xaxis']['minPadding']) ? $this->options['xaxis']['minPadding'] :null,
	    	'#description' => t('Padding of the min value relative to the length of the axis. A padding of 0.05 will make a 100px axis 5px longer. This is useful when you don\'t want the lowest data value to appear on the edge of the plot area. When the axis\' min option is set or a min extreme is set using axis.setExtremes(), the minPadding will be ignored. Defaults to 0.01'),
			);
		$form['xaxis']['tickInterval'] = array(
	    	'#type' => 'textfield',
	    	'#title' => t('Tick Interval'),
	    	'#default_value' => isset($this->options['xaxis']['tickInterval']) ? $this->options['xaxis']['tickInterval'] :null,
	    	'#description' => t('The interval of the tick marks in axis units. When null, the tick interval is computed to approximately follow the tickPixelInterval. Not applicable to categorized axis. Note that datetime axes are based on milliseconds, so for example an interval of one day is expressed as 24 * 3600 * 1000.'),
			);

		$form['xaxis']['labels']= array(
    		'#type' => 'fieldset',
    		'#title' => t('Labels'),
    		'#tree' => true,
  		);
		$form['xaxis']['labels']['align']	= array(
	    	'#type' => 'select',
	    	'#title' => ('Align'),
	    	'#default_value' => isset($this->options['xaxis']['labels']['align']) ?$this->options['xaxis']['labels']['align'] : 'right',
	    	'#description' => ('Alignment of the title relative to the axis values.'),
	        '#size' => 1,
		    '#options' => array('left' => 'Left', 'center' => 'Center', 'right' => 'Right'),
	        '#multiple' => FALSE,
			);
		$form['xaxis']['labels']['enabled'] =	array(
	    	'#type' => 'select',
	    	'#title' => ('Enabled'),
	    	'#default_value' => isset($this->options['xaxis']['labels']['enabled']) ?$this->options['xaxis']['labels']['enabled'] : 1,
	    	'#description' => ('Enable or disable the labels.'),
	        '#size' => 1,
		    '#options' => array(1 => 'Yes', 0 => 'No'),
	        '#multiple' => FALSE,
			);
		$form['xaxis']['labels']['formatter'] = array(
  			'#type' => 'textarea',
	    	'#title' => t('Formatter'),
	    	'#default_value' => isset($this->options['xaxis']['labels']['formatter']) ? $this->options['xaxis']['labels']['formatter'] : null,
	    	'#description' => t('Callback function to format the HTML of the tooltip. Return false to disable tooltip for a specific point on series.'),
  		);
		$form['xaxis']['labels']['rotation'] = array(
	    	'#type' => 'textfield',
	    	'#title' => t('Rotation'),
	    	'#default_value' => isset($this->options['xaxis']['labels']['rotation']) ? $this->options['xaxis']['labels']['rotation']: 0,
	    	'#description' => t('The rotation of the text in degrees. 0 is horizontal, 270 is vertical reading from bottom to top. Defaults to 0.'),
			);
		$form['xaxis']['labels']['x'] = array(
	    	'#type' => 'textfield',
	    	'#title' => t('x'),
	    	'#default_value' => isset($this->options['xaxis']['labels']['x']) ? $this->options['xaxis']['labels']['x']: 0,
	    	'#description' => t('The x position offset of the label relative to the tick position on the axis. Defaults to 0.'),
			);
		$form['xaxis']['labels']['y'] = array(
	    	'#type' => 'textfield',
	    	'#title' => t('y'),
	    	'#default_value' => isset($this->options['xaxis']['labels']['y']) ? $this->options['xaxis']['labels']['y']: 0,
	    	'#description' => t('The y position offset of the label relative to the tick position on the axis. Defaults to 0.'),
			);
		$form['xaxis']['labels']['style'] = array(
	    	'#type' => 'textarea',
	    	'#title' => t('Style'),
	    	'#default_value' => isset($this->options['xaxis']['labels']['style']) ? $this->options['xaxis']['labels']['style']: null,
	    	'#description' => t('CSS styles for the label.'),
			);
		$form['xaxis']['title']= array(
    		'#type' => 'fieldset',
    		'#title' => t('Title'),
    		'#tree' => true,
  		);
		$form['xaxis']['title']['text'] = array(
	    	'#type' => 'textfield',
	    	'#title' => t('Text'),
	    	'#default_value' => isset($this->options['xaxis']['title']['text']) ? $this->options['xaxis']['title']['text'] : null,
	    	'#description' => t('The actual text of the axis title. It can contain basic HTML text markup like &lt;b&gt;, &lt;i&gt; and spans with style. Defaults to null.'),
			);
		$form['xaxis']['title']['align'] = array(
	    	'#type' => 'select',
	    	'#title' => t('Align'),
	    	'#default_value' => isset($this->options['xaxis']['title']['align']) ? isset($this->options['xaxis']['title']['align']) : 'middle',
	    	'#description' => t('Alignment of the title relative to the axis values.'),
	        '#size' => 1,
		    '#options' => array('low' => 'Low', 'middle' => 'Middle', 'high' => 'High'),
	        '#multiple' => FALSE,
			);
		$form['xaxis']['title']['rotation'] = array(
	    	'#type' => 'textfield',
	    	'#title' => t('Rotation'),
	    	'#default_value' => isset($this->options['xaxis']['title']['rotation']) ? $this->options['xaxis']['title']['rotation']: null,
	    	'#description' => t('The rotation of the text in degrees. 0 is horizontal, 270 is vertical reading from bottom to top. Defaults to 0.'),
			);
		$form['xaxis']['title']['margin'] = array(
	    	'#type' => 'textfield',
	    	'#title' => t('Margin'),
	    	'#default_value' => isset($this->options['xaxis']['title']['margin']) ? $this->options['xaxis']['title']['margin']: null,
	    	'#description' => t('The pixel distance between the axis line and the title. Positive values are outside the axis line, negative are inside. Defaults to 35'),
			);
		$form['xaxis']['title']['style'] = array(
	    	'#type' => 'textarea',
	    	'#title' => t('Style'),
	    	'#default_value' => isset($this->options['xaxis']['title']['style']) ? $this->options['xaxis']['title']['style']: null,
	    	'#description' => t('CSS styles for the title. When titles are rotated they are rendered using vector graphic techniques and not all styles are applicable. Most noteworthy, a bug in IE8 renders all rotated strings bold and italic. Defaults to:<br/>color: \'#6D869F\',<br/>fontWeight: \'bold\''),
			);

		$form['yaxis']= array(
    		'#type' => 'fieldset',
    		'#title' => t('Y Axis'),
    		'#tree' => true,
  		);
  		$form['yaxis']['max'] = array(
	    	'#type' => 'textfield',
	    	'#title' => ('Max'),
	    	'#default_value' => isset($this->options['yaxis']['max']) ? $this->options['yaxis']['max']: null,
	    	'#description' => ('The maximum value of the axis. If null, the max value is automatically calculated. If the endOnTick option is true, the max value might be rounded up. The actual maximum value is also influenced by chart.alignTicks. Defaults to null.'),
			);
		$form['yaxis']['gridLineWidth'] = array(
	    	'#type' => 'textfield',
	    	'#title' => ('gridLineWidth'),
	    	'#default_value' => isset($this->options['yaxis']['gridLineWidth']) ? $this->options['yaxis']['gridLineWidth']: 1,
	    	'#description' => ('The width of the grid lines extending the ticks across the plot area. Defaults to 1.'),
			);
		$form['yaxis']['labels']= array(
    		'#type' => 'fieldset',
    		'#title' => t('Labels'),
    		'#tree' => true,
  		);
		$form['yaxis']['labels']['align']	= array(
	    	'#type' => 'select',
	    	'#title' => ('Type'),
	    	'#default_value' => isset($this->options['yaxis']['labels']['align']) ? $this->options['yaxis']['labels']['align']: 'right',
	    	'#description' => ('Alignment of the title relative to the axis values.'),
	        '#size' => 1,
		    '#options' => array('left' => 'Left', 'center' => 'Center', 'right' => 'Right'),
	        '#multiple' => FALSE,
			);
		$form['yaxis']['labels']['enabled'] =	array(
	    	'#type' => 'select',
	    	'#title' => ('Enabled'),
	    	'#default_value' => isset($this->options['yaxis']['labels']['enabled']) ?$this->options['yaxis']['labels']['enabled'] : 1,
	    	'#description' => ('Enable or disable the labels.'),
	        '#size' => 1,
		    '#options' => array(1 => 'Yes', 0 => 'No'),
	        '#multiple' => FALSE,
			);
		$form['yaxis']['labels']['formatter'] = array(
  			'#type' => 'textarea',
	    	'#title' => ('Formatter'),
	    	'#default_value' => isset($this->options['yaxis']['labels']['formatter']) ? $this->options['yaxis']['labels']['formatter'] : null,
	    	'#description' => ('Callback function to format the HTML of the tooltip. Return false to disable tooltip for a specific point on series.'),
  		);
		$form['yaxis']['labels']['rotation'] = array(
	    	'#type' => 'textfield',
	    	'#title' => ('Rotation'),
	    	'#default_value' => isset($this->options['yaxis']['labels']['rotation']) ? $this->options['yaxis']['labels']['rotation']: 0,
	    	'#description' => ('The rotation of the text in degrees. 0 is horizontal, 270 is vertical reading from bottom to top. Defaults to 0.'),
			);
		$form['yaxis']['labels']['style'] = array(
	    	'#type' => 'textarea',
	    	'#title' => t('Style'),
	    	'#default_value' => isset($this->options['yaxis']['labels']['style']) ? $this->options['yaxis']['labels']['style']: null,
	    	'#description' => t('CSS styles for the label.'),
			);
		$form['yaxis']['scale'] = array(
	    	'#type' => 'select',
	    	'#title' => ('Scale Y Axis'),
	    	'#default_value' => $this->options['yaxis']['scale'],
	    	'#description' => ('Scale Y Axis amount.'),
	        '#size' => 1,
		    '#options' => $fieldNames,
	        '#multiple' => FALSE,
			);

		$form['yaxis']['title']= array(
    		'#type' => 'fieldset',
    		'#title' => t('Title'),
    		'#tree' => true,
  		);
		$form['yaxis']['title']['text'] = array(
	    	'#type' => 'textfield',
	    	'#title' => ('Text'),
	    	'#default_value' => isset($this->options['yaxis']['title']['text']) ? $this->options['yaxis']['title']['text']: null,
	    	'#description' => ('The actual text of the axis title. It can contain basic HTML text markup like &lt;b&gt;, &lt;i&gt; and spans with style. Defaults to null.'),
			);
		$form['yaxis']['title']['align']	= array(
	    	'#type' => 'select',
	    	'#title' => ('Type'),
	    	'#default_value' => isset($this->options['yaxis']['title']['align']) ? $this->options['yaxis']['title']['align']: 'low',
	    	'#description' => ('Alignment of the title relative to the axis values.'),
	        '#size' => 1,
		    '#options' => array('low' => 'Low', 'middle' => 'Middle', 'high' => 'High'),
	        '#multiple' => FALSE,
			);
		$form['yaxis']['title']['rotation'] = array(
	    	'#type' => 'textfield',
	    	'#title' => ('Rotation'),
	    	'#default_value' => isset($this->options['yaxis']['title']['rotation']) ? $this->options['yaxis']['title']['rotation']: 0,
	    	'#description' => ('The rotation of the text in degrees. 0 is horizontal, 270 is vertical reading from bottom to top. Defaults to 0.'),
			);
		$form['yaxis']['title']['style'] = array(
	    	'#type' => 'textarea',
	    	'#title' => t('Style'),
	    	'#default_value' => isset($this->options['yaxis']['title']['style']) ? $this->options['yaxis']['title']['style']: null,
	    	'#description' => t('CSS styles for the title. When titles are rotated they are rendered using vector graphic techniques and not all styles are applicable. Most noteworthy, a bug in IE8 renders all rotated strings bold and italic. Defaults to:<br/>color: \'#6D869F\',<br/>fontWeight: \'bold\''),
			);
		$form['yaxis']['title']['margin'] = array(
	    	'#type' => 'textfield',
	    	'#title' => t('Margin'),
	    	'#default_value' => isset($this->options['yaxis']['title']['margin']) ? $this->options['yaxis']['title']['margin']: null,
	    	'#description' => t('The pixel distance between the axis line and the title. Positive values are outside the axis line, negative are inside. Defaults to 40.'),
			);

		$form['exporting']= array(
    		'#type' => 'fieldset',
    		'#title' => t('Exporting'),
    		'#tree' => true,
  		);
  		$form['exporting']['enabled'] =	array(
	    	'#type' => 'select',
	    	'#title' => ('Enabled'),
	    	'#default_value' => isset($this->options['exporting']['enabled']) ? $this->options['exporting']['enabled'] : 0,
	    	'#description' => ('Enable or disable the export.'),
	        '#size' => 1,
		    '#options' => array(1 => 'Yes', 0 => 'No'),
	        '#multiple' => FALSE,
			);
		$form['exporting']['filename'] = array(
	    	'#type' => 'textfield',
	    	'#title' => ('Filename'),
	    	'#default_value' => isset($this->options['exporting']['filename']) ? $this->options['exporting']['filename']: 'chart',
	    	'#description' => ('The filename, without extension, to use for the exported chart. Defaults to "chart".'),
			);

	}



	/**
	 * Implementation of view_style_plugin::theme_functions(). Returns an array of theme functions to use
	 * for the current style plugin
	 * @return array
	 */
	function theme_functions() {
		return views_theme_functions('views_views_highcharts_style', $this->view, $this->display);
	}

	/**
	 * Implementation of views_style_plugin::additional_theme_functions(). Returns empty array.
	 * @return array
	 */
	function additional_theme_functions() {
		return array();
	}

	/**
	 * Implementation of view_style_plugin::render()
	 */
	function render() {
		return parent::render();

	}

}
