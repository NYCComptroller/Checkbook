<?php

/**
 * Function to render export UI page
 */
function _checkbook_export_form(){

    $iRecordsTotal = (int)check_plain($_GET['iRecordsTotal']);
    $iRecordsDisplay = (int)check_plain($_GET['iRecordsDisplay']);
    $maxPages = (int)check_plain($_GET['maxPages']);

    echo theme('checkbook_export_default_theme', array('maxPages' => $maxPages,'totalRecords' => $iRecordsTotal,'displayRecords' => $iRecordsDisplay));
}

/** Function to render export data for grid view */
function _checkbook_export_grid_transactions(){

    $nodeId = RequestUtilities::get('node',false);
    $node = node_load($nodeId);
    if($node ==null ){
    	$node = widget_load($nodeId);
    }
    $_GET['q'] = urldecode(drupal_get_path_alias($_GET['refURL']));
    widget_config($node);

    if(isset($node->widgetConfig->gridConfig->adjustWidgetConfig)){
        eval($node->widgetConfig->gridConfig->adjustWidgetConfig);
    }

    widget_prepare($node);
    widget_invoke($node, 'widget_prepare');

    $sortColumn = $node->widgetConfig->gridConfig->table_columns[$_REQUEST['iSortCol_0']]->column;

    if(isset($node->widgetConfig->gridConfig->table_columns[$_REQUEST['iSortCol_0' ]]->sortColumn)){
        $sortColumn = $node->widgetConfig->gridConfig->table_columns[$_REQUEST['iSortCol_0']]->sortColumn;
    }
    $isAsc = ($_REQUEST['sSortDir_0'] == 'asc');

    widget_data($node);
    if(isset($node->widgetConfig->widgetUpdateJSONConfig)){
    	eval($node->widgetConfig->widgetUpdateJSONConfig);
    }

    //remove blank columns
    $cols = $node->widgetConfig->gridConfig->table_columns;
    for($i=0; $i<count($cols); $i++){
        if($cols[$i]->column){
            $columns[$i] = $cols[$i];
        }
    }

    $data = NULL;

    $i=0;
    foreach($columns as $column){

        $label = (isset($column->labelAlias))? (WidgetUtil::getLabel($column->labelAlias)) : $column->colLabel;
        if($column->eval) {
            $label = eval("return $label;");
        }

        if($i == 0)
            $data .= $label;
        else{
            $data .= ',"'.$label.'"';
        }
        $i++;
    }
    if(isset($node->widgetConfig->gridConfig->data)){
    	foreach($node->widgetConfig->gridConfig->table_columns as $column){
    		echo $column->labelAlias . ",";
    	}
    	echo "\r\n";

        $sortindex = $_REQUEST['iSortCol_0'];

    	$sortindexFormat = $node->widgetConfig->gridConfig->table_columns[$sortindex]->formatType;
    	foreach($node->widgetConfig->gridConfig->data as &$dataRecord){
    		$dataRecord['sort_column'] = $sortindex;
    		if(isset($sortindexFormat)){
    			$dataRecord['formatType'] = $sortindexFormat;
    		}
    	}
        if($isAsc){
            $order = 'asc';
        }
        else{
            $order = 'desc';
        }
    	_checkbook_export_grid_sort($node, $order);
    	foreach ($node->widgetConfig->gridConfig->data as $datarow) {
    		$index = 0;
    		while($index <count($node->widgetConfig->gridConfig->table_columns)){
    			$columnValue = html_entity_decode($datarow[$index]);
    			$columnValue = str_replace('"',chr(34).'"',$columnValue);
				if($node->widgetConfig->gridConfig->table_columns[$index]->formatType =="amount"){
					$columnValue = ($columnValue == null) ? 0:$columnValue;
				}
    			echo (($index == 0) ? ('"'.$columnValue.'"') : (',"'.$columnValue.'"'));
    			$index +=1;
    		}
    		echo "\r\n";
    	}

    }
    else if(is_array($node->data) and count($node->data) > 0){
        $dataRecords = $node->data;

        //Custom column for ordering
        $i=1;
        foreach($dataRecords as &$dataRecord){
            $dataRecord['sort_order'] = $i;
            $i++;
        }

        sort_records($dataRecords, new PropertyBasedComparator_DefaultSortingConfiguration($sortColumn,$isAsc));

        foreach($dataRecords as $dataRec){
            $data .= PHP_EOL;
            $i = 0;
            $rowData = NULL;
            foreach($columns as $column){

                $columnValue = html_entity_decode($dataRec[$column->column], ENT_QUOTES);;
                $columnValue = str_replace('"',chr(34).'"',$columnValue);
                $rowData .= (($i == 0) ? ('"'.$columnValue.'"') : (',"'.$columnValue.'"'));
                $i++;
            }

            $rowData = str_replace(array("\r\n", "\n", "\r"),'',$rowData);
            $data .= $rowData;
        }
    }

    drupal_add_http_header("Content-Type", "text/csv");
    drupal_add_http_header("Content-Length",strlen($data));
    drupal_add_http_header("Content-Disposition", "attachment; filename=TransactionsData.csv");
    drupal_add_http_header("Pragma", "cache");
    drupal_add_http_header("Expires", "-1");
    echo $data;
}


function _checkbook_export_grid_sort($node, $order = null){
	$node->widgetConfig->gridConfig->data;
    if(isset($order)){
       if($order == 'asc'){
           usort($node->widgetConfig->gridConfig->data,
               function($a, $b)
               {
                   $index = $a['sort_column'];

                   if(($a['formatType'] == "monthfy" || $a['formatType'] == "month") && RequestUtilities::get('yeartype') == 'B'){
                       $months =  array("July"=> 1, "August"=>  2, "September"=>  3, "October"=>  4, "November"=>  5, "December"=>  6,
                           "January"=>  7, "February"=>  8, "March"=>  9, "April"=>  10, "May"=> 11, "June"=> 12);

                       if ($months[$a[$index]] == $months[$b[$index]]) {
                           return 0;
                       }
                       return ($months[$a[$index]] > $months[$b[$index]]) ? -1 : 1;
                   }
                   else if($a['formatType'] == "month" && RequestUtilities::get('yeartype') == 'C') {
                       $months =  array("January"=> 1, "February"=>  2, "March"=>  3, "April"=>  4, "May"=>  5, "June"=>  6,
                           "July"=>  7, "August"=>  8, "September"=>  9, "October"=>  10, "November"=> 11, "December"=> 12);

                       if ($months[$a[$index]] == $months[$b[$index]]) {
                           return 0;
                       }
                       return ($months[$a[$index]] > $months[$b[$index]]) ? -1 : 1;

                   }
                   else{
                       if ($a[$index] == $b[$index]) {
                           return 0;
                       }
                       return ($a[$index] < $b[$index]) ? -1 : 1;
                   }
               }
           );
       } else {
            usort($node->widgetConfig->gridConfig->data,
                function($a, $b)
                {
                    $index = $a['sort_column'];

                    if(($a['formatType'] == "monthfy" || $a['formatType'] == "month") && RequestUtilities::get('yeartype') == 'B'){
                        $months =  array("July"=> 1, "August"=>  2, "September"=>  3, "October"=>  4, "November"=>  5, "December"=>  6,
                            "January"=>  7, "February"=>  8, "March"=>  9, "April"=>  10, "May"=> 11, "June"=> 12);

                        if ($months[$a[$index]] == $months[$b[$index]]) {
                            return 0;
                        }
                        return ($months[$a[$index]] < $months[$b[$index]]) ? -1 : 1;
                    }
                    else if($a['formatType'] == "month" && RequestUtilities::get('yeartype') == 'C') {
                        $months =  array("January"=> 1, "February"=>  2, "March"=>  3, "April"=>  4, "May"=>  5, "June"=>  6,
                            "July"=>  7, "August"=>  8, "September"=>  9, "October"=>  10, "November"=> 11, "December"=> 12);

                        if ($months[$a[$index]] == $months[$b[$index]]) {
                            return 0;
                        }
                        return ($months[$a[$index]] < $months[$b[$index]]) ? -1 : 1;

                    }
                    else{
                        if ($a[$index] == $b[$index]) {
                            return 0;
                        }
                        return ($a[$index] > $b[$index]) ? -1 : 1;
                    }
                }
            );
        }
    }
}


/**
 * Function to handle export data
 */
function _checkbook_export_transactions(){
    if ($fileName = _checkbook_export_check_cached()) {
      _checkbook_export_download_file($fileName);
      return;
    }

    $exportMemoryRecordLimit = 0;

    //set_time_limit(0);
    $node = _checkbook_export_get_node_config();
    $limit = $node->widgetConfig->limit;

    //TODO - see if we have to check count again
    $node->widgetConfig->getTotalDataCount = false;
    $node->widgetConfig->getData = true;

    $isList = $node->widgetConfig->useList;

    //Get Export column Configuration
    $exportConfigNid = isset($node->widgetConfig->exportConfigNid)? $node->widgetConfig->exportConfigNid: null;
    if(isset($exportConfigNid)){
        $exportConfigNode = _checkbook_export_get_node_config($exportConfigNid);
        $exportColumns = $exportConfigNode->widgetConfig->table_columns;
    }else{
        $exportColumns = $node->widgetConfig->table_columns;
    }

    if($limit > $exportMemoryRecordLimit || isset($exportConfigNid)){
        $node->widgetConfig->generateDBQuery = true;
    }

    $columns = array();
    foreach($exportColumns as $column){
        if(!isset($column->datasource) || (isset($column->datasource) && ($column->datasource == RequestUtilities::get('datasource')))){
            if(check_node_flag_visibilty($column->visibility_flag, $node)) {
              array_push($columns, $column);
            }
        }
    }

    $headers = [];
    $i = 0;
    $columnsConfig = array();
    foreach($columns as $column){
        if(FALSE === $column->export){
            continue;
        }

        $label = $column->label;
        if (isset($column->labelAlias)) {
          $label = WidgetUtil::getLabel($column->labelAlias);
        } elseif (isset($column->colLabel)) {
          $label = $column->colLabel;
        }

        $label = str_replace("<br/>"," ",$label);

        $headers[] = $label;

        $columnsConfig[] = (isset($column->sortSourceColumn)) ? $column->sortSourceColumn : $column->column;
        $i++;
    }

    if (!empty($headers)) {
      $headers = '"'.join('","',$headers).'"';
    } else {
      $headers = '';
    }

    //handle additional columns required to export derived columns
    //Add these to the json as "export_columns"
    $exportColumns = $node->widgetConfig->export_columns;
    foreach($exportColumns as $column){
        if (!in_array($columnsConfig, $columns)) {
            array_push($columnsConfig, $column);
        }
    }
    if($isList){//Selecting only required columns.
        $node->widgetConfig->columns = $columnsConfig;
    }

    //load data
    widget_data($node);

    // $exportMemoryRecordLimit = 5000 ?

    if(($limit > $exportMemoryRecordLimit) || isset($exportConfigNid)){
        try{
            $fileName = _checkbook_export_generateExportFile($node, $headers, $exportConfigNode);
            _checkbook_export_download_file($fileName);
        }catch (Exception $e){
            LogHelper::log_error("Error generating export file:" . $e->getMessage());
            drupal_add_http_header("Content-Type", "text/plain");
            drupal_add_http_header("Content-Length",strlen($headers));
            drupal_add_http_header("Content-Disposition", "attachment; filename=TransactionsData.Error.txt");
            drupal_add_http_header("Pragma", "cache");
            drupal_add_http_header("Expires", "-1");
            echo "There was an error generating file.";
            return;
        }
    }else{
//        foreach($node->data as $dataRecord){
//            $data .= PHP_EOL;
//            $i = 0;
//            $rowData = NULL;
//            foreach($columns as $column){
//                if(property_exists($column, 'export') && $column->export === FALSE){
//                    continue;
//                }
//
//                $columnValue = NULL;
//                if(isset($column->exportColumn)){
//                    $columnValue = $dataRecord[$column->exportColumn];
//                }else if(isset($column->sortSourceColumn)){
//                    $columnValue = $isList ? $dataRecord[str_replace(".", "_",$column->sortSourceColumn)] : $dataRecord[$column->sortSourceColumn];
//                }else{
//                    $columnValue = $dataRecord[$column->column];
//                }
//
//                $columnValue = str_replace('"',chr(34).'"',$columnValue);
//                $rowData .= ($i == 0) ? ('"'.$columnValue.'"') : (',"'.$columnValue.'"');
//                $i++;
//            }
//
//            $rowData = str_replace(array("\r\n", "\n", "\r"),'',$rowData);
//            $data .= $rowData;
//        }
//
//        drupal_add_http_header("Content-Type", "text/csv");
//        drupal_add_http_header("Content-Length",strlen($data));
//        drupal_add_http_header("Content-Disposition", "attachment; filename=TransactionsData.csv");
//        drupal_add_http_header("Pragma", "cache");
//        drupal_add_http_header("Expires", "-1");
//        echo $data;
    }
}

/** Function to handle download export file */
function _checkbook_export_download_file($fileName){
    global $conf;

    $file = variable_get('file_public_path','sites/default/files') .'/'. $conf['check_book']['data_feeds']['output_file_dir'] .'/'. $conf['check_book']['export_data_dir'] .'/'. $fileName;

    drupal_add_http_header("Content-Type", "text/csv");
    drupal_add_http_header("Content-Disposition", "attachment; filename=TransactionsData.csv");
    drupal_add_http_header("Pragma", "cache");
    drupal_add_http_header("Expires", "-1");

    if(is_file($file)){
        $data = file_get_contents($file);
        drupal_add_http_header("Content-Length",strlen($data));
        echo $data;
    }else{
        echo "Data is not generated! Please contact support team.";
    }
}

/** don't regenerate cached file */
function _checkbook_export_check_cached(){
  if (user_is_logged_in()) {
    return false;
  }
  $fileDir = _checkbook_export_prepareFileOutputDir();
  $filename = _checkbook_export_generate_uuid(). '.csv';
  $outputFile = DRUPAL_ROOT . '/' . $fileDir . '/' . $filename;
  if(is_file($outputFile) && filesize($outputFile)) {
    LogHelper::log_notice('Already pre-cached: '.$outputFile);
    return $filename;
  }
  return false;
}

/** Function to handle generate export file */
function _checkbook_export_generateExportFile($node, $headers, $exportConfigNode = NULL){
    global $conf, $databases;

    try{
        $fileDir = _checkbook_export_prepareFileOutputDir();
        $filename = _checkbook_export_generate_uuid(). '.csv';
        $tmpDir =  (isset($conf['check_book']['tmpdir']) && is_dir($conf['check_book']['tmpdir'])) ? rtrim($conf['check_book']['tmpdir'],'/') : '/tmp';

        if(!is_writable($tmpDir)){
            LogHelper::log_error("$tmpDir is not writable. Please make sure this is writable to generate export file.");
            return $filename;
        }

        $tempOutputFile = $tmpDir .'/'. $filename;
        $outputFile = DRUPAL_ROOT . '/' . $fileDir . '/' . $filename;

        if(!user_is_logged_in() && is_file($outputFile) && filesize($outputFile)) {
          LogHelper::log_notice('Already cached: '.$outputFile);
          return $filename;
        }

        $columns = isset($exportConfigNode) ? $exportConfigNode->widgetConfig->table_columns : $node->widgetConfig->table_columns;
        $columnsConfig = array();
        foreach($columns as $column){
            if($column->column != "") {
                $columnsConfig[] = (isset($column->sortSourceColumn)) ? str_replace('.', '_', $column->sortSourceColumn) : str_replace('.', '_', $column->column);
            }
        }
        $input = $node->data;$start = "SELECT ";$end = " FROM (";
        $selectQuery = substr($input, strlen($start)+strpos($input, $start), (strlen($input) - strpos($input, $end))*(-1));
        //If there is no sub-query in the export query
        if(!$selectQuery){
            $end = " FROM ";
            $selectQuery = substr($input, strlen($start)+strpos($input, $start), (strlen($input) - strpos($input, $end))*(-1));
        }
        $selectColumns = explode(",",$selectQuery);
        $exportColumns = array();

        foreach($selectColumns as $key =>$value){
          $selectColumn = (strpos($value,' AS ')) ? str_replace('AS ','',substr($value, strpos($value,' AS ')+1)) : substr($value, strpos($value,'.')+1);
          if(in_array(trim($selectColumn),$columnsConfig)){
            $exportColumns[trim($selectColumn)] = $value;
          }
        }

        $newQuery = null;
        foreach($columnsConfig as $key => $value){
          $newQuery .= $exportColumns[$value] . ',';
        }
        $newQuery = substr($newQuery, 0, -1);log_error($newQuery);
        $node->data = str_replace($selectQuery,$newQuery, $node->data);log_error($node->data);

        /*
         * For data over the limit of 5000, the sql executes directly from the db.
         * Columns that are derived do not get selected, this allows us to override the
         * select value of the column sql directly.
         * */
        $sqlOverride = isset($exportConfigNode) ? $exportConfigNode->widgetConfig->exportColumnSqlOverride : $node->widgetConfig->exportColumnSqlOverride;
        if(isset($sqlOverride)) {
            $override_sql = null;
            $sql = $node->data;
            $table_columns = isset($exportConfigNode) ? $exportConfigNode->widgetConfig->table_columns : $node->widgetConfig->table_columns;
            $export_columns = $sqlOverride;
            $isList = $node->widgetConfig->useList;

            foreach($export_columns as $export_column){
                $column = $export_column->column;
                $source_column = $export_column->sourceColumn;
                foreach($table_columns as $table_column){
                    if($table_column->column == $column){
                        $override_sql = $export_column->sql;
                        $pos = strpos($sql,$source_column);
                        if ($pos !== false) {
                            if($isList) {
                                //Handle alias
                                $pos -= 3;
                                $alias_source_column = substr($sql, $pos, strlen($source_column)+3);
                                $replace = array($source_column=>$alias_source_column);
                                $override_sql = str_replace(array_keys($replace), array_values($replace), $override_sql);
                                $source_column = $alias_source_column;
                            }
                            $override_sql = substr_replace($sql,$override_sql,$pos,strlen($source_column));
                            $sql = $override_sql;
                        }
                    }
                }
            }
            if($override_sql != null) {
                $node->data = $sql;
            }
        }
        if (!empty($node->widgetConfig->export_columns)) {
            $node->data = checkbook_export_remove_non_export_columns($node);
        }

        LogHelper::log_notice("Exporting to file via SQL: \n".$node->data);

        $command = $conf['check_book']['data_feeds']['command'];

        $data_source = RequestUtilities::get('datasource');
        if ('checkbook_oge' == $data_source) {
            $command .= ' ' . $databases['checkbook_oge']['main']['database'] . ' ';
        } else {
            $command .= ' ' . $databases['checkbook']['main']['database'] . ' ';
        }

        $command .=
                " -c \"\\\\COPY (" . str_replace('\\\\','\\\\\\',$node->data) . ") TO '"
                . $tempOutputFile
                . "'  WITH DELIMITER ',' CSV QUOTE '\\\"' ESCAPE '\\\"' \" ";

//        log_error($command);
        LogHelper::log_notice($command);

        shell_exec($command);

        $cmd = "sed -i '1i" . escapeshellcmd($headers) . "' " . $tempOutputFile;
        LogHelper::log_notice($cmd);
        shell_exec($cmd);

        $mvcmd = "mv $tempOutputFile $outputFile";
        LogHelper::log_notice($mvcmd);
        shell_exec($mvcmd);

        LogHelper::log_notice("chmod 0666 $outputFile");
        chmod($outputFile, 0666);

        return $filename;

    }catch (Exception $e){
        $value = TextLogMessageTrimmer::$LOGGED_TEXT_LENGTH__MAXIMUM;
        TextLogMessageTrimmer::$LOGGED_TEXT_LENGTH__MAXIMUM = NULL;

        LogHelper::log_error($e);
        $msg = "Command used to generate the file: " . $command ;
        $msg .= ("Error generating DB command: " . $e->getMessage());
        LogHelper::log_error($msg);

        TextLogMessageTrimmer::$LOGGED_TEXT_LENGTH__MAXIMUM = $value;
    }
    return false;
}

/**
 * For columns included in config to properly build the sql
 * using the data controller for export,
 * these additional columns should not show on the export,
 * instead we need to strip them manually
 *
 * These are configured in $node->widgetConfig->export_columns
 *
 * @param $node
 * @return mixed
 */
function checkbook_export_remove_non_export_columns($node) {

    $columns = $node->widgetConfig->export_columns;
    $query = $node->data;

    $end = strpos($query, 'FROM');
    $select = substr($query,0,$end);
    $select = str_replace("SELECT", "", $select);
    $parts = explode(",", $select);

    $new_select = "";
    $newline = "\n       ";
    foreach($parts as $part) {
        $part = trim($part);
        $column = $part;
        if (strpos($column,"AS") !== false) {
            $pos = strpos($column, " AS");
            $column = substr($column,0,$pos);
        }
        if (strpos($column,".") !== false) {
            $pos = strpos($column, ".");
            $column = substr($column, $pos+1, strlen($column));
        }
        if (!in_array($column, $columns)) {
            $new_select .= $new_select == "" ? "SELECT ".$part : ",".$newline.$part;
        }
    }
    $new_select .= $newline;
    $query = substr_replace($query, $new_select, 0, $end);
    return $query;
}

/** Function to generate the path to save export file */
function _checkbook_export_prepareFileOutputDir(){
    global $conf;

    $dir = variable_get('file_public_path','sites/default/files') . '/' . $conf['check_book']['data_feeds']['output_file_dir'];
    _checkbook_export_prepareDirectory($dir);

    $dir .= '/' . $conf['check_book']['export_data_dir'];
    _checkbook_export_prepareDirectory($dir);

    return $dir;
}

/** Function to generate the path to save export file */
function _checkbook_export_prepareDirectory($dir){
    if(!file_prepare_directory($dir,FILE_CREATE_DIRECTORY)){
        LogHelper::log_error("Could not prepare file output directory $dir.Should check if this directory is writable.");
        throw new Exception("Could not prepare file. Please contact Support team.");
    }

    /*if(!is_link($dir) && !@chmod($dir,0777)){
        LogHelper::log_error("Could not update permissions to 777 for output directory $dir. Should check if this directory is writable.");
        throw new Exception("Could not prepare file. Please contact Support team.");
    }*/
}

/** Function to generate a unique id */
function _checkbook_export_generate_uuid(){
    if (!empty($_SERVER['REQUEST_METHOD']) && 'GET' === $_SERVER['REQUEST_METHOD']) {
      $data = $_SERVER['REQUEST_URI'].date('Y-m-d');
      return md5($data);
    }

    $guid = 'export';
    $uid = uniqid("", true);
    $data = '';
    $data .= $_SERVER['REQUEST_TIME'];
    $data .= $_SERVER['HTTP_USER_AGENT'];
    $data .= $_SERVER['LOCAL_ADDR'];
    $data .= $_SERVER['LOCAL_PORT'];
    $data .= $_SERVER['REMOTE_ADDR'];
    $data .= $_SERVER['REMOTE_PORT'];
    $hash = strtoupper(hash('ripemd128', $uid . $guid . md5($data)));
    $guid =
        substr($hash,  0,  8) .
            '-' .
            substr($hash,  8,  4) .
            '-' .
            substr($hash, 12,  4) .
            '-' .
            substr($hash, 16,  4) .
            '-' .
            substr($hash, 20, 12);

    return strtolower($guid);
}

/**
 * Function to return export data count
 *
 * @return int Record count
 */
function _checkbook_export_get_data_count(){
    $node = _checkbook_export_get_node_config();

    $node->widgetConfig->getTotalDataCount = true;
    $node->widgetConfig->getData = false;
    //load data
    widget_data($node);

    return $node->totalDataCount;
}

/**
 * Function to prepare widget using configuration
 *
 * @return bool|mixed Prepared widget node.
 */
function _checkbook_export_get_node_config($nodeId=NULL){
    global $conf;

    $_GET['q'] = urldecode(check_plain($_GET['refURL']));

    $nodeId = isset($nodeId) ? $nodeId : RequestUtilities::get('node');

    $node = node_load($nodeId);
    //widget_set_uid($node,"export");
    widget_config($node);
    //prepare anything we'll need before loading
    widget_prepare($node);
    //invoke widget specific prepare
    widget_invoke($node, 'widget_prepare');

    $limitPerPage = (isset($_GET['iDisplayLength']) ? (int)$_GET['iDisplayLength'] : $node->widgetConfig->limit);
    $startWith  =isset($_GET['iDisplayStart']) ? (int)$_GET['iDisplayStart'] : 0;

    $maxRecordsAllowed = $conf['check_book']['datatables']['iTotalDisplayRecords'];
    //This is to handle hacks
    if($startWith+$limitPerPage > $maxRecordsAllowed){
        $startWith = 0;
        $limitPerPage = $maxRecordsAllowed;
    }

    $node->widgetConfig->limit = $limitPerPage;
    $node->widgetConfig->startWith = $startWith;

    $node->widgetConfig->exportData = true;

    return $node;
}
