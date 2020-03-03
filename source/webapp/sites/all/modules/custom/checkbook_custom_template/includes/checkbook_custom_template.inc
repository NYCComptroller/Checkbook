<?php

/**
 * Returns the processed data from the node output
 * @param $node - node id
 * @param $param - data type
 */
function _checkbook_get_agency_budget_percent_difference($node, $param){

    widget_config($node);
	//prepare anything we'll need before loading
	widget_prepare($node);
	widget_data($node);

    echo _checkbook_get_agency_budget_percent_difference_json($node, $param);
}

/**
 * Returns the node data in json format
 * @param $node
 * @param $param - data type
 * @return false|string
 */
function _checkbook_get_agency_budget_percent_difference_json($node, $param){

    $url_path = drupal_get_path_alias($_GET['q']);
    $path_params = explode('/', $url_path);

    if(in_array("year", $path_params)){
        $yr_index = array_search("year",$path_params);
        $req_year_id = $path_params[$yr_index+1];
    }else{
        $req_year_id = CheckbookDateUtil::getCurrentFiscalYearId();
    }
    $req_year_value = _getYearValueFromID($req_year_id);

    if(in_array("agency", $path_params)){
        $ag_index = array_search("agency",$path_params);
        $req_agency_id = $path_params[$ag_index+1];
        $agency_path = "/agency/".$req_agency_id;
    }

    $table_rows = array();
    $years = array();
    $query_params=drupal_get_query_parameters();

    $limitPerPage = ($query_params['iDisplayLength'] != -1) ? $query_params['iDisplayLength'] : $node->widgetConfig->templateLimitRows;
    $startWith  = isset($query_params['iDisplayStart']) ? $query_params['iDisplayStart'] : 0;
    $offset = 0;

    if($param == 'revcat'){
        $id_index = 'category_category';
    }else if($param == 'fundsrccode'){
        $id_index = 'funding_funding';
    }else if($param == 'agency'){
        $id_index = 'agency_agency';
    }else if($param == 'dept'){
        $id_index = 'dept_dept';
    }

    foreach($node->data as $row){
        $table_rows[$row[$id_index]]['id'] = $row[$id_index];
        if($param == 'revcat'){
            $table_rows[$row[$id_index]]['name'] = $row['category_category_revenue_category_name'];
        }else if($param == 'fundsrccode'){
            $table_rows[$row[$id_index]]['name'] = $row['funding_funding_funding_class_name'];
        }else if($param == 'agency'){
            $table_rows[$row[$id_index]]['name'] = $row['agency_agency_agency_name'];
        }else if($param == 'dept'){
            $table_rows[$row[$id_index]]['name'] = $row['dept_dept_department_name'];
        }

        if($req_year_value == $row['year_year_year_value'] && $param != 'dept'){
            $table_rows[$row[$id_index]]['adopted_budget'] = $row['adopted_budget'];
            $table_rows[$row[$id_index]]['current_modified_budget'] = $row['current_modified_budget'];
        }

        $table_rows[$row[$id_index]]['revenue_collected'][$row['year_year_year_value']] = $row['revenue_amount_sum'];

        $years[$row['year_year_year_value']] = 	$row['year_year_year_value'];
    }

    $table_rows = _checkbook_custom_template_multisort_array($table_rows, $query_params['iColumns'], $query_params['iSortCol_0'],$query_params['sSortDir_0'],$req_year_id,$param);
    asort($years);

    $response = array(
        "sEcho"=> intval($_REQUEST['sEcho']),
        "iTotalRecords"=> count($table_rows),
        "iTotalDisplayRecords"=>count($table_rows),
        "iDisplayLength"=> $limitPerPage
    );

    $response['aaData'] = array();
    $i = 0;

    foreach($table_rows as $row){

        if($offset >= $startWith and $limitPerPage > 0){
            $rowarray = array();
            $widgetChart = null;

            if($param != 'dept'){
                $rowarray[] = "<a href='/revenue/year/". $req_year_id . "/" . $param ."/" .$row['id'].$agency_path."'>".$row['name']."</a>";
                $rowarray[] = "<a href='/budget/revenue/transactions/year/". $req_year_id . "/" . $param ."/". $row['id'].$agency_path."'>"
                              .custom_number_formatter_format($row['adopted_budget'], 2, '$')."</a>";
                $rowarray[] = "<a href='/budget/revenue/transactions/year/". $req_year_id . "/" . $param ."/" .$row['id'].$agency_path."'>"
                              .custom_number_formatter_format($row['current_modified_budget'], 2, '$')."</a>";
            }else{
                $rowarray[] = $row['name'];
            }

            $year_param = null;
            foreach ($years as $year){
              $rowarray[] = "<a href='/revenue/transactions/year/". _getYearIDFromValue($year) . "/" . $param ."/" .$row['id'].$agency_path."'>"
                            .custom_number_formatter_format($row['revenue_collected'][$year], 2, '$')."</a>";
               $year_param .= _getYearIDFromValue($year) . '~';
            }
            $year_param = substr($year_param, 0, strlen($year_param)-1);
            $total_revenue = array_sum($row['revenue_collected']);

            if($total_revenue > 0){
                $widgetNode = node_load($node->widgetConfig->templateWidgetNid);
                widget_set_uid($widgetNode,$i.'-'.uniqid());
                $additionalParams = array();
                $additionalParams[$node->widgetConfig->templateWidgetParam] = $row['id'];
                widget_add_additional_parameters($widgetNode,$additionalParams);
                $widgetChart = node_build_content($widgetNode);
                $widgetChart = drupal_render($widgetNode->content);


                $rowarray[] = '<table><tr><td>'."<a href='/revenue/transactions/year/". $year_param . "/" . $param ."/" .$row['id'].$agency_path."'>"
                              .custom_number_formatter_format($total_revenue, 2, '$')."</a>"
                              . '</td><td>' . $widgetChart . '</td></tr></table>';
            }else{
                $rowarray[] = "<a href='/revenue/transactions/year/". $year_param . "/" . $param ."/" .$row['id'].$agency_path."'>"
                              .custom_number_formatter_format($total_revenue, 2, '$')."</a>";
            }

            $i++;

            $response['aaData'][] = $rowarray;
            $limitPerPage = $limitPerPage - 1;
        }
        $offset +=1;
    }

    return json_encode($response);
}


/**
 * Returns the processed data from the node output
 * @param $node - node id
 * @param $param - data type
 */
function _checkbook_revenue_get_data($node, $param){

    widget_config($node);
	//prepare anything we'll need before loading
	widget_prepare($node);
	widget_data($node);
    
    echo _checkbook_revenue_get_data_json($node, $param);
}

/**
 * Returns the node data in json format
 * @param $node
 * @param $param - data type
 * @return false|string
 */
function _checkbook_revenue_get_data_json($node, $param){

    $url_path = drupal_get_path_alias($_GET['q']);
    $path_params = explode('/', $url_path);

    if(in_array("year", $path_params)){
        $yr_index = array_search("year",$path_params);
        $req_year_id = $path_params[$yr_index+1];
    }else{
        $req_year_id = CheckbookDateUtil::getCurrentFiscalYearId();
    }
    $req_year_value = _getYearValueFromID($req_year_id);

    if(in_array("agency", $path_params)){
        $ag_index = array_search("agency",$path_params);
        $req_agency_id = $path_params[$ag_index+1];
        $agency_path = "/agency/".$req_agency_id;
    }

    $table_rows = array();
    $years = array();
    $query_params=drupal_get_query_parameters();

    $limitPerPage = ($query_params['iDisplayLength'] != -1) ? $query_params['iDisplayLength'] : $node->widgetConfig->templateLimitRows;
    $startWith  = isset($query_params['iDisplayStart']) ? $query_params['iDisplayStart'] : 0;
    $offset = 0;

    if($param == 'revcat'){
        $id_index = 'category_category';
    }else if($param == 'fundsrccode'){
        $id_index = 'funding_funding';
    }else if($param == 'agency'){
        $id_index = 'agency_agency';
    }else if($param == 'dept'){
        $id_index = 'dept_dept';
    }

    foreach($node->data as $row){
        $table_rows[$row[$id_index]]['id'] = $row[$id_index];
        if($param == 'revcat'){
            $table_rows[$row[$id_index]]['name'] = $row['category_category_revenue_category_name'];
        }else if($param == 'fundsrccode'){
            $table_rows[$row[$id_index]]['name'] = $row['funding_funding_funding_class_name'];
        }else if($param == 'agency'){
            $table_rows[$row[$id_index]]['name'] = $row['agency_agency_agency_name'];
        }else if($param == 'dept'){
            $table_rows[$row[$id_index]]['name'] = $row['dept_dept_department_name'];
        }

        if($req_year_value == $row['year_year_year_value'] && $param != 'dept'){
            $table_rows[$row[$id_index]]['adopted_budget'] = $row['adopted_budget'];
            $table_rows[$row[$id_index]]['current_modified_budget'] = $row['current_modified_budget'];
        }

        $table_rows[$row[$id_index]]['revenue_collected'][$row['year_year_year_value']] = $row['revenue_amount_sum'];

        $years[$row['year_year_year_value']] = 	$row['year_year_year_value'];
    }

    $table_rows = _checkbook_custom_template_multisort_array($table_rows, $query_params['iColumns'], $query_params['iSortCol_0'],$query_params['sSortDir_0'],$req_year_id,$param);
    asort($years);

    $response = array(
        "sEcho"=> intval($_REQUEST['sEcho']),
        "iTotalRecords"=> count($table_rows),
        "iTotalDisplayRecords"=>count($table_rows),
        "iDisplayLength"=> $limitPerPage
    );

    $response['aaData'] = array();
    $i = 0;

    foreach($table_rows as $row){

        if($offset >= $startWith and $limitPerPage > 0){
            $rowarray = array();
            $widgetChart = null;

            if($param != 'dept'){
                $rowarray[] = "<a href='/revenue/year/". $req_year_id . "/" . $param ."/" .$row['id'].$agency_path."'>".$row['name']."</a>";
                $rowarray[] = "<a href='/budget/revenue/transactions/year/". $req_year_id . "/" . $param ."/". $row['id'].$agency_path."'>"
                              .custom_number_formatter_format($row['adopted_budget'], 2, '$')."</a>";
                $rowarray[] = "<a href='/budget/revenue/transactions/year/". $req_year_id . "/" . $param ."/" .$row['id'].$agency_path."'>"
                              .custom_number_formatter_format($row['current_modified_budget'], 2, '$')."</a>";
            }else{
                $rowarray[] = $row['name'];
            }
            
            $year_param = null;
            foreach ($years as $year){
              $rowarray[] = "<a href='/revenue/transactions/year/". _getYearIDFromValue($year) . "/" . $param ."/" .$row['id'].$agency_path."'>"
                            .custom_number_formatter_format($row['revenue_collected'][$year], 2, '$')."</a>";
               $year_param .= _getYearIDFromValue($year) . '~';
            }
            $year_param = substr($year_param, 0, strlen($year_param)-1);
            $total_revenue = array_sum($row['revenue_collected']);

            if($total_revenue > 0){
                $widgetNode = node_load($node->widgetConfig->templateWidgetNid);
                widget_set_uid($widgetNode,$i.'-'.uniqid());
                $additionalParams = array();
                $additionalParams[$node->widgetConfig->templateWidgetParam] = $row['id'];
                widget_add_additional_parameters($widgetNode,$additionalParams);
                $widgetChart = node_build_content($widgetNode);
                $widgetChart = drupal_render($widgetNode->content);


                $rowarray[] = '<table><tr><td>'."<a href='/revenue/transactions/year/". $year_param . "/" . $param ."/" .$row['id'].$agency_path."'>"
                              .custom_number_formatter_format($total_revenue, 2, '$')."</a>"
                              . '</td><td>' . $widgetChart . '</td></tr></table>';
            }else{
                $rowarray[] = "<a href='/revenue/transactions/year/". $year_param . "/" . $param ."/" .$row['id'].$agency_path."'>"
                              .custom_number_formatter_format($total_revenue, 2, '$')."</a>";
            }

            $i++;

            $response['aaData'][] = $rowarray;
            $limitPerPage = $limitPerPage - 1;
        }
        $offset +=1;
    }

    return json_encode($response);
}


/**
 * Page callback for view all popup for data tables which use 'template' sub type
 * @param $node
 * @param $param
 */

function _checkbook_custom_template_view_all_popup_template($node, $param){
    $_GET['q'] = drupal_get_path_alias($_GET['refURL']);
	widget_set_uid($node,"popup");
	widget_config($node);
	//prepare anything we'll need before loading
	widget_prepare($node);

    $node->widgetConfig->dataTableOptions->bPaginate = true;
	$node->widgetConfig->dataTableOptions->sPaginationType = "full_numbers";
    $node->widgetConfig->getTotalDataCount = true;
	$node->widgetConfig->dataTableOptions->iDisplayLength = 10;
	$node->widgetConfig->dataTableOptions->sDom = "<ir><t><p>";
	$node->widgetConfig->dataTableOptions->bInfo = true;
    $node->widgetConfig->viewAll = false;
    //"sDom":"<pir><t><p>",

    $node->widgetConfig->ajaxDataPopup = true;log_error($node);
	//load data
	widget_data($node);
    $nodeHTML = widget_invoke($node, 'widget_view');
    
    if($param == 'revcat'){
        $data_url = "/checkbook/view_all_popup_template_data/revenue_category/node/" . $node->nid . "?refURL=" . drupal_get_path_alias($_GET['q']);
    }else if($param == 'fundsrccode'){
        $data_url = "/checkbook/view_all_popup_template_data/funding_source/node/" . $node->nid . "?refURL=" . drupal_get_path_alias($_GET['q']);
    }else if($param == 'agency'){
        $data_url = "/checkbook/view_all_popup_template_data/agency_revenue/node/" . $node->nid . "?refURL=" . drupal_get_path_alias($_GET['q']);
    }else if($param == 'dept'){
        $data_url = "/checkbook/view_all_popup_template_data/dept_revenue/node/" . $node->nid . "?refURL=" . drupal_get_path_alias($_GET['q']);
    }

    $nodeHTML = preg_replace('/"sAjaxSource":".*"/','"sAjaxSource":"' . $data_url . '"', $nodeHTML);
    echo $nodeHTML;
}

/**
 * Page callback for  datatable popup pager..
 * @param $node
 * @param $param
 */
function _checkbook_custom_template_view_all_popup_template_data($node, $param){
  $_GET['q'] = drupal_get_path_alias($_GET['refURL']);
  widget_config($node);
  //prepare anything we'll need before loading
  widget_prepare($node);
  $node->widgetConfig->getTotalDataCount = true;
  //load data
  widget_data($node);

  echo _checkbook_revenue_get_data_json($node, $param);

}

/**
 * Function to sort a multi dimensional array
 * @param $table_rows
 * @param $total_col
 * @param $sort_col
 * @param $sort_dir
 * @param $req_year_id
 * @param $param
 * @return mixed
 */

function _checkbook_custom_template_multisort_array($table_rows, $total_col, $sort_col, $sort_dir, $req_year_id, $param){

    foreach($table_rows as $value){
        if($param != "dept"){
            if($sort_col == 0){
                $arg[] = $value['name'];
            }else if($sort_col == 1){
                $arg[] = $value['adopted_budget'];
            }else if($sort_col == 2){
                $arg[] = $value['current_modified_budget'];
            }else if($sort_col < ($total_col-1)){
                $year_value = _getYearValueFromID($req_year_id + ($sort_col- 3));
                $arg[] = $value['revenue_collected'][$year_value];
            }else if($sort_col == ($total_col-1)){
                $arg[] = array_sum($value['revenue_collected']);
            }
        }else{
            if($sort_col == 0){
                $arg[] = $value['name'];
            }else if($sort_col < ($total_col-1)){
                $year_value = _getYearValueFromID($req_year_id + ($sort_col- 1));
                $arg[] = $value['revenue_collected'][$year_value];
            }else if($sort_col == ($total_col-1)){
                $arg[] = array_sum($value['revenue_collected']);
            }
        }
    }

    if($sort_dir == 'asc'){
        array_multisort($arg, SORT_ASC, $table_rows);
    }else{
        array_multisort($arg, SORT_DESC, $table_rows);
    }
    return $table_rows;
}




