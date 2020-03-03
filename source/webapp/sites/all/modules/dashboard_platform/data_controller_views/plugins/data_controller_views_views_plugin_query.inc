<?php



require_once('CSVStreamerResultFormatter.php');

/**
 * @file
 *
 * Defines the views query plugin that talks to the Data Controller
 */

class data_controller_views_views_plugin_query extends views_plugin_query {
    var $requests;
    var $aliases;
    var $joins;
    var $orderby;
    var $required_fields;
    var $dataset;
    var $parameters;
    var $useCube;
    var $rowFlattenerConfig;
    var $percentCalculatorConfig;
    var $weightedGraderConfig;
    var $csvStreamer;

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
        $this->useCube = FALSE;
        $this->rowFlattenerConfig = NULL;
        $this->percentCalculatorConfig = NULL;
        $this->weightedGraderConfig = NULL;
        $this->csvStreamer = NULL;

        // Required fields are fields that may not be empty in rows of the
        // resultset. Any rows with empty required fields are dropped *prior* to
        // paging.
        $this->required_fields = [];
    }

    function ensure_table($table) {
        //return empty string to indicate no alias for this table
        // (we only ever work with one "virtual" table at this level anyway)
        return '';
    }

    //despite the comment below,w e still use add_filter for these
    //things that are not really filters
    function add_filter($table, $field, $value) {
        if ($field == 'dataset') {
            $this->dataset = $value;
        }
//        elseif ($field == 'cube_or_list') {
//            if ($value == 'Cube')
//                $this->useCube = TRUE;
//        }
        elseif ($field == 'row_flattener') {
            $this->rowFlattenerConfig = $value;
        }
        elseif ($field == 'percents_calculator') {
            $this->percentCalculatorConfig = $value;
        }
        elseif ($field == 'weighted_grader') {
            $this->weightedGraderConfig = $value;
        }
        elseif ($field == 'csv_streamer') {
            $this->csvStreamer = $value;
        }
    }

    //note we use add_parameter instead of add_where/add_filter
    //because of this we override argument and filter hanlders
    //to call our function with the expected parameters
    function add_parameter($field, $value, $operator = 'Equal') {

        //FIXME add support for configurable delimiter
        $comma_pos = strpos($value, ',');
        if ($comma_pos == FALSE) {
            if ($value == '_') {
                $value = '';
            }
            $this->parameters[$field] = $value;
        }
        else {
            $this->parameters[$field] = explode(',', $value);
            foreach ($this->parameters[$field] as &$val) {
                if ($val == '_') {
                    $val = '';
                }
            }
        }

        $inner_value = $this->parameters[$field];
        $operatorConfig = data_controller_get_operator_factory_instance();
        $this->parameters[$field] = $operatorConfig->initiateHandler($operator, array($inner_value));
    }

    //this is called when the user adds a field to the view
    function add_field($table, $field, $required = FALSE) {
        $this->ensure_table($table);
        $this->aliases[$table][$field] = $field;

        //FIXME consider removing this, we do not honor this request
        //      when rendering the results (doing so would break
        //      server side paging as well)
        if ($required) {
            $this->required_fields[$field] = $field;
        }
        return str_replace('.', '_', $field);
    }


    function add_orderby($table, $field, $order) {
        if (strcasecmp($order, 'ASC') == 0) {
            $this->orderby[] = $field;
        }
        else {
            $this->orderby[] = "-" . $field;
        }
    }

    /**
     * Executes the query and fills the associated view object with according
     * values.
     *
     * Values to set: $view->result, $view->total_rows, $view->execute_time,
     * $view->pager['current_page'].
     */
    function execute(&$view) {

        $start = microtime(TRUE);

        if (method_exists($this, 'init_pager')) {
            $this->init_pager($view);
        }
        // Views 3.x dev
        elseif (method_exists($view, 'init_pager')) {
            $view->init_pager();
        }


        //some pager preparation
        if ($this->pager->use_pager()) {
            $this->pager->set_current_page($view->current_page);
        }

        $this->pager->query();

        //FIXME preparing the column list
        //need to refactor and just store in the format we want in the first place
        $columns = [];
        if (isset($this->aliases[''])) {
            foreach ($this->aliases[''] as $field => $alias) {
                // log_debug("requested field: " . $field . " with alias " . $alias);
                $columns[] = $field;
            }
        }


        //data controller specific code
        $dataQueryController = data_controller_get_instance();
        $query = "";

        $resultFormatter = NULL;
        $resultFormatterString = '';
        //FIXME this checking style doesnt easily support large numbers
        // adding check to include 3rd option is messy
        if (isset($this->rowFlattenerConfig) && isset($this->percentCalculatorConfig)) {
            throw new Exception("Use of multipe result formatters is not supported at this time.");
        }
        elseif (isset($this->rowFlattenerConfig)) {
            if ($this->rowFlattenerConfig->aggregate_column) {
                $resultFormatter = new AggregatedRowFlattenerResultFormatter($this->rowFlattenerConfig->group_by_columns, $this->rowFlattenerConfig->enumeration_column, $this->rowFlattenerConfig->subject_columns);
                $resultFormatterString .= 'Result Formatter: Aggregated Row Flattener' . "\n";
                $resultFormatterString .= 'Row Flattener - Group By Columns:' . var_export($this->rowFlattenerConfig->group_by_columns, TRUE) . "\n";
                $resultFormatterString .= 'Row Flattener - Enumeration Column:' . var_export($this->rowFlattenerConfig->enumeration_column, TRUE) . "\n";
                $resultFormatterString .= 'Row Flattener - Subject Columns:' . var_export($this->rowFlattenerConfig->subject_columns, TRUE) . "\n";
            }
            else {
                $resultFormatter = new RowFlattenerResultFormatter($this->rowFlattenerConfig->group_by_columns, $this->rowFlattenerConfig->enumeration_column, $this->rowFlattenerConfig->subject_columns);
                $resultFormatterString .= 'Result Formatter: Row Flattener' . "\n";
                $resultFormatterString .= 'Row Flattener - Group By Columns:' . var_export($this->rowFlattenerConfig->group_by_columns, TRUE) . "\n";
                $resultFormatterString .= 'Row Flattener - Enumeration Column:' . var_export($this->rowFlattenerConfig->enumeration_column, TRUE) . "\n";
                $resultFormatterString .= 'Row Flattener - Subject Columns:' . var_export($this->rowFlattenerConfig->subject_columns, TRUE) . "\n";
            }
        }
        elseif (isset($this->percentCalculatorConfig)) {
            $resultFormatter = new PercentCalculatorArrayResultFormatter($this->percentCalculatorConfig->amount_columns, $this->percentCalculatorConfig->prefix, $this->percentCalculatorConfig->suffix);
            $resultFormatterString .= 'Result Formatter: Percent Caclulator' . "\n";
            $resultFormatterString .= 'Percent Caclulator - Amount Columns:' . var_export($this->percentCalculatorConfig->amount_columns, TRUE) . "\n";
            $resultFormatterString .= 'Percent Caclulator - Prefix:' . $this->percentCalculatorConfig->prefix . "\n";
            $resultFormatterString .= 'Percent Caclulator - Suffix:' . $this->percentCalculatorConfig->suffix . "\n";
        }
        elseif (isset($this->weightedGraderConfig)) {
            $resultFormatter = new WeightedGraderArrayResultFormatter($this->weightedGraderConfig->value_column, $this->weightedGraderConfig->grade_column);
            $resultFormatterString .= 'Result Formatter: Weighted Grader' . "\n";
            $resultFormatterString .= 'Weighted Grader - Value Column:' . $this->weightedGraderConfig->value_column . "\n";
            $resultFormatterString .= 'Weighted Grader - Grade Column:' . $this->weightedGraderConfig->grade_column . "\n";
        }
        elseif (isset($this->csvStreamer)) {
            $resultFormatter = new CSVStreamerResultFormatter($view, $this->csvStreamer->header);
            $resultFormatterString .= 'Result Formatter: CSV Streamer' . "\n";
            drupal_set_header('Content-type: text/csv; charset=utf-8');
            if ($this->csvStreamer->filename) {
                  drupal_set_header('Content-Disposition: attachment; filename="'. $this->csvStreamer->filename .'"');
            }
        }
        else {
            $resultFormatterString .= 'Result Formatter: None' . "\n";
        }

        $limit = $this->limit;
        if ($limit == 0) {
            $limit = NULL;
        }

        if (!$this->useCube) {
            $query .= "Type: List\n";
            $ourResult = $dataQueryController->queryDataset($this->dataset, $columns, $this->parameters, $this->orderby, $this->offset, $limit, $resultFormatter);
            if (isset($this->offset) && isset($limit)) {
                $view->total_rows = $dataQueryController->countDatasetRecords($this->dataset, $this->parameters);
            }
        }
        else {
            $query .= "Type: Cube\n";
            $ourResult = $dataQueryController->queryCube($this->dataset, $columns, $this->parameters, $this->orderby, $this->offset, $limit, $resultFormatter);
            if (isset($this->offset) && isset($limit)) {
                $view->total_rows = $dataQueryController->countCubeRecords($this->dataset, $columns, $this->parameters);
            }
        }
        if ($this->csvStreamer) {
            exit;
        }
        $query .= 'Dataset: ' . $this->dataset . "\n";
        $query .= 'Columns: ' . var_export($columns, TRUE) . "\n";
        $query .= 'Parameters: ' . var_export($this->parameters, TRUE) . "\n";
        $query .= 'Order By: ' . var_export($this->orderby, TRUE) . "\n";
        $query .= 'Offset: ' . $this->offset . "\n";
        $query .= 'Limit(original) : ' . (isset($this->limit) ? $this->limit : 'NULL') . "\n";
        $query .= 'Limit(used) : ' . (isset($limit) ? $limit : 'NULL') . "\n";
        $query .= $resultFormatterString;

        //convert result into format they expect
        $view->result = [];
        if (isset($ourResult)) {
            foreach ($ourResult as $rowKey => $row) {
                unset($newRow);
                if (!is_array($row)) {
                    continue;
                }
                foreach ($row as $key => $val) {
                    $newRow->$key = $val;
                }
                $view->result[$rowKey] = $newRow;
            }
        }

        log_debug($view->result);

        //this was necessary to get paging links to show up
        $this->pager->total_items = $view->total_rows;
        $this->pager->update_page_info();
        $view->execute_time = microtime(TRUE) - $start;

        $view->build_info['query'] = $query;
        //var_dump($view);

    }

    function validate() {
      //FIXME this is new method
      // needs a real implementation
      return TRUE;
    }

}
