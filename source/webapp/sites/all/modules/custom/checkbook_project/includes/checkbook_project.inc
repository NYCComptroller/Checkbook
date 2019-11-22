<?php


/**
 * This function returns current NYC fiscal year ID
 * @return string
 */
function _getFiscalYearID()
{
  return CheckbookDateUtil::getCurrentFiscalYearId();
}

/**
 * This function returns Calendar year ID
 * @return string
 */
function _getCurrentCalendarYearID()
{
  return CheckbookDateUtil::getCurrentCalendarYearId();
}

/**
 * @return mixed
 */
function _getCurrentYearID()
{
  return CheckbookDateUtil::getCurrentFiscalYearId();
}

/**
 * returns NYC year id for a giver year ...
 * @param string $year_value
 * @return string
 */
function _getYearIDFromValue($year_value)
{
  return CheckbookDateUtil::year2yearId($year_value);
}

/**
 * return year value for a give year id ...
 * @param string $year_id
 * @return string
 */
function _getYearValueFromID($year_id)
{
  return CheckbookDateUtil::yearId2Year($year_id);
}

/**
 * return full year text value for a give year id ...
 * @return string
 */
function _getFullYearString()
{

  $year_id = RequestUtilities::get('year');
  if (empty($year_id)) {
    $year_id = RequestUtilities::get('calyear');
  }
  $yearType = RequestUtilities::get('yeartype');
  if (empty($yearType)) {
    $yearType = 'B';
  }

  if (empty($year_id)) {
    $year_id = CheckbookDateUtil::getCurrentFiscalYearId();
  }

  $yearValue = _getYearValueFromID($year_id);
  $yearString = ($yearType == 'B') ? "FY $yearValue" : "CY $yearValue";
  if (RequestUtilities::get('datasource') == Datasource::NYCHA) {
    $yearString .= "(January 1, " . ($yearValue) . " - Decemeber 31, $yearValue)";
  } else {
    $yearString .= ($yearType == 'B') ? " (July 1, " . ($yearValue - 1) . " - June 30, $yearValue)" : " (January 1, {$yearValue} - December 31, $yearValue)";
  }


  return $yearString;
}

/**
 * returns NYC month id for a different year id
 * @param $month_id
 * @param $year_id
 * @param null $year_type
 * @return mixed
 */
function _translateMonthIdByYear($month_id, $year_id, $year_type = null)
{
  $month_value = _getMonthValueFromId($month_id);
  $month_id = _getMonthIDFromValue($month_value, $year_id, $year_type);
  return $month_id;
}

/**
 * returns NYC month id for a given month value and year id
 * @param $month_value
 * @param $year_id
 * @param $year_type
 * @return mixed
 */
function _getMonthIDFromValue($month_value, $year_id, $year_type = null)
{
  $month_id = null;
  if ($year_type == "C") {
    $monthIDs = _checkbook_project_querydataset('checkbook:month', array('month_id'), array('month_value' => $month_value, 'year_id' => $year_id));
    $month_id = $monthIDs[0]['month_id'];
  } else {
    $query =
      "SELECT DISTINCT calendar_month_id FROM ref_date date
            JOIN ref_year year ON year.year_id = date.nyc_year_id
            JOIN ref_month month ON month.month_id = date.calendar_month_id
            WHERE year.year_id = " . $year_id . " AND month.month_value = " . $month_value;
    $results = _checkbook_project_execute_sql($query, "main");
    $month_id = $results[0]['calendar_month_id'];

  }
  return $month_id;
}

/**
 * returns NYC month value for a given month id
 * @param $month_id
 * @return mixed
 */
function _getMonthValueFromId($month_id)
{
  $monthValues = _checkbook_project_querydataset('checkbook:month', array('month_value'), array('month_id' => $month_id));
  $month_value = $monthValues[0]['month_value'];
  return $month_value;
}

/**
 * If the length of the text passed exceeds the specified length,
 * replaces the remaining characters with "..." and add a tooltip
 *
 * @param $text
 * @param int $length
 * @return string
 */
function _shorten_word_with_tooltip($text, $length = 20)
{

  if (strlen($text) > $length) {
    return "<span title='" . htmlentities($text, ENT_QUOTES) . "'>" . substr(htmlentities($text), 0, $length - 3) . "...</span>";
  }
  return $text;
}


/**
 * return replacment patter for title ...
 * @param string $text
 * @param integer $length
 * @param int $no_of_lines
 * @return string
 */
function _get_tooltip_markup($text, $length = 20, $no_of_lines = 2)
{
  return _break_text_custom(html_entity_decode($text, ENT_QUOTES), $length, $no_of_lines);

}

/**
 * return replacment patter for title ...
 * @param string $text
 * @param integer $length
 * @param int $no_of_lines
 * @return string
 */
function _break_text_custom($text, $length = 20, $no_of_lines = 2)
{
  $text_array = explode(" ", $text);
  $offset = 0;
  $remaining_original = $remaining = ($length % 2 == 0) ? ($length / $no_of_lines) : (round($length / $no_of_lines) - 1);
  $first_line = true;

  $index = 0;

  if (strlen($text_array[0]) >= 2 * $remaining_original) {
    return "<span title='" . htmlentities($text, ENT_QUOTES) . "'>" . substr(htmlentities($text), 0, $length - 3) . "...</span>";
  } else {
    foreach ($text_array as $key => $value) {
      if (isset($first_line) && $first_line) {
        if (strlen($value) >= $remaining && $index == 0) {
          $first_line = false;
          $second_line = true;
          $remaining = 2 * $remaining_original - strlen($value) - 1;
          $offset = strlen($value) + 1;
        } else {
          $remaining = $remaining - (strlen($value) + 1);
          if ($remaining <= 0) {
            $first_line = false;
            $second_line = true;
            $remaining = $remaining_original;
          } else {
            $offset += strlen($value) + 1;
          }
        }
      }
      if (isset($second_line) && $second_line) {
        $prev_remaining = $remaining;
        $remaining = $remaining - (strlen($value) + 1);
        if ($remaining >= 0) {
          $offset = $offset + strlen($value) + 1;
        } else {
          $offset = $offset + ($prev_remaining - 3);
          $second_line = false;
        }
      }
      $index += 1;
    }

    if ($offset < strlen($text))
      return "<span title='" . htmlentities($text, ENT_QUOTES) . "'>" . htmlentities(substr($text, 0, $offset)) . "...</span>";
    else
      return "<span title='" . htmlentities($text, ENT_QUOTES) . "'>" . htmlentities(substr($text, 0, $offset)) . "</span>";
  }
}

/**
 * return replacment patter for title ...
 * @param string $text
 * @param integer $length
 * @return string
 */
function _break_text_custom2($text, $length = 18)
{
  $text = html_entity_decode($text, ENT_QUOTES);
  $text_array = explode(" ", $text);
  $breaked_text = "";
  foreach ($text_array as $key => $value) {
    if (strlen($value) >= $length)
      $value = implode(" ", str_split($value, $length));
    $breaked_text = $breaked_text . " " . $value;
  }

  return htmlentities($breaked_text);

}

/**
 * returns request parameter value from URL($_REQUEST['q'])
 * @param string $paramName
 * @param boolean $fromRequestPath
 * @return string request
 */
function _getRequestParamValue($paramName, $fromRequestPath = True)
{
  return RequestUtilities::get($paramName, $fromRequestPath);
}

/**
 * returns request parameter value from bottomURL
 * @param string $paramName
 * @param string $fromRequestPath
 * @return string
 */
function _getRequestParamValueBottomURL($paramName, $fromRequestPath = 'TRUE')
{
  if (empty($paramName)) {
    return NULL;
  }
  $value = NULL;
  if ($fromRequestPath) {
    $bottomURL = $_REQUEST['expandBottomContURL'];
    $pathParams = explode('/', $bottomURL);
    $index = array_search($paramName, $pathParams);
    if ($index !== FALSE) {
      $value = filter_xss($pathParams[($index + 1)]);
    }
    if (trim($value) == "") {
      return NULL;
    }
    if (isset($value) || $fromRequestPath) {
      return htmlspecialchars_decode($value, ENT_QUOTES);
    }
  } else {
    return filter_xss(htmlspecialchars_decode($_GET[$paramName], ENT_QUOTES));
  }
}

/**
 * Page callback for grid view popupdatatable..
 * @param $node
 * @return mixed|string
 * @throws Exception
 */
function _checkbook_project_grid_view_popup($node)
{
  $url = $_GET['q'];
  $_GET['original_q'] = drupal_get_path_alias($_GET['q']);
  $_GET['q'] = drupal_get_path_alias($_GET['refURL']);

  //widget_set_uid($node,"gridpopup");
  widget_config($node);

  if (isset($node->widgetConfig->gridConfig->adjustWidgetConfig)) {
    eval($node->widgetConfig->gridConfig->adjustWidgetConfig);
  }

  $node->widgetConfig->getTotalDataCount = true;
  $node->widgetConfig->displayType = 'gridview';

  //prepare anything we'll need before loading
  widget_prepare($node);
  //invoke widget specific prepare
  widget_invoke($node, 'widget_prepare');
  widget_data($node);
  $themekey = $node->widgetConfig->gridConfig->template;

  $html = theme($themekey, array('node' => $node));
  $_GET['q'] = $url;
  return $html;
}


/**
 * @param $node
 *
 * @return string
 * @throws \Exception
 */
function _checkbook_project_grid_view_widget_popup($node)
{
  $url = $_GET['q'];
  $_GET['original_q'] = drupal_get_path_alias($_GET['q']);
  $_GET['q'] = drupal_get_path_alias($_GET['refURL']);

  //widget_set_uid($node,"gridpopup");
  widget_config($node);

  if (isset($node->widgetConfig->gridConfig->adjustWidgetConfig)) {
    eval($node->widgetConfig->gridConfig->adjustWidgetConfig);
  }

  $node->widgetConfig->getTotalDataCount = true;
  $node->widgetConfig->displayType = 'gridview';

  //prepare anything we'll need before loading
  widget_prepare($node);
  //invoke widget specific prepare
  widget_invoke($node, 'widget_prepare');
  widget_data($node);

  if (isset($node->widgetConfig->widgetUpdateJSONConfig)) {
    @eval($node->widgetConfig->widgetUpdateJSONConfig);
  }

  $themekey = $node->widgetConfig->gridConfig->template;

  $html = theme($themekey, array('node' => $node));
  $_GET['q'] = $url;
  return $html;
}

/**
 * Page callback for popupdatatable.. Only called on initial load ...
 * @param $node
 * @return mixed|string|string[]|null
 */
function _checkbook_project_view_all_popup($node)
{
  $_GET['q'] = drupal_get_path_alias($_GET['refURL']);
  widget_set_uid($node, "popup");
  widget_config($node);
  //prepare anything we'll need before loading
  widget_prepare($node);
  //invoke widget specific prepare
  widget_invoke($node, 'widget_prepare');
  $node->widgetConfig->dataTableOptions->bPaginate = true;
  $node->widgetConfig->dataTableOptions->sPaginationType = "full_numbers";
  $node->widgetConfig->dataTableOptions->sDom = "<pir><t><p>";
  $node->widgetConfig->disableViewAll = true;
  $node->widgetConfig->ajaxDataPopup = true;
  if (RequestUtilities::get('gridview')) {
    $node->widgetConfig->displayType = 'gridview';
    $node->widgetConfig->dataTableOptions->fnDrawCallback = NULL;
    $nodeHTML = widget_invoke($node, 'widget_view');
    $data_url = "/checkbook/view_all_popup_data/node/" . $node->nid . "?refURL=" . drupal_get_path_alias($_GET['q']);
    $nodeHTML = preg_replace('/"sAjaxSource":".*"/', '"sAjaxSource":"' . $data_url . '"', $nodeHTML);
    return $nodeHTML;
  } else {
    $nodeHTML = widget_invoke($node, 'widget_view');
    $data_url = "/checkbook/view_all_popup_data/node/" . $node->nid . "?refURL=" . drupal_get_path_alias($_GET['q']);
    $nodeHTML = preg_replace('/"sAjaxSource":".*"/', '"sAjaxSource":"' . $data_url . '"', $nodeHTML);
    echo $nodeHTML;
  }
}

/**
 * Page callback for  datatable popup pager..
 * @param $node
 * @return string
 */
function _checkbook_project_view_all_popup_data($node)
{
  $_GET['q'] = drupal_get_path_alias($_GET['refURL']);
  $limitPerPage = (isset($node->widgetConfig->limitPerPage) ? $node->widgetConfig->limitPerPage : 10);

  widget_config($node);
  //prepare anything we'll need before loading
  widget_prepare($node);
  //invoke widget specific prepare
  widget_invoke($node, 'widget_prepare');
  $node->widgetConfig->limit = $limitPerPage;
  $node->widgetConfig->getTotalDataCount = true;
  if (RequestUtilities::get('gridview')) {
    $node->widgetConfig->displayType = 'gridview';
  }
  //load data
  widget_data($node);

  switch ($node->widgetConfig->widgetSubType) {
    case "ajaxSimple":
      $response = array(
        "sEcho" => $_REQUEST['sEcho'],
        "iTotalRecords" => $node->totalDataCount,
        "iTotalDisplayRecords" => $node->totalDataCount,
        "iDisplayLength" => $limitPerPage
      );
      $response['aaData'] = array();
      foreach ($node->data as $datarow) {
        $rowarray = array();
        foreach ($node->widgetConfig->table_columns as $row) {
          $rowarray[] = $datarow[$row->column];
        }
        $response['aaData'][] = $rowarray;
      }
      $response = json_encode($response);
      break;

    case "dataTableWidgets":
      $response = _widget_data_tables_list_wt_prepare_json($node);
      break;
    default:
      return "";
      break;
  }


  echo $response;

}


/**
 * Page callback for displaying node view
 * @param $node
 * @throws Exception
 */
function _checkbook_project_node_display($node)
{
  widget_config($node);

  //prepare anything we'll need before loading
  widget_prepare($node);
  //invoke widget specific prepare
  widget_invoke($node, 'widget_prepare');
  widget_data($node);

  $themekey = $node->widgetConfig->template;
  echo theme($themekey, array('node' => $node));
}

/**
 * Return the name for the reference object... this will be used to get agency name, department name, vendor name etc from a give id
 * @param $keyword
 * @param $id
 * @return string
 */
function _checkbook_project_get_name_for_argument($keyword, $id)
{
  if (_checkbook_check_isNYCHAPage()) {
    $data_source = Datasource::NYCHA;
  } else {
    $data_source = Datasource::CITYWIDE;
  }
  switch ($keyword) {
    case "agency_id":
      $agencies = _checkbook_project_querydataset($data_source . ':agency', array('agency_id', 'agency_name'), array($keyword => $id));
      return htmlentities(ucwords(strtolower($agencies[0]['agency_name'])));
      break;
    case "object_class_id":
      $objects = _checkbook_project_querydataset($data_source . ':object_class', array('object_class_id', 'object_class_name'), array($keyword => $id));
      return ucwords(strtolower($objects[0]['object_class_name']));
      break;
    case "budget_code_id":
      $budget_codes = _checkbook_project_querydataset($data_source . ':budget_code', array('budget_code_id', 'attribute_name'), array($keyword => $id));
      return ucwords(strtolower($budget_codes[0]['attribute_name']));
      break;
    case "revenue_category_id":
      $revcat_ids = _checkbook_project_querydataset($data_source . ':revenue_category', array('revenue_category_id', 'revenue_category_name'), array($keyword => $id));
      return ucwords(strtolower($revcat_ids[0]['revenue_category_name']));
      break;
    case "funding_class_id":
      $fundsrc_ids = _checkbook_project_querydataset($data_source . ':ref_funding_class', array('funding_class_id', 'funding_class_name'), array($keyword => $id));
      return ucwords(strtolower($fundsrc_ids[0]['funding_class_name']));
      break;
    case "funding_class_code":
      $revcat_codes = _checkbook_project_querydataset($data_source . ':funding_class', array('funding_class_code', 'funding_class_name'), array($keyword => $id));
      return ucwords(strtolower($revcat_codes[0]['funding_class_name']));
      break;
    case "funding_source_id":
      $fundsrc_ids = _checkbook_project_querydataset($data_source . ':funding_source', array('funding_source_id', 'display_funding_source_descr'), array($keyword => $id));
      return ucwords(strtolower($fundsrc_ids[0]['display_funding_source_descr']));
      break;
    case "vendor_id":
      if ($data_source == Datasource::NYCHA) {
        $vendors = _checkbook_project_querydataset($data_source . ':vendor', array('vendor_id', 'vendor_name'), array($keyword => $id));
        return ucwords(strtolower($vendors[0]['vendor_name']));
      } else {
        $vendors = _checkbook_project_querydataset($data_source . ':vendor', array('vendor_id', 'legal_name'), array($keyword => $id));
        return ucwords(strtolower($vendors[0]['legal_name']));
      }
      break;
    case "year_id":
      $years = _checkbook_project_querydataset($data_source . ':year', array('year_id', 'year_value'), array($keyword => $id));
      return ucwords(strtolower($years[0]['year_value']));
      break;
    case "department_id":
      $depts = _checkbook_project_querydataset($data_source . ':department', array('department_id', 'department_name'), array($keyword => $id));
      return ucwords(strtolower($depts[0]['department_name']));
      break;
    case "department_code":
      $deptcodes = _checkbook_project_querydataset($data_source . ':budget', array('department_code', 'department_name'), array($keyword => $id));
      return ucwords(strtolower($deptcodes[0]['department_name']));
      break;
    case "award_method_id":
      $award_methods = _checkbook_project_querydataset($data_source . ':award_method', array('award_method_id', 'award_method_name'), array($keyword => $id));
      return ucwords(strtolower($award_methods[0]['award_method_name']));
      break;
    case "award_size_id":
      $award_sizes = _checkbook_project_querydataset($data_source . ':award_size', array('award_size_id', 'award_size_name'), array($keyword => $id));
      return ucwords(strtolower('Contracts ' . $award_sizes[0]['award_size_name']));
      break;
    case "industry_type_id":
      if ($data_source == Datasource::NYCHA) {
        $industry_types = _checkbook_project_querydataset($data_source . ':industry_type', array('industry_type_id', 'display_industry_type_name'), array($keyword => $id));
        return ucwords(strtolower($industry_types[0]['display_industry_type_name']));
      } else {
        $industry_types = _checkbook_project_querydataset($data_source . ':industry_type', array('industry_type_id', 'industry_type_name'), array($keyword => $id));
        return ucwords(strtolower($industry_types[0]['industry_type_name']));
      }
      break;
    case "pending_contracts_vendor_id":
      $keyword = 'vendor_id';
      $pending_contracts_vendors = _checkbook_project_querydataset($data_source . ':pending_contracts', array('vendor_id', 'vendor_legal_name'), array($keyword => $id));
      return ucwords(strtolower($pending_contracts_vendors[0]['vendor_legal_name']));
      break;
    case "pending_contracts_award_method":
      $keyword = 'award_method_code';
      $pending_contracts_award_method = _checkbook_project_querydataset($data_source . ':pending_contracts', array('award_method_code', 'award_method_name'), array($keyword => $id));
      return ucwords(strtolower($pending_contracts_award_method[0]['award_method_name']));
      break;
    case "employee_id":
      $employees = _checkbook_project_querydataset($data_source . ':employee', array('civil_service_title', 'employee_id'), array($keyword => $id));
      return ucwords(strtolower($employees[0]['civil_service_title']));
      break;
    case "expenditure_object_id":
      $exp_categorys = _checkbook_project_querydataset($data_source . ':expenditure_object', array('expenditure_object_name', 'expenditure_object_id'), array($keyword => $id));
      return ucwords(strtolower($exp_categorys[0]['expenditure_object_name']));
      break;
    case "agreement_id":
      $contracts = _checkbook_project_querydataset($data_source . ':history_agreement', array('contract_number', 'agreement_id'), array($keyword => $id));
      return ucwords(strtolower($contracts[0]['contract_number']));
      break;
    case "master_agreement_id":
      $contracts = _checkbook_project_querydataset($data_source . ':history_master_agreement', array('contract_number', 'master_agreement_id'), array($keyword => $id));
      return ucwords(strtolower($contracts[0]['contract_number']));
      break;
    case "document_code":
      $agencies = _checkbook_project_querydataset($data_source . ':ref_document_code', array('document_code', 'document_code_id'), array($keyword => $id));
      return htmlentities(ucwords(strtolower($agencies[0]['document_code_id'])));
      break;
    case "sub_vendor_id":
      $keyword = "vendor_id";
      $sub_vendors = _checkbook_project_querydataset($data_source . ':sub_vendor', array('vendor_id', 'legal_name'), array($keyword => $id));
      return htmlentities(ucwords(strtolower($sub_vendors[0]['legal_name'])));
      break;
    case "minority_type_id":
      $keyword = "minority_type_id";
      $mwbe = _checkbook_project_querydataset($data_source . ':minority_type', array('minority_type_id', 'minority_type_name'), array($keyword => $id));
      return htmlentities(ucwords(strtolower($mwbe[0]['minority_type_name'])));
      break;
    case "title":
      $title_code = RequestUtil::getRequestKeyValueFromURL("title", current_path());
      return htmlentities(ucwords(PayrollUtil::getTitleByCode($title_code)));
      break;

    default:
      return $id;
      break;
  }

}

/**
 * query the dataset and get data ...
 * @param string $dataset
 * @param array|string $columns
 * @param null $parameters
 * @return mixed
 */
function _checkbook_project_querydataset($dataset, $columns, $parameters = null)
{
  try {
    $dataController = data_controller_get_instance();
    $dataset = _update_dataset_datasource($dataset);
    $data = $dataController->queryDataset($dataset, $columns, $parameters, NULL, 0, NULL, NULL);
  } catch (Exception $exception) {
    log_error("Error getting data from the controller: \n" . $exception->getMessage()/*, $e*/);
    return;
  }
  return $data;
}

/**
 * Function to adjust custom contract parameters passed to data controller module
 * @param $node Node data
 * @param $parameters Widget parsed request parameters
 * @param $contractType Contract Type
 * @param null $dimension
 * @return mixed adjusted parameters
 */
function _checkbook_project_adjustContractParameterFilters(&$node, &$parameters, $contractType, $dimension = null)
{

  //$contractCategoryParam = 'contcat';
  $contractStatusParam = 'contstatus';
  $yearParam = 'year';
  $yearTypeParam = 'yeartype';

  /*$contCategoryCol = $node->widgetConfig->urlParamMap->$contractCategoryParam;
  $reqContCat = $node->widgetConfig->requestParams[$contCategoryCol];*/

  switch ($contractType) {
    case "active_registered":

      //adjust dataset
      $yearTypeCol = $node->widgetConfig->urlParamMap->$yearTypeParam;
      $reqYearType = $node->widgetConfig->requestParams[$yearTypeCol];
      $current_url = $_SERVER['HTTP_REFERER'];
//            if (preg_match("/contract\/search\/transactions/", $current_url)) {
//                $advanced_search = true;
//            } else {
//                $current_url = request_uri();
//                if (preg_match("/contract\/search\/transactions/", $current_url)) {
//                    $advanced_search = true;
//                }
//            }

      if (isset($reqYearType) && ($reqYearType == 'C') && !_checkbook_check_isEDCPage()) {
        if (isset($node->widgetConfig->dataset)) {
          $node->widgetConfig->dataset = 'checkbook:all_agreement_transactions_cy';
        } else if (isset($node->widgetConfig->model->series) && is_array($node->widgetConfig->model->series)) {
          foreach ($node->widgetConfig->model->series as $series) {
            $series->dataset = 'checkbook:all_agreement_transactions_cy';
          }
        }
      }

      //adjust status
      $contractStatusCol = $node->widgetConfig->urlParamMap->$contractStatusParam;
      $contractStatus = $node->widgetConfig->requestParams[$contractStatusCol];
      if (isset($contractStatus)) {
        $parameters[$contractStatusCol] = $contractStatus;
      }

      //adjust year
      $yearCol = isset($node->widgetConfig->urlParamMap->$yearParam) ? $node->widgetConfig->urlParamMap->$yearParam : $node->widgetConfig->urlParamMap->calyear;
      $reqYear = $node->widgetConfig->requestParams[$yearCol];
      if (isset($reqYear)) {
        $data_controller_instance = data_controller_get_operator_factory_instance();
        $geCondition = $data_controller_instance->initiateHandler(GreaterOrEqualOperatorHandler::$OPERATOR__NAME, array($reqYear));
        $leCondition = $data_controller_instance->initiateHandler(LessOrEqualOperatorHandler::$OPERATOR__NAME, array($reqYear));
        if (isset($dimension)) {
          $parameters[$dimension . '.' . $dimension . '.starting_year_id'] = $leCondition;
          $parameters[$dimension . '.' . $dimension . '.ending_year_id'] = $geCondition;
        } else {
          $parameters['starting_year_id'] = $leCondition;
          $parameters['ending_year_id'] = $geCondition;
        }
        if ($contractStatus == 'R') {
          if (isset($dimension))
            $parameters[$dimension . '.' . $dimension . '.registered_year_id'] = array($reqYear);
          else
            $parameters['registered_year_id'] = array($reqYear);
        } else if ($contractStatus == 'A') {
          if (isset($dimension)) {
            $parameters[$dimension . '.' . $dimension . '.effective_begin_year_id'] = $leCondition;
            $parameters[$dimension . '.' . $dimension . '.effective_end_year_id'] = $geCondition;
          } else {
            $parameters['effective_begin_year_id'] = $leCondition;
            $parameters['effective_end_year_id'] = $geCondition;
          }

        }
      }

      //adjust category
      /*if(isset($reqContCat)){
          if($reqContCat=='revenue'){
              $docCodes = 'RCT1';
          }else if($reqContCat=='expense'){
              $docCodes = array("MMA1","MA1","CT1");
          }else if($reqContCat=='allexpense'){
              $docCodes = array("MMA1","MA1","CT1","DO1","CTA1");
          }

          $node->widgetConfig->requestParams[$contCategoryCol]= $docCodes;
          $node->widgetConfig->originalRequestParams[$contractCategoryParam]= $docCodes;
          $parameters[$contCategoryCol]= $docCodes;
      }*/

      break;

    /*case "pending":
        //adjust category
        if(isset($reqContCat)){
            if($reqContCat=='revenue'){
                $docCodes = 'RCT1';
            }else if($reqContCat=='expense'){
                $docCodes = array("MMA1","MA1","CT1","MAR","CTR","CTA1");
            }

            $node->widgetConfig->requestParams[$contCategoryCol]= $docCodes;
            $node->widgetConfig->originalRequestParams[$contractCategoryParam]= $docCodes;
            $parameters[$contCategoryCol]= $docCodes;
        }
    break;*/

    case "active_registered_all":
      /*if(RequestUtilities::getRequestParamValue($contractStatusParam) =='A'){
          $data_controller_instance = data_controller_get_operator_factory_instance();
          $leCondition = $data_controller_instance->initiateHandler(LessOrEqualOperatorHandler::$OPERATOR__NAME, array(date('Y-m-d')));
          $parameters['effective_begin_date'] = $leCondition;
      }*/
      break;

    default:
      break;
  }


}

/**
 * @param $node
 * @param $parameters
 *
 * @return mixed
 */
function _checkbook_project_adjust_spending_parameter_filters(&$node, &$parameters)
{
  $cat = $parameters['spending_category_id'];
  if (isset($parameters['ctx.ctx.vendor_id']) || isset($parameters['ctx.ctx.document_agency_id']) || isset($parameters['ctx.ctx.award_method_id']) || isset($parameters['ctx.ctx.award_size_id']) || isset($parameters['ctx.ctx.industry_type_id'])) {
    $year = $parameters['check_eft_issued_nyc_year_id'];
    $calyear = $parameters['calendar_fiscal_year_id'];
    if (isset($year)) {
      $parameters['ctx.ctx.fiscal_year_id'] = $year;
      $parameters['ctx.ctx.type_of_year'] = 'B';
    } else if (isset($calyear)) {
      $parameters['ctx.ctx.fiscal_year_id'] = $calyear;
      $parameters['ctx.ctx.type_of_year'] = 'C';
    }
  }
  $dtsmnid = RequestUtilities::get('dtsmnid');
  if ($dtsmnid == 20) {//From spending landing page
    $data_controller_instance = data_controller_get_operator_factory_instance();
    $parameters['agreement_id'] = $data_controller_instance->initiateHandler(NotEqualOperatorHandler::$OPERATOR__NAME, NULL);
    $parameters['contract_number'] = $data_controller_instance->initiateHandler(NotEqualOperatorHandler::$OPERATOR__NAME, NULL);
  }
  return $parameters;
}

/**
 * Handle calendar/fiscal year parameters in centralized function for spending
 *
 * @param $node
 * @param $parameters
 * @return mixed
 */
function _checkbook_project_adjust_date_spending_parameter_filters(&$node, &$parameters)
{
  $yearType = $parameters['year_type'][0];
  $year = $parameters['year_id'][0];

  if (isset($yearType)) {
    if ($yearType == 'B')
      $parameters['check_eft_issued_nyc_year_id'] = $year;
    else if ($yearType == 'C')
      $parameters['calendar_fiscal_year_id'] = $year;
  }

  unset($parameters['year_type']);
  unset($parameters['year_id']);

  return $parameters;
}

/**
 * Transaction page from M/WBE Dashboard landing page
 * Top 10 agencies widget (759) - sub and prime data - reverted in NYCCHKBK-4798
 * Top 10 Sub Vendors widget (763) - sub data
 * All Others widgets - prime data
 *
 * @param $node
 * @param $parameters
 * @return mixed
 */
function _checkbook_project_adjust_mwbe_spending_parameter_filters(&$node, &$parameters)
{

  $dtsmnid = RequestUtilities::get('dtsmnid');
  $smnid = RequestUtilities::get('smnid');
  $month = RequestUtilities::get('month');
  $nid = isset($dtsmnid) ? $dtsmnid : $smnid;
  $current_node_nid = $node->nid;

  $magid = RequestUtilities::get('magid');
  $industry = RequestUtilities::get('industry');
  if ($smnid == 764 && $magid != null && $industry != null) {
    $parameters['master_contract_industry_type_id'] = $parameters['industry_type_id'];
    unset($parameters['industry_type_id']);
  }

  if (isset($parameters['vendor_id@checkbook:contracts_spending_transactions']) || isset($parameters['document_agency_id@checkbook:contracts_spending_transactions']) || isset($parameters['award_method_id@checkbook:contracts_spending_transactions'])
    || isset($parameters['award_size_id@checkbook:contracts_spending_transactions']) || isset($parameters['industry_type_id@checkbook:contracts_spending_transactions'])
  ) {
    $year = $parameters['check_eft_issued_nyc_year_id'];
    $calyear = $parameters['calendar_fiscal_year_id'];
    if (isset($year)) {
      $parameters['fiscal_year_id@checkbook:contracts_spending_transactions'] = $year;
      $parameters['type_of_year@checkbook:contracts_spending_transactions'] = 'B';
    } else if (isset($calyear)) {
      $parameters['fiscal_year_id@checkbook:contracts_spending_transactions'] = $calyear;
      $parameters['type_of_year@checkbook:contracts_spending_transactions'] = 'C';
    }
  }

  if ($dtsmnid == 20) {//From spending landing page
    $data_controller_instance = data_controller_get_operator_factory_instance();
    $parameters['agreement_id'] = $data_controller_instance->initiateHandler(NotEqualOperatorHandler::$OPERATOR__NAME, NULL);
    $parameters['contract_number'] = $data_controller_instance->initiateHandler(NotEqualOperatorHandler::$OPERATOR__NAME, NULL);
  }


  if (isset($parameters['vendor_type'])) {
    $parameters['vendor_type'] = MappingUtil::getVendorTypeValue($parameters['vendor_type']);
  } else {
    switch ($nid) {
      case 763:
        $parameters['is_prime_or_sub'] = array('S');
        break;
      default:
        if (RequestUtilities::get('dashboard') == null) {
          //Static Amount for Citywide = Prime Only
          if ($current_node_nid == 775 || ($current_node_nid == 706 && isset($month)))
            $parameters['is_prime_or_sub'] = array('P');
          //Static Amount for Citywide from the month visualization = Prime Only
          else
            $parameters['is_prime_or_sub'] = array('P', 'S');
        } else {
          $parameters['is_prime_or_sub'] = array('P');
        }
        break;
    }
  }

  if ($dtsmnid == 764) {
    $parameters['contract_document_code'] = array('CT1', 'CTA1', 'POD', 'POC', 'PCC1', 'DO1', 'MA1', 'MMA1', 'PON1');
  }

  return $parameters;

}

/**
 * For nodes that cannot have an aggregate value, this function will aggregate any column and return the value
 * on the fly using the provided widget config.
 * @param $node
 * @param $aggregateColumn
 * @return mixed
 */
function _checkbook_project_pre_process_aggregation($node, $aggregateColumn)
{
  $params = $node->nodeAdjustedParamaterConfig;
  $ds = _update_dataset_datasource($node->widgetConfig->dataset, $node->widgetConfig->keepOriginalDatasource);
  $column = $aggregateColumn;
  $orderBy = $node->widgetConfig->orderBy;
  $startWith = isset($node->widgetConfig->startWith) ? $node->widgetConfig->startWith : 0;
  $limit = $node->widgetConfig->limit;
  $resultFormatter = new SpecialCharacterResultFormatter(NULL, new ArrayResultFormatter());
  //Handle multiple cube joins
  if (strpos($aggregateColumn, '@') !== false) {
    $col_array = explode("@", $aggregateColumn);
    $column = $col_array[0];
    $ds = $col_array[1];
  }
  $dataQueryController = data_controller_get_instance();
  $result = $dataQueryController->queryCube($ds, array($column), $params, $orderBy, $startWith, $limit, $resultFormatter);
  return $result[0][$column];
}

/**
 * Function to update the JSON configuration at runtime for advance search spending results.
 * The vendor facet will use 'LIKE' search initially, once the facet is populated and clicked the first time,
 * the vendor name is exact and an 'IN' search can be used for speed and accuracy.
 * @param $node
 */
function _checkbook_project_adjust_vendor_facet_json(&$node)
{

  if (preg_match('/fvendor/', $_GET['q'])) {
    //Vendor Facet
    if ($node->widgetConfig->filterName == 'Vendor') {
      if (isset($node->widgetConfig->urlParamMap->fvendor))
        unset($node->widgetConfig->urlParamMap->fvendor);
      if (($key = array_search('fvendor', $node->widgetConfig->cleanURLParameters)) !== false)
        unset($node->widgetConfig->cleanURLParameters[$key]);
    } else {
      if (isset($node->widgetConfig->urlParamMap->vendornm))
        unset($node->widgetConfig->urlParamMap->vendornm);
      if (($key = array_search('vendornm', $node->widgetConfig->cleanURLParameters)) !== false)
        unset($node->widgetConfig->cleanURLParameters[$key]);
    }
  }
}

/**
 * Function to build the sql for the custom payroll facet
 * @param $node
 * @return string
 */
function _checkbook_project_payroll_sql($node)
{

  $where_filters = array();
  $filter_param_value = $node->param;

  foreach ($node->widgetConfig->requestParams as $param => $value) {
    $alias = $param == 'civil_service_title' ? ' e.' : ' a.';
    $param = $param == "pay_frequency.pay_frequency" ? "pay_frequency" : $param;
    $param = $param == "gross_pay.gross_pay" ? "gross_pay" : $param;
    $param = $param == "annual_salary.annual_salary" ? "annual_salary" : $param;
    $param = $param == "overtime_pay.overtime_pay" ? "overtime_pay" : $param;

    switch ($param) {
      case "civil_service_title";
        if (isset($filter_param_value)) {
          $where_filters[] = _widget_build_sql_condition($alias . "civil_service_title", $filter_param_value);
        }
        break;
      case "gross_pay":
      case "annual_salary":
      case "overtime_pay":
        switch ($value) {
          case 1:
            $where_filters[] = $param . ' < 20000 ';
            break;
          case 2:
            $where_filters[] = $param . ' BETWEEN 21000 AND 50000 ';
            break;
          case 3:
            $where_filters[] = $param . ' BETWEEN 51000 AND 100000 ';
            break;
          case 4:
            $where_filters[] = $param . ' BETWEEN 101000 AND 250000 ';
            break;
          case 5:
            $where_filters[] = $param . ' > 250000 ';
            break;
        }
        break;
      default:
        $where_filters[] = _widget_build_sql_condition($alias . $param, $value);
        break;
    }
  }
  if (count($where_filters) > 0) {
    $where_filter = ' WHERE ' . implode(' AND ', $where_filters);
  }
  $sql = 'SELECT DISTINCT e.civil_service_title, count(*) as txcount
        FROM aggregateon_payroll_employee_agency a
        JOIN employee e on e.employee_id = a.employee_id
        ' . $where_filter . '
        GROUP BY civil_service_title
        ORDER BY txcount DESC';

  return $sql;
}

/**
 * Function to adjust custom parameters passed to data controller module
 * @param $node Node data
 * @param $parameters Widget parsed request parameters
 * @return Widget adjusted parameters
 */
function _checkbook_project_applyParameterFilters($node, $parameters)
{

  /*
      //SAMPLE DATA
      $originalRequestParams = array('reqParam1'=>'1~2','reqParam2'=>'6','reqParam3'=>'~1');

      urlParamMap = array('reqParam1'=>'dbcol1','reqParam2'=>'dbcol1','reqParam3'=>'dbcol2');
      $paramTypeConfig = array('reqParam1'=>'range','reqParam2'=>'rangeid','reqParam3'=>'range');

      //'dbcol1' will have latest value from request. This should be handled differently. Look at 'dbcol1' value.
      $parameters = array('dbcol1'=>array(6),'dbcol2'=>array(null,1));
      //This function hanldes correct value for 'dbcol1' to organise these values by directly accessging original request parameters
      $configurationTypes = array('dbcol1'=>array(
                                                  'range'=>array(1,2),
                                                  'rangeid'=>array(6))
                                                  );
  */

  $adjustedParameters = $parameters;

  //Convert configuration to array for processing
  $paramTypeConfig = isset($node->widgetConfig->paramTypeConfig) ? get_object_vars($node->widgetConfig->paramTypeConfig) : NULL;
  $defaultParamTypeConfig = isset($node->widgetConfig->defaultParamTypeConfig) ? get_object_vars($node->widgetConfig->defaultParamTypeConfig) : NULL;
  if ((empty($paramTypeConfig) && empty($defaultParamTypeConfig)) || empty($adjustedParameters)) {//Nothing to adjust
    return $adjustedParameters;
  }

  //Convert configuration to array for processing
  $urlParamMap = isset($node->widgetConfig->urlParamMap) ? get_object_vars($node->widgetConfig->urlParamMap) : array();

  $originalRequestParams = $node->widgetConfig->originalRequestParams;
  $logicalOrFacet = $node->widgetConfig->logicalOrFacet;
  $unionOrFacet = $node->widgetConfig->unionOrFacet;

  //adjust request parameter configurations
  $configurationTypes = array();
  if (!empty($paramTypeConfig)) {
    foreach ($adjustedParameters as $param => $value) {
      $flippedParamTypeConfigKeys = array_flip(array_keys($urlParamMap, $param));
      $intersectKeys = array_intersect_key($flippedParamTypeConfigKeys, $originalRequestParams);

      foreach ($intersectKeys as $intersectKey => $value) {
        if (isset($paramTypeConfig[$intersectKey]) && $urlParamMap[$intersectKey] == $param) {
          if ((!isset($logicalOrFacet) || !$logicalOrFacet) && (!isset($unionOrFacet) || !$unionOrFacet))
            $configurationTypes[$param][$paramTypeConfig[$intersectKey]] = explode("~", $originalRequestParams[$intersectKey]);
        }
      }
    }
  }

  //adjust default parameter configurations if not adjusted above.
  $defaultParameters = $node->widgetConfig->defaultParameters;
  if (isset($defaultParameters) && isset($defaultParamTypeConfig)) {
    $defaultConfigParameters = get_object_vars($defaultParameters);
    $defaultParamTypeConfigArray = get_object_vars($node->widgetConfig->defaultParamTypeConfig);
    if (!empty($defaultParamTypeConfigArray)) {
      foreach ($defaultConfigParameters as $key => $value) {
        if (!array_key_exists($key, $configurationTypes) //It might already been adjusted if passed in request URL.
          && array_key_exists($key, $defaultParamTypeConfigArray)) {
          $tempValues = explode("~", isset($originalRequestParams[$key]) ? $originalRequestParams[$key] : $value);
          $adjustedParameters[$key] = $tempValues;
          $configurationTypes[$key][$defaultParamTypeConfigArray[$key]] = $tempValues;
        }
      }
    }
  }

  if (empty($configurationTypes)) {//Nothing to adjust
    return $adjustedParameters;
  }


  $data_controller_instance = data_controller_get_operator_factory_instance();
  foreach ($adjustedParameters as $param => $value) {
    if (!array_key_exists($param, $configurationTypes)) {//Configuration for param do not exist
      continue;
    }

    $paramConfigTypes = $configurationTypes[$param];

    $conditions = NULL;

    foreach ($paramConfigTypes as $paramConfigType => $paramValues) {
      switch ($paramConfigType) {
        case "like":
          $localValue = (is_array($paramValues) && count($paramValues) > 1) ? implode('~', $paramValues) : $paramValues[0];
          $conditions[] = $data_controller_instance->initiateHandler(WildcardOperatorHandler::$OPERATOR__NAME, array($localValue, FALSE, TRUE));
          break;
        case "contains":
          $localValue = (is_array($paramValues) && count($paramValues) > 1) ? implode('~', $paramValues) : $paramValues[0];
          $localValue = _checkbook_regex_replace_pattern($localValue);
          $pattern = "(.* $localValue .*)|(.* $localValue$)|(^$localValue.*)|(.* $localValue.*)";
          $conditions[] = $data_controller_instance->initiateHandler(RegularExpressionOperatorHandler::$OPERATOR__NAME, $pattern);
          break;
        case "nlike":
          $localValue = (is_array($paramValues) && count($paramValues) > 1) ? implode('~', $paramValues) : $paramValues[0];
          $conditions[] = $data_controller_instance->initiateHandler(NotWildcardOperatorHandler::$OPERATOR__NAME, array($localValue, FALSE, TRUE));
          break;
        case "autocomplete":
          $localValue = (is_array($paramValues) && count($paramValues) > 1) ? implode('~', $paramValues) : $paramValues[0];
          $parts = explode(" ", $localValue);
          foreach ($parts as $part) {
            //$part = str_replace("(","\\(",$part);
            $part = _checkbook_regex_replace_pattern($part);
            $pattern = "(.* " . $part . ".*)|(^" . $part . ".*)";
            $conditions[] = $data_controller_instance->initiateHandler(RegularExpressionOperatorHandler::$OPERATOR__NAME, $pattern);
          }
          break;
        case "eqignorecase":
          $patterns = array();
          foreach ($paramValues as $value) {
            $patterns[] = "(^" . _checkbook_regex_replace_pattern($value) . "$)";
          }
          $conditions[] = $data_controller_instance->initiateHandler(RegularExpressionOperatorHandler::$OPERATOR__NAME, implode($patterns, "|"));
          break;
        case "neqignorecase":
          $patterns = array();
          foreach ($paramValues as $value) {
            $patterns[] = "(^" . _checkbook_regex_replace_pattern($value) . "$)";
          }
          $conditions[] = $data_controller_instance->initiateHandler(NotRegularExpressionOperatorHandler::$OPERATOR__NAME, implode($patterns, "|"));
          break;
        case "ne":
          $conditions[] = $data_controller_instance->initiateHandler(NotEqualOperatorHandler::$OPERATOR__NAME, $paramValues);
          break;
        case "gt":
          $conditions[] = $data_controller_instance->initiateHandler(GreaterThanOperatorHandler::$OPERATOR__NAME, $paramValues[0]);
          break;
        case "gte":
          $conditions[] = $data_controller_instance->initiateHandler(GreaterOrEqualOperatorHandler::$OPERATOR__NAME, $paramValues[0]);
          break;
        case "range":
          $conditions[] = $data_controller_instance->initiateHandler(RangeOperatorHandler::$OPERATOR__NAME, array($paramValues[0], $paramValues[1]));
          break;
        case "le":
          $conditions[] = $data_controller_instance->initiateHandler(LessOrEqualOperatorHandler::$OPERATOR__NAME, $paramValues[0]);
          break;
        case "rangeid":
          //Since support for for OR conditions is not present,
          //currently negating the selection criteria is only alternative.
          for ($i = 1; $i < 7; $i++) {
            switch ($i) {
              case 1:
                if (!in_array($i, $paramValues)) {
                  $conditions[] = $data_controller_instance->initiateHandler(GreaterOrEqualOperatorHandler::$OPERATOR__NAME, 1000000);
                }
                break;
              case 2:
                if (!in_array($i, $paramValues)) {
                  $conditions[] = $data_controller_instance->initiateHandler(NotInRangeOperatorHandler::$OPERATOR__NAME, array(1000000, 10000000));
                }
                break;
              case 3:
                if (!in_array($i, $paramValues)) {
                  $conditions[] = $data_controller_instance->initiateHandler(NotInRangeOperatorHandler::$OPERATOR__NAME, array(10000000.01, 25000000));
                }
                break;
              case 4:
                if (!in_array($i, $paramValues)) {
                  $conditions[] = $data_controller_instance->initiateHandler(NotInRangeOperatorHandler::$OPERATOR__NAME, array(25000000.01, 50000000));
                }
                break;
              case 5:
                if (!in_array($i, $paramValues)) {
                  $conditions[] = $data_controller_instance->initiateHandler(NotInRangeOperatorHandler::$OPERATOR__NAME, array(50000000.01, 100000000));
                }
                break;
              case 6:
                if (!in_array($i, $paramValues)) {
                  $conditions[] = $data_controller_instance->initiateHandler(LessOrEqualOperatorHandler::$OPERATOR__NAME, 100000000);
                }
                break;
            }
          }
          break;
        case "rangeid2":
          //Since support for for OR conditions is not present,
          //currently negating the selection criteria is only alternative.
          for ($i = 1; $i < 7; $i++) {
            switch ($i) {
              case 1:
                if (!in_array($i, $paramValues)) {
                  $conditions[] = $data_controller_instance->initiateHandler(GreaterOrEqualOperatorHandler::$OPERATOR__NAME, 1000000000);
                }
                break;
              case 2:
                if (!in_array($i, $paramValues)) {
                  $conditions[] = $data_controller_instance->initiateHandler(NotInRangeOperatorHandler::$OPERATOR__NAME, array(1000000000, 2000000000));
                }
                break;
              case 3:
                if (!in_array($i, $paramValues)) {
                  $conditions[] = $data_controller_instance->initiateHandler(NotInRangeOperatorHandler::$OPERATOR__NAME, array(2000000000.01, 3000000000));
                }
                break;
              case 4:
                if (!in_array($i, $paramValues)) {
                  $conditions[] = $data_controller_instance->initiateHandler(NotInRangeOperatorHandler::$OPERATOR__NAME, array(3000000000.01, 4000000000));
                }
                break;
              case 5:
                if (!in_array($i, $paramValues)) {
                  $conditions[] = $data_controller_instance->initiateHandler(NotInRangeOperatorHandler::$OPERATOR__NAME, array(4000000000.01, 5000000000));
                }
                break;
              case 6:
                if (!in_array($i, $paramValues)) {
                  $conditions[] = $data_controller_instance->initiateHandler(LessOrEqualOperatorHandler::$OPERATOR__NAME, 5000000000);
                }
                break;
            }
          }
          break;
        case "rangeid3":
          for ($i = 1; $i < 6; $i++) {
            switch ($i) {
              case 1:
                if (!in_array($i, $paramValues)) {
                  $conditions[] = $data_controller_instance->initiateHandler(GreaterOrEqualOperatorHandler::$OPERATOR__NAME, 20000);
                }
                break;
              case 2:
                if (!in_array($i, $paramValues)) {
                  $conditions[] = $data_controller_instance->initiateHandler(NotInRangeOperatorHandler::$OPERATOR__NAME, array(20000, 50000));
                }
                break;
              case 3:
                if (!in_array($i, $paramValues)) {
                  $conditions[] = $data_controller_instance->initiateHandler(NotInRangeOperatorHandler::$OPERATOR__NAME, array(50000.01, 100000));
                }
                break;
              case 4:
                if (!in_array($i, $paramValues)) {
                  $conditions[] = $data_controller_instance->initiateHandler(NotInRangeOperatorHandler::$OPERATOR__NAME, array(100000.01, 250000));
                }
                break;
              case 5:
                if (!in_array($i, $paramValues)) {
                  $conditions[] = $data_controller_instance->initiateHandler(LessOrEqualOperatorHandler::$OPERATOR__NAME, 250000);
                }
                break;
            }
          }
          break;
        default:
          break;
      }
    }

    if (empty($conditions)) {
      unset($adjustedParameters[$param]);
    } else {
      $adjustedParameters[$param] = $conditions;
    }
  }
  return $adjustedParameters;
}

/**
 * Function to get DB results
 *
 * @param $isList Indicator for list query or cube query
 * @param $datasetName Dataset Name
 * @param null $columns Required Colummns
 * @param null $parameters Parameters
 * @param null $orderBy Sort Column
 * @param int $startWith Offset
 * @param null $limit Limit
 * @param null|ResultFormatter $resultFormatter ResultFormatter
 * @return DB results
 */
function get_db_results($isList, $datasetName, $columns = NULL, $parameters = NULL, $orderBy = NULL, $startWith = 0, $limit = NULL, ResultFormatter $resultFormatter = NULL)
{
  $controller = data_controller_get_instance();

  $records = NULL;
  if ($isList) {
    $records = $controller->queryDataset($datasetName, $columns, $parameters, $orderBy, $startWith, $limit, $resultFormatter);
  } else {
    $records = $controller->queryCube($datasetName, $columns, $parameters, $orderBy, $startWith, $limit, $resultFormatter);
  }

  return $records;
}

/**
 * @param $isList Indicator for list query or cube query
 * @param $datasetName Dataset Name
 * @param $columns Required Colummns
 * @param null $parameters Parameters
 * @param null|ResultFormatter $resultFormatter ResultFormatter
 * @return integer Count
 */
function get_db_result_count($isList, $datasetName, $columns, $parameters = NULL, ResultFormatter $resultFormatter = NULL)
{
  $controller = data_controller_get_instance();

  $recordCount = NULL;
  if ($isList) {
    $recordCount = $controller->countDatasetRecords($datasetName, $parameters, $resultFormatter);
  } else {
    $recordCount = $controller->countCubeRecords($datasetName, $columns, $parameters, $resultFormatter);
  }

  return $recordCount;
}

/**
 * Function to get prepared DB query
 *
 * @param $isList Indicator for list query or cube query
 * @param $datasetName Dataset Name
 * @param null $columns Required Colummns
 * @param null $parameters Parameters
 * @param null $orderBy Sort Column
 * @param int $startWith Offset
 * @param null $limit Limit
 * @param null|ResultFormatter $resultFormatter ResultFormatter
 * @return null Query
 * @throws Exception
 */
function get_db_query($isList, $datasetName, $columns = NULL, $parameters = NULL, $orderBy = NULL, $startWith = 0, $limit = NULL, ResultFormatter $resultFormatter = NULL)
{
  $sqlQuery = NULL;

  //Since we are disabling execution mode below, need to make sure meta model is loaded.
  data_controller_get_metamodel();

  $executionMode = AbstractSQLDataSourceHandler::$STATEMENT_EXECUTION_MODE;
  try {
    AbstractSQLDataSourceHandler::$STATEMENT_EXECUTION_MODE = AbstractSQLDataSourceHandler::STATEMENT_EXECUTION_MODE__IGNORE;
    StatementLogMessageKeeper::reset();

    get_db_results($isList, $datasetName, $columns, $parameters, $orderBy, $startWith, $limit, $resultFormatter);

    $sqlQuery = $isList ? StatementLogMessageKeeper::$statements['dataset.query'][0] : StatementLogMessageKeeper::$statements['cube.query'][0];
  } catch (Exception $e) {
    AbstractSQLDataSourceHandler::$STATEMENT_EXECUTION_MODE = $executionMode;
    throw $e;
  }
  AbstractSQLDataSourceHandler::$STATEMENT_EXECUTION_MODE = $executionMode;

  return $sqlQuery;
}

/**
 * Function to get connection to data feeds DB
 *
 * @param string $db_name
 * @param string $data_source
 * @return DatabaseConnection|void DB connection
 */
function get_datafeed_connection($db_name = "datafeed", $data_source = 'checkbook')
{
  try {
    return Database::getConnection($db_name, $data_source);
  } catch (Exception $e) {
    LogHelper::log_error("Exception getting connection for datafeed. Exception is :" . $e);
    return;
  }

}

/**
 * Checks the existence of a request parameter value in the current URL.
 * @param $reqParam : Request Parameter name in the query string
 * @param $value : Value to be checked
 * @return true or false
 */
function _checkbook_project_check_parameter($reqParam, $value = null)
{
  $urlPath = drupal_get_path_alias($_GET['q']);
  $pathParams = explode('/', $urlPath);
  $index = array_search($reqParam, $pathParams);

  if ($pathParams[($index + 1)] == $value) {
    return TRUE;
  }
  return FALSE;
}

/**
 * returns the year type and year values string to be appended to the URL.
 * @param bool $applySpendingYear
 * @param bool $applyPreviousYear
 * @param bool $spendingTransactions
 * @param bool $landing_page_link
 * @return string
 */
function _checkbook_project_get_year_url_param_string($applySpendingYear = false, $applyPreviousYear = false, $spendingTransactions = false, $landing_page_link = false)
{

  $urlPath = drupal_get_path_alias($_GET['q']);
  $pathParams = explode('/', $urlPath);

  $calyrIndex = array_search("calyear", $pathParams);
  $yeartypeIndex = array_search("yeartype", $pathParams);
  $yrIndex = array_search("year", $pathParams);
  if ($spendingTransactions) {
    $yearId = $calyrIndex ? $pathParams[($calyrIndex + 1)] : $pathParams[($yrIndex + 1)];
    if ($calyrIndex || $pathParams[$yeartypeIndex + 1] == "C") {
      return "/yeartype/C/calyear/" . $yearId;
    } else {
      return "/yeartype/B/year/" . $yearId;
    }
  } else {
    if ($calyrIndex) {
      $year_param_name = ($landing_page_link) ? 'year' : 'calyear';
      $calYear = ($applyPreviousYear ? ($pathParams[($calyrIndex + 1)] - 1) : $pathParams[($calyrIndex + 1)]);
      return "/yeartype/C/" . $year_param_name . "/" . $calYear . ($applySpendingYear ? ('/scalyear/' . $calYear) : '');
    }
    if ($yeartypeIndex && $pathParams[($yeartypeIndex + 1)] == "C") {
      $calYear = ($applyPreviousYear ? ($pathParams[($yrIndex + 1)] - 1) : $pathParams[($yrIndex + 1)]);
      return "/yeartype/C/year/" . $calYear . ($applySpendingYear ? ('/syear/' . $calYear) : '');
    }

    if ($yrIndex) {
      $year = ($applyPreviousYear ? ($pathParams[($yrIndex + 1)] - 1) : $pathParams[($yrIndex + 1)]);
      return "/yeartype/B/year/" . $year . ($applySpendingYear ? ('/syear/' . $year) : '');
    }

    $curYear = CheckbookDateUtil::getCurrentFiscalYearId();
    $curYear = ($applyPreviousYear ? ($curYear - 1) : $curYear);
    return "/yeartype/B/year/" . $curYear . ($applySpendingYear ? ('/syear/' . $curYear) : '');
  }
}

/**
 * @return bool
 */
function _checkbook_current_request_is_ajax()
{
  return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
}

/**
 * returns key-value pair string is present in URL
 * detects ajax automatically
 * @param $key
 * @param null $key_alias
 * @param bool $ajaxPath
 * @return string
 */
function _checkbook_project_get_url_param_string($key, $key_alias = null, $ajaxPath = FALSE)
{
  return RequestUtilities::buildUrlFromParam("{$key}|{$key_alias}");

//    $urlPath = drupal_get_path_alias($_GET['q']);
//    if (_checkbook_current_request_is_ajax()) {
//      // remove query part
//      $urlPath = strtok($_SERVER['HTTP_REFERER'], '?');
//    }
//
//    $pathParams = explode('/', $urlPath);
//    $keyIndex = NULL;
//    foreach ($pathParams as $index => $value) {
//        if ($key == $value || ($key_alias != null && $key_alias == $value && $value != nul)) {
//            $keyIndex = $index;
//        }
//    }
//
//    $url = '';
//    if ($keyIndex) {
//        if ($key_alias == null) {
//            $url = "/$key/" . urlencode($pathParams[($keyIndex + 1)]);
//        } else {
//          $url = "/$key_alias/" . urlencode($pathParams[($keyIndex + 1)]);
//        }
//    }
////    if (_checkbook_current_request_is_ajax()) {
////      LogHelper::log_warn('_checkbook_project_get_url_param_string :: isAjax :: ' . $url);
////    }
//    return $url;

}

/**
 * Forms the url parameter string for the fvendor param.
 * This is used to populate the vendor name facet for pages with sub and prime vendors
 *
 * @param null $node
 * @return string
 */
function _checkbook_project_get_vendor_facet_url_param_string($node = null)
{
  $fvendor = null;
  $vendor = RequestUtilities::get('vendor');
  $subvendor = RequestUtilities::get('subvendor');
  $nid = $node != null ? $node->nid : null;

  switch ($nid) {
    case 720:
    case 722:
    case 721:
    case 725:
    case 726:
    case 727:
    case 728:
    case 729:
    case 781:
    case 782:
    case 783:
    case 785:
    case 786:
    case 787:
    case 788:
      if ($subvendor != null)
        $fvendor = $subvendor;
      break;
    default:
      if ($vendor != null)
        $fvendor = $vendor;
      break;
  }

  return $fvendor != null ? '/fvendor/' . $fvendor : '';

}

/**
 * return helper function to get master agreement id from chidl agreement id
 * @return array
 */
function _get_document_codes()
{

  $document_codes = get_db_results(true, 'checkbook:ref_document_code', array("document_code_id", "document_code"), null, NULL, 0, null);
  $codes = array();
  foreach ($document_codes as $row) {
    $codes[$row['document_code_id']] = $row['document_code'];
  }
  return $codes;
}


/**
 * return helper function to get master agreement id from chidl agreement id
 * @return string
 */
function _get_master_agreement_id()
{
  $agid = (RequestUtilities::get('agid') == null) ? 1 : RequestUtilities::get('agid');
  $results = get_db_results(true, 'checkbook:history_agreement', array("master_agreement_id"), array("original_agreement_id" => $agid), NULL, 0, 1);
  $magid = ($results[0]['master_agreement_id'] == null) ? 0 : $results[0]['master_agreement_id'];
  return $magid;

}

/**
 * return helper function to get master agreement id details
 * @param $magid
 * @return array
 */
function _get_master_agreement_details($magid)
{
  if (!isset($magid)) {
    return NULL;
  }
  $results = get_db_results(true, 'checkbook:history_master_agreement', array("document_code@checkbook:ref_document_code", "contract_number"), array("master_agreement_id" => $magid), NULL, 0, 1);
  //$docTypeCode = ($results[0]['document_code@checkbook:ref_document_code'] ==null ) ? '' : $results[0]['document_code@checkbook:ref_document_code'];
  return isset($results[0]) ? $results[0] : NULL;

}

/**
 * Given the parent contract number, returns the details of the contract
 * @param $parent_contract_number
 * @return null
 */
function _get_master_agreement_details_by_parent_contract_number($parent_contract_number)
{
  if (!isset($parent_contract_number)) {
    return NULL;
  }
  $results = get_db_results(true, 'checkbook:history_master_agreement', array("document_code@checkbook:ref_document_code", "master_agreement_id", "original_master_agreement_id"), array("contract_number" => $parent_contract_number, "latest_flag" => "Y"), NULL, 0, 1);
  return isset($results[0]) ? $results[0] : NULL;

}

/**
 * return helper function to get child agreement id details
 * @param $agid
 * @return array
 */
function _get_child_agreement_details($agid)
{
  if (!isset($agid)) {
    return NULL;
  }
  $results = get_db_results(true, 'checkbook:history_agreement', array("document_code@checkbook:ref_document_code", "contract_number"), array("agreement_id" => $agid), NULL, 0, 1);
  //$docTypeCode = ($results[0]['document_code@checkbook:ref_document_code'] ==null ) ? '' : $results[0]['document_code@checkbook:ref_document_code'];
  return isset($results[0]) ? $results[0] : NULL;

}

/**
 * determines whether contract is master agreement or not based on the contract number
 * @param $contnum
 * @return string
 */
function _get_contract_type($contnum)
{
  $contnum_3 = substr($contnum, 0, 3); //get first 3 characters from contract number
  $contnum_4 = substr($contnum, 0, 4); //get first 4 characters from contract number
  $fourLetterContractTypes = array('mma1', 'cta1', 'rct1', 'pcc1', 'cta2', 'mac1');
  if (in_array(strtolower($contnum_4), $fourLetterContractTypes)) {
    return $contnum_4;
  } else {
    return $contnum_3;
  }
}

/**
 * @param $contnum
 * @param $agreement_id
 *
 * @return string
 */
function _checkbook_project_get_contract_url($contnum, $agreement_id)
{
  $contract_type = _get_contract_type($contnum);
  if (strtolower($contract_type) == 'mma1' || strtolower($contract_type) == 'ma1') {
    return '/magid/' . $agreement_id . '/doctype/' . $contract_type;
  } else {
    return '/agid/' . $agreement_id . '/doctype/' . $contract_type;
  }
}

/**
 * @param $contract_number
 * @param $original_agreement_id
 * @param $doctype
 * @param null $pending_contract_number
 * @param null $version
 * @param null $linktype
 *
 * @return string
 */
function _pending_contracts_link_contract_details($contract_number, $original_agreement_id, $doctype, $pending_contract_number = null, $version = null, $linktype = null)
{
  $lower_doctype = strtolower($doctype);

  if ($original_agreement_id) {
    if (($lower_doctype == 'ma1') || ($lower_doctype == 'mma1') || ($lower_doctype == 'rct1')) {
      $url = '/panel_html/contract_transactions/magid/' . $original_agreement_id . '/doctype/' . $doctype;
    } else {
      $url = '/panel_html/contract_transactions/agid/' . $original_agreement_id . '/doctype/' . $doctype;
    }
  } else {
    $url = '/minipanels/pending_contract_transactions/contract/' . $contract_number . '/version/' . $version;
  }

  //Don't persist M/WBE parameter if there is no dashboard (this could be an advanced search parameter)
  $mwbe_parameter = RequestUtilities::get('dashboard') != null ? RequestUtilities::buildUrlFromParam('mwbe') : '';
  $url .= $mwbe_parameter;

  if ($linktype == 'bar') {
    if (preg_match("/contracts_pending_exp_landing/", $_GET['q']))
      return 'contracts_pending_exp_landing' . _checkbook_project_get_year_url_param_string() . $mwbe_parameter
        . RequestUtilities::buildUrlFromParam('dashboard') . '?expandBottomContURL=' . $url;
    if (preg_match("/contracts_pending_rev_landing/", $_GET['q']))
      return 'contracts_pending_rev_landing' . _checkbook_project_get_year_url_param_string() . $mwbe_parameter
        . RequestUtilities::buildUrlFromParam('dashboard') . '?expandBottomContURL=' . $url;
  } else {
    if (in_array('expense', _getLastRequestParamValue($_SERVER["HTTP_REFERER"], "contcat"))) {
      $url = '/contracts_pending_exp_landing' . _checkbook_project_get_year_url_param_string() . $mwbe_parameter . '?expandBottomContURL=' . $url;
      return '<a href = "' . $url . '">' . $pending_contract_number . '</a>';
    } else if (in_array('revenue', _getLastRequestParamValue($_SERVER["HTTP_REFERER"], "contcat"))) {
      $url = '/contracts_pending_rev_landing' . _checkbook_project_get_year_url_param_string() . $mwbe_parameter . '?expandBottomContURL=' . $url;
      return '<a href = "' . $url . '">' . $pending_contract_number . '</a>';
    } else if (in_array('all', _getLastRequestParamValue($_SERVER["HTTP_REFERER"], "contcat"))) {
      if (_get_contract_cat($lower_doctype) == 'revenue') {
        $url = '/contracts_pending_rev_landing' . _checkbook_project_get_year_url_param_string() . $mwbe_parameter . '?expandBottomContURL=' . $url;
        return '<a href = "' . $url . '">' . $pending_contract_number . '</a>';
      } else {
        $url = '/contracts_pending_exp_landing' . _checkbook_project_get_year_url_param_string() . $mwbe_parameter . '?expandBottomContURL=' . $url;
        return '<a href = "' . $url . '">' . $pending_contract_number . '</a>';
      }
    } else {
      return '<a class="bottomContainerReload" href = "' . $url . '">' . $pending_contract_number . '</a>';
    }
  }
}

/**
 * @param $doctype
 * @return string
 */
function _get_contract_cat($doctype)
{

  if ($doctype == 'rct1') {
    return ('revenue');
  } else {
    return ('expense');
  }

}

/**
 * Function to generate the path to save the file for data feeds
 * @return string
 * @throws Exception
 */
function _checkbook_project_prepare_data_feeds_file_output_dir()
{
  global $conf;

  $dir = variable_get('file_public_path', 'sites/default/files') . '/' . $conf['check_book']['data_feeds']['output_file_dir'];
  _checkbook_project_prepare_data_feeds_dir($dir);

  $dir .= '/' . $conf['check_book']['export_data_dir'];
  _checkbook_project_prepare_data_feeds_dir($dir);

  try {
    //delete files older than 2 hours
    _checkbook_project_clean_files($dir);
  } catch (Exception $e) {
    LogHelper::log_error($e);
  }

  return $dir;
}

/**
 * Function to generate a unique id
 * @return string
 */
function _checkbook_project_generate_uuid()
{
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
    substr($hash, 0, 8) .
    '-' .
    substr($hash, 8, 4) .
    '-' .
    substr($hash, 12, 4) .
    '-' .
    substr($hash, 16, 4) .
    '-' .
    substr($hash, 20, 12);

  return strtolower($guid);
}

/**
 * Function to generate the path to save the file for data feeds
 * @param $dir
 * @throws Exception
 */
function _checkbook_project_prepare_data_feeds_dir($dir)
{
  if (!file_prepare_directory($dir, FILE_CREATE_DIRECTORY)) {
    LogHelper::log_error("Could not prepare file output directory $dir.Should check if this directory is writable.");
    throw new Exception("Could not prepare file. Please contact Support team.");
  }
}

/**
 *
 */
function _checkbook_project_clean_files()
{
  $dir = _checkbook_export_prepareFileOutputDir();
  file_scan_directory($dir, '/.*/', array('callback' => '_checkbook_project_delete_file_if_stale'));
}

/**
 * Callback to delete files modified more than a set time ago.
 *
 * @param $path
 */
function _checkbook_project_delete_file_if_stale($path)
{
  $timeDiff = intval(floor((REQUEST_TIME - filemtime($path)) / (3600)));
  if ($timeDiff > 2) {//More than 2 hours old
    file_unmanaged_delete($path);
  }
}

/**
 * @param $sql
 * @param string $db_name
 * @param string $data_source
 * @return array|bool|mixed|void
 */
function _checkbook_project_execute_sql($sql, $db_name = "main", $data_source = 'checkbook')
{
  global $databases;

  if (function_exists('_checkbook_project_execute_sql_test')) {
//      phpunit
    return _checkbook_project_execute_sql_test($sql);
  }

  $cacheKey = '_checkbook_query_' . md5($sql . $db_name . $data_source);
  if ($result = _checkbook_dmemcache_get($cacheKey)) {
    LogHelper::log_info($sql);
    LogHelper::log_info(' PROCESSED CACHED (' . sizeof($result) . ' records)');
    return $result;
  }

  if (!isset($databases[$data_source][$db_name])) {
    LogHelper::log_error("No DB configuration found for: \$databases['$data_source']['$db_name']");
    return;
  }

  LogHelper::log_notice("Querying database \$databases['$data_source']['$db_name'] (" . $databases[$data_source][$db_name]['host'] . ')');
  LogHelper::log_notice($sql);

  $connection = get_datafeed_connection($db_name, $data_source);
  $results = null;

  try {
    $results = $connection->query($sql);
  } catch (Exception $e) {
    LogHelper::log_error("Error executing DB query for generating: " . $sql . ". Exception is: " . $e);
    return;
  }

  $output = array();
  if ($results) {
    foreach ($results as $row) {
      $output[] = get_object_vars($row);
    }
  }

  LogHelper::log_info("Processed " . sizeof($output) . " records (_checkbook_project_execute_sql)");
  if (13 > sizeof($output) || (13 > sizeof($output[0]) && 1000 > sizeof($output))) {
    _checkbook_dmemcache_set($cacheKey, $output);
  }


  return $output;
}

/**
 * @param $sql
 * @param string $data_source
 * @return array|bool|mixed
 */
function _checkbook_project_execute_sql_by_data_source($sql, $data_source = 'checkbook')
{
  return _checkbook_project_execute_sql($sql, 'main', $data_source);
}

/**
 * Function to check if records exists
 * @param $nid
 * @return bool
 */
function _checkbook_project_recordsExists($nid)
{
  static $recordExists = array();

  if (isset($recordExists[$nid])) {
    return $recordExists[$nid];
  }

  LogHelper::log_info('Loading node # ' . $nid);

  $startTime = microtime(TRUE);
  $node = node_load($nid);

  widget_config($node);

  $node->widgetConfig->getTotalDataCount = true;
  $node->widgetConfig->getData = false;

  //prepare anything we'll need before loading
  widget_prepare($node);
  //invoke widget specific prepare
  widget_invoke($node, 'widget_prepare');
  widget_data($node);

  $recordCount = $node->totalDataCount;
  $recordExist = ($recordCount > 0);
  $recordExists[$nid] = $recordExist;

  LogHelper::log_debug("Record Count time for node $nid:" . (1000 * (microtime(TRUE) - $startTime)) . " msec. Total Records: $recordCount");
  return $recordExist;
}

/**
 * Function to return record count
 * @param $node (processed node config)
 * @return int
 */
function _checkbook_project_recordCount($node)
{
  static $recordCounts = array();

  if (isset($recordCounts[$node->nid])) {
    return $recordCounts[$node->nid];
  }

  $startTime = microtime(TRUE);

  $node->widgetConfig->getTotalDataCount = true;
  $node->widgetConfig->getData = false;

  load_widget_data($node, NULL);

  $recordCount = $node->totalDataCount;
  $recordCounts[$node->nid] = $recordCount;

  LogHelper::log_info("Record Count time for node $node->nid:" . (1000 * (microtime(TRUE) - $startTime)) . " msec. Total Records: $recordCount");
  return $recordCount;
}

/**
 * @param $domain
 * @param $subTitle
 * @param null $by
 * @param null $chart
 *
 * @return string
 */
function _checkbook_project_getChartTitle($domain, $subTitle, $by = null, $chart = null)
{

  $title = NULL;

  $url = $_GET['q'];

  switch (strtolower($domain)) {
    case "contracts":
      if (preg_match("/^contracts_landing/", $url)) {
        if (RequestUtilities::get("status") == "A") {
          $title = 'Active Expense Contracts';
        } else if (RequestUtilities::get("status") == "R") {
          $title = 'Registered Expense Contracts';
        }
      } else if (preg_match("/^contracts_revenue_landing/", $url)) {
        if (RequestUtilities::get("status") == "A") {
          $title = 'Active Revenue Contracts';
        } else if (RequestUtilities::get("status") == "R") {
          $title = 'Registered Revenue Contracts';
        }
      } else if (preg_match("/^contracts_pending_rev_landing/", $url)) {
        $title = 'Pending Revenue Contracts';
      } else if (preg_match("/^contracts_pending_exp_landing/", $url)) {
        $title = 'Pending Expense Contracts';
      }
      break;
  }
  if ($chart == 'contracts') {
    return 'Top Ten ' . $title . ' by Current Amount';
  } else {
    if (!$by)
      return $title . ' ' . $subTitle;
    else
      return $subTitle . ' by ' . $title;
  }
}

/**
 * @param $string
 *
 * @return false|string
 */
function format_string_to_date($string)
{

  if ($string == null or trim($string) == '')
    return "";

  return date("m/d/Y", strtotime($string));
}

/**
 * returns last paramater value from URL($_REQUEST['q'])
 *
 * @param null $url
 * @param null $param
 * @return array
 */
function _getLastRequestParamValue($url = null, $param = null)
{
  $value = NULL;
  $skip_next = false;
  if ($url == null) $url = $_GET['q'];
  $urlPath = drupal_get_path_alias($url);
  $pathParams = explode('/', $urlPath);

  $replacedPathParams = array();
  $slipParams = [
    'mwbe',
    'dashboard',
    'status',
    'yeartype',
    'year',
  ];
  foreach ($pathParams as $key) {
    if (in_array($key, $slipParams)) {
      $skip_next = true;
      continue;
    }
    if ($skip_next) {
      $skip_next = false;
      continue;
    }
    $replacedPathParams[] = $key;
  }

  if ($param == null) {
    $paramName = $replacedPathParams[count($replacedPathParams) - 2];
  } else
    $paramName = $param;
  $index = array_search($paramName, $replacedPathParams);

  if ($index != FALSE) {
    $value = filter_xss($replacedPathParams[($index + 1)]);
  }
  $reqParams = array($paramName => $value);
  return $reqParams;
}


/**
 * @param $string
 *
 * @return mixed
 */
function _checkbook_regex_replace_pattern($string)
{
  $search = [
    '.',
    '^',
    '$',
    '*',
    '+',
    '(',
    ')',
    '[',
    ']',
    '{',
    '}',
  ];
  $replace = [
    '\.',
    '\^',
    '\$',
    '\*',
    '\+',
    '\(',
    '\)',
    '\[',
    '\]',
    '\{',
    '\}',
  ];
  $string = str_replace($search, $replace, $string);


  return $string;
}


/**
 * @return bool
 */
function _checkbook_project_ma1_mma1_exist()
{
  $url = $_GET['q'];
  $urlPath = drupal_get_path_alias($url);
  $pathParams = explode('/', $urlPath);
  $index = array_search('contnum', $pathParams);

  if ($index !== false) {
    $value = $pathParams[($index + 1)];
    $doc_type1 = strtolower(substr($value, 0, 3));
    $doc_type2 = strtolower(substr($value, 0, 4));
    if ('ma1' == $doc_type1 || 'mma1' == $doc_type2) {
      return true;
    }
  }

  return false;
}

/**
 * @param $string
 *
 * @return int
 */
function _get_num_from_string($string)
{
  return (int)preg_replace('/[^\-\d]*(\-?\d*).*/', '$1', $string);
}

/**
 * @param $year
 * @return int
 */
function _checkbook_get_year_id($year)
{
  return CheckbookDateUtil::year2yearId($year);
}

/**
 * @return array
 */
function _checkbook_year_list()
{
  $year = CheckbookDateUtil::getCurrentFiscalYear();
  $return = [];
  $min = $year - 10;
  while ($year > $min) {
    $return[] = [
      'year_value' => $year,
      'year_id' => CheckbookDateUtil::year2yearId($year)
    ];
    $year--;
  }
  return $return;
}


/**
 * @return array
 */
function _checkbook_max_data_year($data_source = Datasource::CITYWIDE)
{
  return [
    'year_value' => CheckbookDateUtil::getCurrentFiscalYear($data_source),
    'year_id' => CheckbookDateUtil::getCurrentFiscalYearId($data_source),
    'cal_year_value' => CheckbookDateUtil::getCurrentCalendarYear(),
    'cal_year_id' => CheckbookDateUtil::getCurrentCalendarYearId()
  ];
}

/*
 * Will add mwbe, subvendor and datasource parameters to url.  Precedence ,$source > $overidden_params > requestparam
 */

/**
 * @param null $source
 * @param array $overidden_params
 * @param bool $top_nav
 *
 * @return string
 */
function _checkbook_append_url_params($source = null, $overidden_params = array(), $top_nav = false)
{
  $datasource = (isset($overidden_params['datasource'])) ? $overidden_params['datasource'] : RequestUtilities::get('datasource');
  $mwbe = (isset($overidden_params['mwbe'])) ? $overidden_params['mwbe'] : RequestUtilities::get('mwbe');
  //$svendor = (isset($overidden_params['subvendor'])) ? $overidden_params['subvendor'] : RequestUtilities::getRequestParamValue('subvendor');
  $dashboard = (isset($overidden_params['dashboard'])) ? $overidden_params['dashboard'] : RequestUtilities::get('dashboard');

  $url = "";
  if (isset($datasource)) {
    $url = "/datasource/" . $datasource;
  } else {
    $current_url = explode('/', $_SERVER['HTTP_REFERER']);
    if (($current_url[3] == 'contract' && ($current_url[4] == 'search' || $current_url[4] == 'all') && $current_url[5] == 'transactions')) {
      $advanced_search = true;
    }
    if (!$advanced_search) {
      if ($source) {
        $source = explode("/", $source);
        if (!in_array("mwbe", $source)) {
          $url = isset($mwbe) ? "/mwbe/" . $mwbe : "";
        }
        if (!in_array("dashboard", $source)) {
          $url = isset($dashboard) ? "/dashboard/" . $dashboard : "";
        }
      } else {
        if (!$top_nav || (isset($mwbe) && RequestUtilities::get('vendor') > 0 && RequestUtilities::get('dashboard') != "ms")) {
          $url = isset($mwbe) ? "/mwbe/" . $mwbe : "";
          $url .= isset($dashboard) ? "/dashboard/" . $dashboard : "";
        }
      }
    }
  }
  return $url;
}

/**
 * @return bool
 */
function _checkbook_check_isEDCPage()
{
  if ('edc' == _checkbook_get_datasource()) {
    return true;
  }
  $database = RequestUtilities::get('datasource');
  if (isset($database) && $database == 'checkbook_oge') {
    return true;
  } else {
    return false;
  }
}

/**
 * @return bool for NYCHA page
 */
function _checkbook_check_isNYCHAPage()
{
  if ('nycha' == _checkbook_get_datasource()) {
    return true;
  }
  $database = RequestUtilities::get('datasource');
  if (isset($database) && $database == 'checkbook_nycha') {
    return true;
  } else {
    return false;
  }
}

/** Returns Boolean if this is an mwbe page */
function _checkbook_check_is_mwbe_page()
{
  return null != RequestUtilities::get('mwbe');
}

/** Returns Boolean if this is a sub vendor page */
function _checkbook_check_is_sub_vendor_page()
{
  return preg_match('"/dashboard\/dsv"', $_GET['q']);
}

/**
 * Returns Boolean describing if this is a "Sub Vendor Level" page (sub vendor page for a particular sub vendor)
 *
 * "subvendor/all" -> returns false,
 * "subvendor/{sub_vendor_id}" -> returns true,
 *
 * @return int
 */
function _checkbook_check_is_sub_vendor_level_page()
{
  return _checkbook_check_is_sub_vendor_page() && RequestUtilities::get("subvendor") != "all";
}

/**
 * Returns Boolean describing if this is a "Sub Vendor Ethnicity Page"
 *
 * @return int
 */
function _checkbook_check_is_sub_vendor_ethnicity_page()
{
  return _checkbook_check_is_sub_vendor_page() && _checkbook_check_is_mwbe_page();
}

/**
 * @param $cont_num
 *
 * @return bool
 */
function _checkbook_is_oge_contract($cont_num)
{
  $rows = _checkbook_project_querydataset('checkbook_oge:oge_contract', array('fms_contract_number'), array('fms_contract_number' => $cont_num));
  if ($rows > 0) {
    return true;
  } else {
    return false;
  }
}

/**
 * @param $cont_num
 *
 * @return bool
 */
function _checkbook_is_oge_parent_contract($cont_num)
{
  $rows = _checkbook_project_querydataset('checkbook_oge:agreement_snapshot', array('master_contract_number'), array('master_contract_number' => $cont_num));
  if ($rows > 0) {
    return true;
  } else {
    return false;
  }
}


/**
 * @param $vendor_id
 *
 * @return string
 */
function _checkbook_get_oge_agency_id($vendor_id)
{
  $vendors = _checkbook_project_querydataset('checkbook:vendor', array('vendor_id', 'legal_name'), array('vendor_id' => $vendor_id));
  $vendor_name = $vendors[0]['legal_name'];
  $agencies = _checkbook_project_querydataset('checkbook_oge:agency', array('agency_id', 'agency_name'), array('agency_name' => $vendor_name, 'is_display' => 'Y', 'is_oge_agency' => 'Y'));
  return htmlentities($agencies[0]['agency_id']);
}

/**
 * Return citywide/edc/nycha current datasource
 * @return string
 */
function _checkbook_get_datasource()
{
  $url = '//' . current_path();

  if (strpos($url, '/nycha')
    || strpos($url, 'agency/162')) {
    return 'nycha';
  }

  if (strpos($url, 'agency/9000')
    || strpos($url, '/edc')) {
    return 'edc';
  }

  return 'citywide';
}

/**
 * @param $agency_id
 *
 * @return string
 */
function _checkbook_get_toggle_vendor_id($agency_id)
{
  $agencies = _checkbook_project_querydataset('checkbook_oge:agency', array('agency_id', 'agency_name'), array('agency_id' => $agency_id, 'is_oge_agency' => 'Y'));
  $agency_name = $agencies[0]['agency_name'];
  $vendors = _checkbook_project_querydataset('checkbook:vendor', array('vendor_id', 'legal_name'), array('legal_name' => $agency_name));
  $vendor_id = htmlentities($vendors[0]['vendor_id'] - 100000);
  return $vendor_id;
}

/**
 * @return array|null
 */
function _get_toggle_view_links()
{
  $q = request_uri();
  $array_q = explode('/', $q);
  $array_q[1] = ($array_q[1] == 'contracts_landing') ? $array_q[1] . '/status/A' : $array_q[1];
  $year_string = _checkbook_project_get_year_url_param_string();
  $link = $oge_agency_id = $vendor_id = null;

  if (!_checkbook_check_isEDCPage() && RequestUtilities::get('vendor')) {
    $oge_agency_id = _checkbook_get_oge_agency_id(RequestUtilities::get('vendor'));
    if ($oge_agency_id > 0)
      $link = '/' . $array_q[1] . $year_string . '/datasource/checkbook_oge/agency/' . $oge_agency_id;

    if (strlen($link) > 0)
      return array('vendor', $link);
  } else if (_checkbook_check_isEDCPage()) {
    $vendor_id = _checkbook_get_toggle_vendor_id(RequestUtilities::get('agency'));
    if ($vendor_id > 0)
      $link = '/' . $array_q[1] . $year_string . '/vendor/' . $vendor_id;
    if (strlen($link) > 0)
      return array('agency', $link);
  } else {
    return null;
  }
  return null;
}


/**
 * @param $agreement_id
 *
 * @return bool
 */
function _is_mwbe_vendor($agreement_id)
{
  if (!($agreement_id = intval($agreement_id))) {
    return false;
  }
  $query1 = "SELECT (CASE WHEN fa.minority_type_id IN (2,3,4,5,9)  THEN 'Yes' ELSE 'NO' END) AS mwbe_vendor,
	                 (CASE WHEN fa.minority_type_id in (4,5) then 'Asian American' ELSE fa.minority_type_name END)AS ethnicity
              FROM {agreement_snapshot} fa
	          WHERE fa.latest_flag = 'Y' and fa.original_agreement_id = " . $agreement_id . " limit 1";
  $results1 = _checkbook_project_execute_sql_by_data_source($query1, _get_default_datasource());
  $res = $results1;
  if ($res[0]['mwbe_vendor'] == 'Yes') {
    return true;
  }
  return false;
}


/**
 * @param $node
 */
function _getAnnualSalariedEmployeeCount($node)
{
  $agency_id = RequestUtilities::get('agency');
  $yeartype = RequestUtilities::get('yeartype');
  $yearid = RequestUtilities::get('year');

  $where_filter = " WHERE type_of_year = '" . $yeartype . "' AND type_of_employment = 'Salaried' AND fiscal_year_id = " . $yearid;
  if ($agency_id) {
    $where_filter .= " AND agency_id = " . $agency_id;
  }

  $sql = "SELECT COUNT(DISTINCT employee_number) AS record_count
            FROM {aggregateon_payroll_employee_agency}
            " . $where_filter;

  $result = _checkbook_project_execute_sql_by_data_source($sql, _get_default_datasource());
  $node->PayrollTotalDataCount = (int)$result[0]['record_count'];
}


/**
 * @return bool
 */
function isAdvancedSearchResultsPage()
{
  $current_url = $_SERVER['HTTP_REFERER'];
  if (preg_match("/contract\/search\/transactions/", $current_url)) {
    return true;
  } else {
    $current_url = request_uri();
    if (preg_match("/contract\/search\/transactions/", $current_url)) {
      return true;
    }
  }
  return false;
}

/**
 * @param null $attributes
 * @return array|void
 */
function _get_contract_includes_subvendors_data($attributes = NULL)
{
  if (function_exists('_get_contract_includes_subvendors_data_test')) {
//      phpunit
    return _get_contract_includes_subvendors_data_test($attributes);
  }
  try {
    $dataController = data_controller_get_instance();
    $values = $dataController->queryDataset('checkbook:ref_subcontract_status',
      array('scntrc_status', 'scntrc_status_name', 'display_flag'),
      NULL, 'sort_order', 0, 10, NULL);

    if ($attributes) {
      $statuses = array('title' => 'Select Status');
    } else {
      $statuses = array('Select Status');
    }
    foreach ($values as $value) {
      if ($attributes) {
        $statuses[$value['scntrc_status']] = array('title' => $value['scntrc_status_name']);
      } else {
        if ($value['display_flag'] == 1) {
          if (strlen($value['scntrc_status_name']) > 20)
            $statuses[$value['scntrc_status']] = substr($value['scntrc_status_name'], 0, 20) . '...';
          else
            $statuses[$value['scntrc_status']] = $value['scntrc_status_name'];
        }
      }
    }
    return $statuses;
  } catch (Exception $e) {
    log_error("Error getting data from the controller: \n" . $e->getMessage());
    return;
  }
}


/**
 * @param $vendor_id
 * @param bool $prime
 *
 * @return string
 */
function _checkbook_vendor_link($vendor_id, $prime = FALSE)
{

  $status = '/status/' . (RequestUtilities::get("status") ?: 'A');

  if (RequestUtilities::get("doctype") == "RCT1") {
    $page = '/contracts_revenue_landing';
    $is_mwbe = _is_mwbe_vendor(RequestUtilities::get("magid"));
    if ($is_mwbe) {
      $mwbe = '/dashboard/mp';

    } else {
      $mwbe = '';
    }


  } else {

    $page = '/contracts_landing';
    $url = '  ' . RequestUtil::getCurrentPageUrl();

    if (stripos($url, 'contracts_pending_exp_landing') && $status === '/status/A') {
      $page = '/contracts_landing';
    } elseif (stripos($url, 'contracts_pending_exp_landing')) {
      $page = '/contracts_pending_exp_landing';
    } elseif (stripos($url, 'contracts_pending_rev_landing')) {
      $page = '/contracts_pending_rev_landing';
    } elseif (stripos($url, 'contracts_revenue_landing')) {
      $page = '/contracts_revenue_landing';
    }


    if (_is_mwbe_vendor(RequestUtilities::get("agid")) || _is_mwbe_vendor(RequestUtilities::get("magid")) || PrimeVendorService::getLatestMinorityTypeByYear($vendor_id, CheckbookDateUtil::getCurrentFiscalYearId(), 'B')) {
      $mwbe = RequestUtilities::_appendMWBESubVendorDatasourceUrlParams();
    } else {
      $mwbe = '';
    }

  }

  $vendor_link = $page . $status . '/year/' . CheckbookDateUtil::getCurrentFiscalYearId() . '/yeartype/B/vendor/' . $vendor_id . $mwbe . '?expandBottomCont=true';

  return $vendor_link;
}


/**
 * @param $agency_id
 *
 * @param bool $prime
 * @return string
 */
function _checkbook_agency_link($agency_id, $prime = FALSE)
{
  $issubvendor = 'false';

  $status = '/status/' . (RequestUtilities::get("status") ?: 'A');

  $datasource = '';
  if ('checkbook_oge' == RequestUtilities::get("datasource")) {
    $datasource = "/datasource/checkbook_oge";
  }

  if (RequestUtilities::get("doctype") == "RCT1") {
    $is_mwbe = _is_mwbe_vendor(RequestUtilities::get("magid"));
    if ($is_mwbe) {
      $mwbe = RequestUtilities::_appendMWBESubVendorDatasourceUrlParams();
    } else {
      $mwbe = '';
    }
    $agency_link = '/contracts_revenue_landing' . $status . $mwbe . '/year/' . CheckbookDateUtil::getCurrentFiscalYearId() . $datasource . '/yeartype/B/agency/'
      . $agency_id . '?expandBottomCont=true';
  } else {
    $dashboard = '/contracts_landing';
    $url = '  ' . RequestUtil::getCurrentPageUrl();
    if (stripos($url, 'contracts_pending_exp_landing') && $status === '/status/A') {
      $dashboard = '/contracts_landing';
    } elseif (stripos($url, 'contracts_pending_exp_landing')) {
      $dashboard = '/contracts_pending_exp_landing';
    } elseif (stripos($url, 'contracts_pending_rev_landing')) {
      $dashboard = '/contracts_pending_rev_landing';
    } elseif (stripos($url, 'contracts_revenue_landing')) {
      $dashboard = '/contracts_revenue_landing';
    } else if (stripos($url, 'bottom_slider/sub_vendor')) {
      $dashboard = '/contracts_landing/bottom_slider/sub_vendor';
    }
    if (stripos($url, 'dashboard/ss')) {
      $issubvendor = true;
    }

    if (_is_mwbe_vendor(RequestUtilities::get("magid")) || _is_mwbe_vendor(RequestUtilities::get("agid")) || $issubvendor) {
      $mwbe = RequestUtilities::_appendMWBESubVendorDatasourceUrlParams();
    }

    $agency_link = $dashboard . $status . '/year/' . CheckbookDateUtil::getCurrentFiscalYearId() . $datasource . '/yeartype/B/agency/'
      . $agency_id . $mwbe . '?expandBottomCont=true';
  }
  return $agency_link;
}

/**
 * @param $key
 * @param string $bin
 * @param null $mc
 * @return bool|mixed
 */
function _checkbook_dmemcache_get($key, $bin = 'cache', $mc = NULL)
{
//    MOCK CACHE FOR PHPUNIT
  if (defined('PHPUNIT_RUNNING') && PHPUNIT_RUNNING) {
    global $phpunit_cache;
    return !empty($phpunit_cache[$key]) ? $phpunit_cache[$key] : false;
  }

//    manually disabled, ex. during etl status
  if (defined('CHECKBOOK_NO_DB_CACHE') && CHECKBOOK_NO_DB_CACHE) {
    return false;
  }

//    NO CACHE FOR ADMINS
  if (user_is_logged_in()) {
    return false;
  }
  if (function_exists('dmemcache_get')) {
    $return = dmemcache_get($key, $bin, $mc);
    if ($return) {
      LogHelper::log_info($key . ' CACHE HIT! ' . md5(json_encode($return)));
    }
    return $return;
  }
  LogHelper::log_warn('could not find dmemcache_get() function, try enabling memcache');
  return false;
}

/**
 * @param $key
 * @param $value
 * @param int $exp
 * @param string $bin
 * @param null $mc
 * @return bool
 */
function _checkbook_dmemcache_set($key, $value, $exp = 54000, $bin = 'cache', $mc = NULL)
{
  if (defined('PHPUNIT_RUNNING') && PHPUNIT_RUNNING) {
    global $phpunit_cache;
    $phpunit_cache[$key] = $value;
    return true;
  }
  if (user_is_logged_in() || !$value) {
    return false;
  }
  if (function_exists('dmemcache_set')) {
    $return = dmemcache_set($key, $value, $exp, $bin, $mc);
    if ($return) {
      LogHelper::log_info($key . ' CACHED NOW! ' . md5(json_encode($value)));
    }
    return $return;
  }
  LogHelper::log_warn('could not find dmemcache_set() function, try enabling memcache');
  return false;
}

/**
 * @param string $database
 * @return mixed|string
 */
function _checkbook_psql_command($database = 'checkbook')
{
  global $conf, $databases;

  // extra check empty db
  if (!$database) {
    $database = 'checkbook';
  }

  $psql_path = '/usr/bin/psql';  // default
  if (isset($conf['check_book']['psql_path'])) {
    $psql_path = $conf['check_book']['psql_path'];
  }
  if (!isset($databases[$database])) {
    LogHelper::log_warn('$databases[' . $database . '] not found');
    return '';
  }

  $command = 'PGPASSWORD=%PWD% %PSQL% -h %HOST% -U %USER% %DATABASE% ';
  $search = ['%PWD%', '%PSQL%', '%HOST%', '%USER%', '%DATABASE%'];
  $replace = [
    $databases[$database]['main']['password'],
    $psql_path,
    $databases[$database]['main']['host'],
    $databases[$database]['main']['username'],
    $databases[$database]['main']['database'],
  ];
  $command = str_replace($search, $replace, $command);

  return $command;
}

/**
 * Strips a value from brackets using RegEx else returns zero.
 * @param string $input
 * @return int|string
 */
function emptyToZero($input)
{
  if (is_numeric($input)) {
    return $input;
  }
  $p = "/.*?(\\[.*?\\])/is";
  $matches = array();
  if ($input) {
    preg_match($p, $input, $matches);
    $output = trim($matches[1], '[ ]');
  } else {
    $output = 0;
  }
  return $output;
}

/**
 * @param $input
 * @return int|string
 */
function _emptyToZero($input){
  return emptyToZero($input);
}
