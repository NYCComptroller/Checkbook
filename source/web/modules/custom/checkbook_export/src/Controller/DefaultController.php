<?php
/**
 * This file is part of the Checkbook NYC financial transparency software.
 *
 * Copyright (c) 2012 – 2023 New York City
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Drupal\checkbook_export\Controller;

use Drupal\checkbook_custom_breadcrumbs\TrendPageTitle;
use Drupal\Core\Controller\ControllerBase;
use Drupal\checkbook_log\LogHelper;
use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;
use Drupal\checkbook_project\WidgetUtilities\WidgetUtil;
use Drupal\Core\TypedData\Plugin\DataType\BinaryData;
use Drupal\data_controller\Common\Object\Comparator\SortHelper;
use Exception;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Drupal\data_controller\Common\Object\Comparator\Handler\PropertyBasedComparator_DefaultSortingConfiguration;
use Drupal\widget_config\Utilities\Trends\TrendCsvUtil;

/**
 * Default controller for the checkbook_export module.
 */
class DefaultController extends ControllerBase {

  public function checkbook_export_form() {
    $maxPages  = \Drupal::request()->query->get('maxPages');
    $totalRecords = \Drupal::request()->query->get('iRecordsTotal');
    $displayRecords = \Drupal::request()->query->get('iRecordsDisplay');

    $render['#theme'] = 'checkbook_export_form';
    $render['#displayRecords'] = $displayRecords;
    $render['#maxPages'] = $maxPages;
    $render['#totalRecords'] = $totalRecords;

    return $render;
  }
  /**
  * to handle export data
  */
  public function _checkbook_export_transactions(){
    if ($fileName = _checkbook_export_check_cached()) {
      return _checkbook_export_download_file($fileName);
     // return;
    }

    $exportMemoryRecordLimit = 0;
    $node = _checkbook_export_get_node_config();
    $limit = $node->widgetConfig->limit;

    //TODO - see if we have to check count again
    $node->widgetConfig->getTotalDataCount = false;
    $node->widgetConfig->getData = true;

    $isList = $node->widgetConfig->useList;

    //Get Export column Configuration
    $exportConfigNid = $node->widgetConfig->exportConfigNid ?? null;
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
        if(!isset($column->datasource) || (($column->datasource == RequestUtilities::get('datasource')))){
            if(check_node_flag_visibilty($column->visibility_flag, $node)) {
              $columns[] = $column;
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
            $columnsConfig[] = $column;
        }
    }
    if($isList){//Selecting only required columns.
        $node->widgetConfig->columns = $columnsConfig;
    }

    //load data
    widget_data($node);

    if(($limit > $exportMemoryRecordLimit) || isset($exportConfigNid)){
        try{
            $fileName = _checkbook_export_generateExportFile($node, $headers, $exportConfigNode);
            return _checkbook_export_download_file($fileName);
        }catch (Exception $e){
            LogHelper::log_error("Error generating export file:" . $e->getMessage());
            $fileheaders = array(
              'Content-Type' => 'text/plain',
              'Content-Disposition' => 'attachment; filename=TransactionsData.Error.txt',
              'Pragma' => 'cache',
              'Expires' => '-1',
              "Content-Length" => strlen($headers)
            );
            return new Response("There was an error generating file.", 200, $fileheaders, true);
        }
    } else {
      return new Response('Could not find data.');
    }
  }

  /** Function to render export data for grid view
   * @throws Exception
   */
  public function _checkbook_export_grid_transactions($nodeId){
    $node = _widget_node_load_file($nodeId);

    if($node == null ){
      $node = widget_load($nodeId);
    }
    widget_config($node);

    if(isset($node->widgetConfig->gridConfig->adjustWidgetConfig)){
        eval($node->widgetConfig->gridConfig->adjustWidgetConfig);
    }
    //@ToDo: Was not working if only set q with Drupal::request. So setting with request here, then remove and add $_GET['q'] again.
    \Drupal::request()->query->set('q', urldecode(\Drupal::request()->query->get('refURL')));
    widget_prepare($node);

    widget_invoke($node, 'widget_prepare');

    $sortColumn = $node->widgetConfig->gridConfig->table_columns[$_REQUEST['iSortCol_0']]->column;

    if(isset($node->widgetConfig->gridConfig->table_columns[$_REQUEST['iSortCol_0' ]]->sortColumn)){
        $sortColumn = $node->widgetConfig->gridConfig->table_columns[$_REQUEST['iSortCol_0']]->sortColumn;
    }
    $isAsc = ($_REQUEST['sSortDir_0'] == 'asc');


    if(isset($node->nid) && !is_object($node->nid)) {
      LogHelper::log_notice("Exporting grid by node id # ".$node->nid);
    } else {
      LogHelper::log_notice("Exporting grid by node id # ".$node->nid);
    }
    widget_data($node);
    if(isset($node->widgetConfig->widgetUpdateJSONConfig)){
      eval($node->widgetConfig->widgetUpdateJSONConfig);
    }

    $columns=[];
    //remove blank columns
    $cols = $node->widgetConfig->gridConfig->table_columns;
    if(is_countable($cols)){
    for ($i = 0; $i < count($cols); $i++) {
      if ($cols[$i]->column) {
        $columns[$i] = $cols[$i];
      }
    }
    }

    $data = NULL;

    $i=0;
    foreach($columns as $column){

        $label = (isset($column->labelAlias))? (WidgetUtil::getLabel($column->labelAlias)) : $column->colLabel;
        if(isset($column->eval)) {
            $label = eval("return $label;");
        }else{
          $label = ucwords($label);
        }
        $label = str_replace('<br/>',' ', $label);
        if($i == 0)
            $data .= $label;
        else{
            $data .= ',"'.$label.'"';
        }
        $i++;
    }

    if(isset($node->widgetConfig->gridConfig->data)){
      foreach($node->widgetConfig->gridConfig->table_columns as $column){
        $colTitle = str_replace('<br/>',' ', WidgetUtil::$labels[strtolower($column->labelAlias)]);
        $colTitle = strlen($colTitle)>0 ? $colTitle : $column->labelAlias;
        $data .= $colTitle . ",";
      }
      //Remove comma at the end of header to avoid empty column
      $data = rtrim($data, ',');
      $data .= "\r\n";
      $sort_index = $_REQUEST['iSortCol_0'];
      $sort_index_format = $node->widgetConfig->gridConfig->table_columns[$sort_index]->formatType;
      $sort_for_export = isset($node->widgetConfig->gridConfig->table_columns[$sort_index]->sortExport) ? $node->widgetConfig->gridConfig->table_columns[$sort_index]->sortExport : true;

      foreach($node->widgetConfig->gridConfig->data as &$dataRecord){
        $dataRecord['sort_column'] = $sort_index;
        if(isset($sort_index_format)){
          $dataRecord['formatType'] = $sort_index_format;
        }
      }
      if($sort_for_export) {
        $order = $isAsc ? 'asc' : 'desc';
        _checkbook_export_grid_sort($node, $order);
      }

      foreach ($node->widgetConfig->gridConfig->data as $data_row) {
        $index = 0;
        while($index <count($node->widgetConfig->gridConfig->table_columns)){
          $columnValue = html_entity_decode($data_row[$index]);
          $columnValue = str_replace('"',chr(34).'"',$columnValue);
          if($node->widgetConfig->gridConfig->table_columns[$index]->formatType =="amount"){
            $columnValue = ($columnValue == null) ? 0:$columnValue;
          }
          $data .= (($index == 0) ? ('"'.$columnValue.'"') : (',"'.$columnValue.'"'));
          $index +=1;
        }
        $data .= "\r\n";
      }
    }else if(is_array($node->data) and count($node->data) > 0){
        $dataRecords = $node->data;
        //Custom column for ordering
        $i=1;
        foreach($dataRecords as &$dataRecord){
          $dataRecord['sort_order'] = $i;
          $i++;
        }
      SortHelper::sort_records($dataRecords, new PropertyBasedComparator_DefaultSortingConfiguration($sortColumn,$isAsc));
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


    $response = new Response();
    $response->headers->set('Content-Type','text/csv');
    $response->headers->set('Content-Disposition','attachment; filename=TransactionsData.csv');
    $response->headers->set('Content-Length',strlen($data));
    $response->headers->set('Expires','cache');
    $response->headers->set('Expires','-1');

    $response->setContent($data);

    return $response;
  }

  public function _checkbook_export_trends($nodeId){
    $node = _widget_node_load_file($nodeId);
    if($node == null ){
      $node = widget_load($nodeId);
    }
    widget_config($node);
    widget_prepare($node);
    widget_invoke($node, 'widget_prepare');
    widget_data($node);
    $trendBreadCrumb = TrendPageTitle::getBreadCrumbTitle($nodeId);
    $filename = trim(preg_replace('/[^A-Za-z]+/', '_', $trendBreadCrumb['trend_type'] . '_' . $trendBreadCrumb['trend_name'])).".csv";
    $data = TrendCsvUtil::getTrendsData($node);
    $response = new Response();
    $response->headers->set('Content-Type','text/csv');
    $response->headers->set('Content-Disposition','attachment; filename='.$filename);
    $response->headers->set('Content-Length',strlen($data));
    $response->headers->set('Expires','cache');
    $response->headers->set('Expires','-1');

    $response->setContent($data);

    return $response;

  }

}
