<?php

/**
 * @file
 *
 * Defines the views query plugin that talks to the Data Controller
 */

class view_join_views_plugin_query extends views_plugin_query {
	var $requests;
	var $aliases;
	var $joins;
	var $orderby;
	var $required_fields;
	var $dataset = '';
	var $parameters;
	var $views;
	var $joinType;
	var $joinColumns;

	/**
	 * Constructor; Create the basic query object and fill with default values.
	 */
	function init($base_table, $base_field) {
		$this->requests = [];
		$this->aliases = [];
		$this->joins = [];
		$this->orderby = [];
		$this->parameters = [];
		$this->ensure_table($base_table);
		$this->views = [];
		$this->joinColumns = [];

		// Required fields are fields that may not be empty in rows of the
		// resultset. Any rows with empty required fields are dropped *prior* to
		// paging.
		$this->required_fields = [];
	}

	function ensure_table($table) {
		//return empty string to indicate no alias for this table
		// (we only ever work with one "virtual" table at this level anyway)
		$this->views[] = $table;
		return $table;
	}

	//despite the comment below,w e still use add_filter for these
	//things that are not really filters
  function add_filter($table, $field, $value) {
	if ($field == 'view') {
		$this->ensure_table($value);
	}
	elseif ($field == 'join_type') {
		$this->joinType = $value;
	}
	elseif ($field == 'join_column') {
		$this->joinColumns[] = $value;
	}
  }

	//note we use add_parameter instead of add_where/add_filter
	//because of this we override argument and filter hanlders
	//to call our function with the expected parameters
	function add_parameter($field, $value) {
		while(isset($this->parameters[$field])) {
			$field = $field + '_';
		}
		$this->parameters[$field] = $value;
	}

	//this is called when the user adds a field to the view
	function add_field($table, $field, $required = FALSE) {
		$this->ensure_table($table);
		$this->aliases[$table][$field] = $field;

		// consider removing this, we do not honor this request
		//      when rendering the results (doing so would break
		//      server side paging as well)
		if ($required) {
			$this->required_fields[$field] = $field;
		}
		return $field;
	}


  function add_orderby($table, $field, $order) {
    $this->orderby = array(
      'field_alias' => $field,
      'order' => $order,
    );
    log_debug($this->orderby);
  }

	/**
	 * Executes the query and fills the associated view object with according
	 * values.
	 *
	 * Values to set: $view->result, $view->total_rows, $view->execute_time,
	 * $view->pager['current_page'].
	 */
	function execute(&$view) {

		$start = views_microtime();

		if (method_exists($this, 'init_pager')) {
			$this->init_pager($view);
		}
		// Views 3.x dev
		elseif (method_exists($view, 'init_pager')) {
			$view->init_pager();
		}

		if ($this->pager->use_pager()) {
			$this->pager->set_current_page($view->current_page);
		}

		$this->pager->query();

		$query = '';
		$query .= 'Join Type: ' . $this->joinType . "\n";
		$query .= 'Join Columns: ' . var_export($this->joinColumns, TRUE) . "\n";

		//only try this if we have some views
		if (count($this->views) > 0) {

			$query .= 'Views: ' . var_export($this->views, TRUE) . "\n";

			$data = view_join_views_get_view_data($view, $this->views[0], $this->parameters);

			$data = $this->convertStructure($data);
			log_debug("after view converted data");
			log_debug($data);

			//only if more than 1 view is selected
			if (count($this->views) > 1) {
				$joinController = join_controller_get_instance($this->joinType);

				$sourceConfiguration = new JoinController_SourceConfiguration($data, $this->joinColumns);
				for($iter=1; $iter < count($this->views); $iter++) {
                    //load next data
                    $nextdata = view_join_views_get_view_data($view, $this->views[$iter], $this->parameters);
                    $nextdata = $this->convertStructure($nextdata);
                    $sourceConfiguration = $joinController->join(
                        $sourceConfiguration,
                        new JoinController_SourceConfiguration($nextdata, $this->joinColumns));
                }
                $data = $sourceConfiguration->tableData;
			}

			//now convert into format expected by views module
			$view->result = [];
			$fields = $view->get_items('field');
			if (isset($data)) {
				foreach ($data as $row) {
					unset($newRow);

					foreach ($fields as $name => $field) {
						$newRow->$field['label'] = $row[$field['view_col']];
					}

					$view->result[] = $newRow;
				}
			}

		}

		//apply filters
		$query .= 'Filters: ' . var_export($this->parameters, TRUE) . "\n";
		$view->result = array_filter($view->result, array($this, 'applyFilters'));

		// Sort.
	    if (!empty($this->orderby)) {
	    	$query .= 'Order By: ' . $this->orderby['field_alias'] . ' - ' . strtolower($this->orderby['order']) . "\n";
	      	uasort($view->result, array($this, 'uasort_'. strtolower($this->orderby['order'])));
	    }

		$this->pager->total_items = count($view->result);
		$this->pager->update_page_info();

		// Slice the resultset.
	    if (isset($this->offset, $this->limit)) {
	    	$query .= 'Offset: ' . $this->offset . "\n";
	    	$query .= 'Limit: ' . $this->limit . "\n";
	      	$end = $this->offset + $this->limit;
	      	$end = count($view->result) >= $end ? $end : count($view->result);
	      	$view->result = array_slice($view->result, $this->offset, $end - $this->offset);
	    }

	    log_debug($view->result);

		$view->execute_time = views_microtime() - $start;

		$view->build_info['query'] = $query;

	}

	protected function applyFilters($var) {
		foreach ($this->parameters as $key => $value) {
			if (strstr($key, '_none') !== FALSE)
				continue;
			if ($var->$key != $value)
				return false;
		}
		return true;
	}

	function convertStructure($inObj) {
		$result = NULL;
		foreach ($inObj['result'] as $record) {
			$recordArray = [];
			foreach ($record['row'] as $key => $value) {
				$recordArray[$key] = $value;
			}
			$result[] = $recordArray;
		}
		return $result;
	}

	//sorting functions borrowed from wbapi
  protected function uasort_asc($a, $b) {
    // Always move NULL values to the end.
    if (!isset($a->{$this->orderby['field_alias']})) {
      return TRUE;
    }
    if (!isset($b->{$this->orderby['field_alias']})) {
      return FALSE;
    }
    // Numeric comparison.
    if (is_numeric($a->{$this->orderby['field_alias']}) && is_numeric($b->{$this->orderby['field_alias']})) {
      return $a->{$this->orderby['field_alias']} > $b->{$this->orderby['field_alias']};
    }
    // Locale-aware string comparison.
    else {
      return strcoll($a->{$this->orderby['field_alias']}, $b->{$this->orderby['field_alias']});
    }
  }

  protected function uasort_desc($a, $b) {
    // Always move NULL values to the end.
    if (!isset($a->{$this->orderby['field_alias']})) {
      return TRUE;
    }
    if (!isset($b->{$this->orderby['field_alias']})) {
      return FALSE;
    }
    // Numeric comparison.
    if (is_numeric($a->{$this->orderby['field_alias']}) && is_numeric($b->{$this->orderby['field_alias']})) {
      return $a->{$this->orderby['field_alias']} < $b->{$this->orderby['field_alias']};
    }
    // Locale-aware string comparison.
    else {
      return strcoll($b->{$this->orderby['field_alias']}, $a->{$this->orderby['field_alias']});
    }
  }

}
