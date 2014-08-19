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

    $nodeId = _getRequestParamValue('node',false);
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
    
    
    $columns = $node->widgetConfig->gridConfig->table_columns;
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

                $columnValue = html_entity_decode($dataRec[$column->column]);
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

/**
 * Function to handle export data
 */
function _checkbook_export_transactions(){
    $exportMemoryRecordLimit = 5000;

    //set_time_limit(0);
    $node = _checkbook_export_get_node_config();
    $limit = $node->widgetConfig->limit;

    if($limit > $exportMemoryRecordLimit){
        $node->widgetConfig->generateDBQuery = true;
    }

    //TODO - see if we have to check count again
    $node->widgetConfig->getTotalDataCount = false;
    $node->widgetConfig->getData = true;

    $isList = $node->widgetConfig->useList;
    $data = NULL;
    $i = 0;
    $exportColumns = $node->widgetConfig->table_columns;
    $columns = array();
    foreach($exportColumns as $column){
        if(!isset($column->datasource) || (isset($column->datasource) && ($column->datasource == _getRequestParamValue('datasource')))){
            array_push($columns, $column);
        }
    }

    $columnsConfig = array();
    foreach($columns as $column){
        if($column->export === FALSE){
            continue;
        }

        $label = (isset($column->labelAlias))? (WidgetUtil::getLabel($column->labelAlias)) :
            (isset($column->colLabel) ? $column->colLabel : $column->label);

        if($i == 0){
            $data .= $label;
        }else{
            $data .= ',"'.$label.'"';
        }

        $columnsConfig[] = (isset($column->sortSourceColumn)) ? $column->sortSourceColumn : $column->column;
        $i++;
    }

    if($isList){//Selecting only required columns.
        $node->widgetConfig->columns = $columnsConfig;
    }
    
    //load data
    widget_data($node);

    if($limit > $exportMemoryRecordLimit){
        try{

            $fileName = _checkbook_export_generateExportFile($node,$data);
            _checkbook_export_download_file($fileName);
        }catch (Exception $e){
            LogHelper::log_error("Error generating export file:" . $e->getMessage());
            drupal_add_http_header("Content-Type", "text/csv");
            drupal_add_http_header("Content-Length",strlen($data));
            drupal_add_http_header("Content-Disposition", "attachment; filename=TransactionsData.csv");
            drupal_add_http_header("Pragma", "cache");
            drupal_add_http_header("Expires", "-1");
            echo "There was an error generating file.";
            return;
        }
    }else{
        foreach($node->data as $dataRecord){
            $data .= PHP_EOL;
            $i = 0;
            $rowData = NULL;
            foreach($columns as $column){
                if($column->export === FALSE){
                    continue;
                }

                $columnValue = NULL;
                if(isset($column->exportColumn)){
                    $columnValue = $dataRecord[$column->exportColumn];
                }else if(isset($column->sortSourceColumn)){
                    $columnValue = $isList ? $dataRecord[str_replace(".", "_",$column->sortSourceColumn)] : $dataRecord[$column->sortSourceColumn];
                }else{
                    $columnValue = $dataRecord[$column->column];
                }

                $columnValue = str_replace('"',chr(34).'"',$columnValue);
                $rowData .= ($i == 0) ? ('"'.$columnValue.'"') : (',"'.$columnValue.'"');
                $i++;
            }

            $rowData = str_replace(array("\r\n", "\n", "\r"),'',$rowData);
            $data .= $rowData;
        }

        drupal_add_http_header("Content-Type", "text/csv");
        drupal_add_http_header("Content-Length",strlen($data));
        drupal_add_http_header("Content-Disposition", "attachment; filename=TransactionsData.csv");
        drupal_add_http_header("Pragma", "cache");
        drupal_add_http_header("Expires", "-1");
        echo $data;
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
        echo "Data is not generated. Please contact support team.";
    }
}

/** Function to handle generate export file */
function _checkbook_export_generateExportFile($node,$headers){
    global $conf;

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
        
        $columns = $node->widgetConfig->table_columns;
        $columnsConfig = array();
        foreach($columns as $column){
          $columnsConfig[] = (isset($column->sortSourceColumn)) ? str_replace('.','_',$column->sortSourceColumn) : str_replace('.','_',$column->column);
        }
        
        $input = $node->data;$start = "SELECT ";$end = " FROM (";
        $selectQuery = substr($input, strlen($start)+strpos($input, $start), (strlen($input) - strpos($input, $end))*(-1));
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
        $newQuery = substr($newQuery, 0, -1);
        $node->data = str_replace($selectQuery,$newQuery, $node->data);
                
        

        $command = $conf['check_book']['data_feeds']['command']
                . " -c \"\\\\COPY (" . str_replace('\\\\','\\\\\\',$node->data) . ") TO '"
                . $tempOutputFile
                . "'  WITH DELIMITER ',' CSV QUOTE '\\\"' ESCAPE '\\\"' \" ";
        
        log_error($command);
        
        shell_exec($command);

        $cmd = "sed -i 1i'" . $headers . "' " . $tempOutputFile;
        shell_exec($cmd);

        $mvcmd = "mv $tempOutputFile $outputFile";
        shell_exec($mvcmd);

    }catch (Exception $e){
        $value = TextLogMessageTrimmer::$LOGGED_TEXT_LENGTH__MAXIMUM;
        TextLogMessageTrimmer::$LOGGED_TEXT_LENGTH__MAXIMUM = NULL;

        LogHelper::log_error($e);
        $msg = "Command used to generate the file: " . $command ;
        $msg .= ("Error generating DB command: " . $e->getMessage());
        LogHelper::log_error($msg);

        TextLogMessageTrimmer::$LOGGED_TEXT_LENGTH__MAXIMUM = $value;
    }

    return $filename;
}

/** Function to generate the path to save export file */
function _checkbook_export_prepareFileOutputDir(){
    global $conf;

    $dir = variable_get('file_public_path','sites/default/files') . '/' . $conf['check_book']['data_feeds']['output_file_dir'];
    _checkbook_export_prepareDirectory($dir);

    $dir .= '/' . $conf['check_book']['export_data_dir'];
    _checkbook_export_prepareDirectory($dir);

    try{
        //delete files older than 2 hours
        _checkbook_project_clean_files($dir);
    }catch (Exception $e){
        LogHelper::log_error($e);
    }

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
function _checkbook_export_get_node_config(){
    global $conf;

    $_GET['q'] = urldecode(check_plain($_GET['refURL']));

    $nodeId = _getRequestParamValue('node');

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
