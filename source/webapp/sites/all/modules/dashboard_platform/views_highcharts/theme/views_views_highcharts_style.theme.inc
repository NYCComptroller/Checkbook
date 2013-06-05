<?php
//this is a theme pre-process function where we can prepare varialbes for the template
function template_preprocess_views_views_highcharts_style(&$vars) {
	
	//kpr($vars);
	$options = $vars['options'];
	
	
	$view = $vars['view'];
	
	$fields   = &$view->field; 
	
    if (isset($fields[$options['xaxis']['xaxis_column']]->field_alias) && ($fields[$options['xaxis']['xaxis_column']]->field_alias != 'unknown')) {
    	$options['xaxis']['xaxis_column'] = $fields[$options['xaxis']['xaxis_column']]->field_alias;
    }
    $seriesCount = count($options['series']);
    for($i = 0 ; $i < $seriesCount; $i++) {
    	if (isset($fields[$options['series'][$i]['yaxis_column']]->field_alias) && ($fields[$options['series'][$i]['yaxis_column']]->field_alias != 'unknown')) {
    		$options['series'][$i]['yaxis_column'] = $fields[$options['series'][$i]['yaxis_column']]->field_alias;
    	}
    }
    
	
	$categories = array();	
	if (!isset($options['legend']['width'])) {
		unset($options['legend']['width']);
	}

	//begin block to render fields
	$rows_in = $vars['rows'];
	
	
	$rows = array();
	foreach ($rows_in as $row) {
		$rows[] = views_highcharts_views_render_fields($view, $row);
	}
	
	//try and only loop through the result once and get everything in one pass
	$nb = 0;
	
	if ($options['yaxis']['scale'] != null) {
		$scaleColName = str_replace('.', '_', $options['yaxis']['scale']);
		$lastRow = end($rows);
		$maxAmount = $lastRow->$scaleColName;
		//log_debug('Max Amount' , $maxAmount);		
		//$unit = custom_number_formater_unit($maxAmount);
		$scale = $unit[1];
		$vars['yaxis_title'] = $unit[0];		
		$vars['yaxis_label_formatter'] = "return (this.value / " . $scale . ");";		
	} else {
		$vars['yaxis_title'] = $options['yaxis']['title']['text'];
		$vars['yaxis_label_formatter'] = $options['yaxis']['labels']['formatter'];
	}	
	foreach ($rows as $row) {
		//log_debug('Row Data', $row);
	    $xaxis_column = str_replace('.', '_', $options['xaxis']['xaxis_column']);
		if (isset($row->$xaxis_column)) {		
			if ($options['xaxis']['type'] == 'linear' && (!isset($options['series']['chart_type']) || $options['series']['chart_type'] != 'pie')) {			
				$categories[] = $row->$xaxis_column;
			}
			
			for($i = 0 ; $i < $seriesCount; $i++) {
				if ($options['series'][$i]['yaxis_column'] != NULL) {
					$pointData = array();
					$yaxis_column = str_replace('.', '_', $options['series'][$i]['yaxis_column']);
					if (isset($options['series'][$i]['yaxis_point_data_column'])) {
    					$pointColumns = $options['series'][$i]['yaxis_point_data_column'];
    					foreach ($pointColumns as $pCol) {
    						$pCol = str_replace('.', '_', $pCol);
    						if (isset($fields[$pCol]->field_alias) && ($fields[$pCol]->field_alias != 'unknown')) {
    							$aliasName = $fields[$pCol]->field_alias;
    						} else {
    							$aliasName = $pCol;
    						} 
    						$pointData[preg_replace('/[^\w]/', '_', $fields[$pCol]->options['label'])] = $row->$aliasName;
    					}
    				}    	
					if ($options['xaxis']['type'] == 'linear') {
						$data[$i][] = array_merge(array('y' => $row->$yaxis_column + 0, 'name' => $row->$xaxis_column), $pointData);
						//if ($row->$yaxis_column == null) {
							//$data[$i][] = null;
						//} else {
							//$data[$i][] = array('y' => $row->$yaxis_column + 0, 'name' => $row->$xaxis_column,'color' => getColor($nb));
						//}
					} else {
						$data[$i][] = array_merge(array('x' => $row->$xaxis_column + 0, 'y' => $row->$yaxis_column + 0), $pointData);
					}					
				}
			}
		}
		$nb++;					
	}
	
	$vars['data'] = $data;
	$vars['seriesCount'] = $seriesCount;
	$vars['categories']	= $categories;	
	$vars['chartDiv'] = 'view-highchart-'. $view->name . "-". $view->current_display;
	
}

function views_highcharts_views_render_fields($view, $row) {
	$field_ids = array_keys($view->field);

	foreach ($field_ids as $id) {
		$field = $view->field[$id];

		$field_output = $view->field[$field->options['id']]->advanced_render($row);

		if (empty($field->options['exclude']) && ($field_output != "") && !empty($field_output)) {
			$fname = $field->field_alias;
			$rendered_fields->$fname = $field_output;
		}
	}
	
	return $rendered_fields;
}
