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

namespace Drupal\checkbook_faceted_search\Utilities;
use Drupal\checkbook_infrastructure_layer\Constants\Common\Datasource;
use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;
use Drupal\checkbook_project\ContractsUtilities\ContractUtil;
use Drupal\checkbook_project\MwbeUtilities\MappingUtil;
use Drupal\checkbook_project\MwbeUtilities\VendorType;
use Drupal\checkbook_project\PayrollUtilities\PayrollType;


class FacetUtil
{
  /**
   * Function to handle Budget Code Name paramenter
   * Adjust Budget Code Name paramenter to do wildcard search on 'Budget Code Name' or 'Budget Code' columns
   * @param $node
   * @param $parameters
   * @return mixed
   */
  public static function adjustFacetData(&$variables, &$node)
  {
    $urlParameter = $node->widgetConfig->urlParameterName;
    // Update Agency Name
    if (strtolower($variables['filter_name']) == 'agency' || strtolower($variables['filter_name']) == 'citywide agency') {
      if (Datasource::isOGE() || Datasource::isNYCHA() || ($variables['datasource'] == DATASOURCE::OGE)) {
        $variables['filter_name'] = 'Other Government Entity';
      } else {
        $variables['filter_name'] = 'Citywide Agency';
      }
    }
    $datasource = RequestUtilities::get('datasource');
    if(strtolower(($variables['filter_name']) == 'vendor') || strtolower(($variables['filter_name'])) == 'payee' ){

      if(Datasource::isOGE() || $datasource == DATASOURCE::OGE){
        $variables['filter_name'] = 'Prime Vendor';
      }
    }

    if($node->widgetConfig->filterName == 'Expense Type'){

      if(Datasource::isOGE() || $datasource == 'checkbook_oge'){
        $variables['filter_name'] = 'Spending Category';
      }
    }

    // Update Modified Expense Budget
    if ($node->widgetConfig->filterName == 'Modified Expense Budget') {
      $showAllRecords = $node->widgetConfig->showAllRecords ?? false;
      if (!$showAllRecords) {
        $params = explode('~', RequestUtilities::get($variables['urlParameter']));
        if ($params[0]) {
          $variables['unchecked'] = null;
        }
      }
    }

    $logicalOrFacet = $node->widgetConfig->logicalOrFacet ?? null;
    if(isset($logicalOrFacet) && $logicalOrFacet) {
      foreach($variables['unchecked'] as $key => $value){
        //Remove N/A from facet
        if($value[1] == null) {
          unset($variables['unchecked'][$key]);
        }
      }
      foreach($variables['checked'] as $key=>$value){
        //Remove N/A from facet
        if($value[1] == null) {
          unset($variables['checked'][$key]);
        }
      }
    }

    //Amount Filter
    if($node->widgetConfig->filterName == 'Amount') {
      $showAllRecords = $node->widgetConfig->showAllRecords ?? false;
      if(!$showAllRecords) {
        $params = explode('~', RequestUtilities::get($urlParameter));
        if($params[0]) {
          $variables['unchecked'] = null;
        }
      }
    }

    //Payroll Range Filter
    $is_payroll_range_filter =
      ($node->widgetConfig->filterName == 'Gross Pay YTD') ||
      ($node->widgetConfig->filterName == 'Annual Salary') ||
      ($node->widgetConfig->filterName == 'Overtime Payment');
    if($is_payroll_range_filter) {
      $showAllRecords = $node->widgetConfig->showAllRecords ?? false;
      if(!$showAllRecords) {
        $params = explode('~', RequestUtilities::get($urlParameter));
        if($params[0]) {
          $variables['unchecked']= null;
        }
      }
    }

    //donot show annual in ratetype facet
    if($node->widgetConfig->filterName == 'Rate Type'){
      if ($variables['unchecked'])
        foreach($variables['unchecked'] as $key => $value) {
          if($value[1] == 'ANNUAL') {
            $variables['unchecked'][$key] = 0;
          }
        }
    }

    //Payroll Type Filter
    $count = 0;
    if($node->widgetConfig->filterName == 'Payroll Type') {

      switch($node->nid) {
        case 898:
        case 899:
          //Advanced Search Payroll Type Facets
//        if ($checked && is_array($checked))
          if (isset($variables['checked']) && $variables['checked'])
            foreach($variables['checked'] as $key => $value) {
              if($value[0] == 2 || $value[0] == 3) {
                $count = $count + $value[2];
                $id = "2~3";
                unset($variables['checked'][$key]);
              }
              else {
                $variables['checked'][] = array($value[0], PayrollType::$SALARIED, $value[2]);
                unset($variables['checked'][$key]);
              }
            }
          if($count > 0) {
            $variables['checked'][] = array($id, PayrollType::$NON_SALARIED, $count);
          }
          break;
      }
    }

//Contract Includes Sub Vendors Facet
//For N/A value, some values are null, this needs to be handled
    if($node->widgetConfig->filterName == 'Contract Includes Sub Vendors') {
      if (isset($variables['unchecked']) && $variables['unchecked'])
        foreach($variables['unchecked'] as $key => $value) {
          if($value[1] == null) {
            $variables['unchecked'][$key][0] = 5;
            $variables['unchecked'][$key][1] = "N/A";
          }
        }
      if (isset($variables['checked']) && $variables['checked'])
        foreach($variables['checked'] as $key => $value) {
          if($value[1] == null) {
            $variables['checked'][$key][0] = 5;
            $variables['checked'][$key][1] = "N/A";
          }
        }
    }

    //Sub Vendor Status in PIP
//For N/A value, some values are null, this needs to be handled
    if($node->widgetConfig->filterName == 'Sub Vendor Status in PIP') {
      if (isset($variables['unchecked']) && $variables['unchecked'])
        foreach($variables['unchecked']as $key => $value) {
          if($value[1] == null) {
            $variables['unchecked'][$key][0] = 0;
            $variables['unchecked'][$key][1] = "N/A";
          }
        }
      if (isset($variables['checked']) && $variables['checked'])
        foreach($variables['checked'] as $key => $value) {
          if($value[1] == null) {
            $variables['checked'][$key][0] = 0;
            $variables['checked'][$key][1] = "N/A";
          }
        }
    }

    //Document ID filter display N/A for null values
    if($node->widgetConfig->filterName == 'Document ID') {
      if (isset($variables['unchecked']) && $variables['unchecked'])
        foreach($variables['unchecked'] as $key => $value) {
          if($value[1] == null) {
            $variables['unchecked'][$key][0] = "N/A";
            $variables['unchecked'][$key][1] = "N/A";
          }
        }
      if (isset($variables['checked']) && $variables['checked'])
        foreach($variables['checked'] as $key => $value) {
          if($value[1] == null) {
            $variables['checked'][$key][0] = "N/A";
            $variables['checked'][$key][1] = "N/A";
          }
        }
    }

    //Budget Name and Budget Type filter display N/A values as N/A for Nycha budget and Nycha revenue fields
    if( $node->nid == '1043' || $node->nid == '1044' || $node->nid == '1059' || $node->nid == '1060')
    {
      if (isset($variables['unchecked']) && $variables['unchecked'])
        foreach($variables['unchecked'] as $key => $value) {
          if($value[1] == null ) {
            $variables['unchecked'][$key][0] = "N/A";
            $variables['unchecked'][$key][1] = "N/A";
          }
        }
      if (isset($variables['checked']) && $variables['checked'])
        foreach($variables['checked'] as $key => $value) {
          if($value[1] == null) {
            $variables['checked'][$key][0] = "N/A";
            $variables['checked'][$key][1] = "N/A";
          }
        }
    }
    // NYCHA Contracts special condition in advanced search disable purchase order when selected.
    if($node->widgetConfig->filterName == 'Purchase Order Type') {
      $disableFacet = !(isset($node->widgetConfig->allowFacetDeselect) ? $node->widgetConfig->allowFacetDeselect : false);
    }

    //Modified Expense Budget Filter
    if($node->widgetConfig->filterName == 'Modified Expense Budget') {
      $showAllRecords = isset($node->widgetConfig->showAllRecords) ? $node->widgetConfig->showAllRecords : false;
      if(!$showAllRecords) {
        $params = explode('~', RequestUtilities::get($urlParameter));
        if($params[0]) {
          $variables['unchecked'] = null;
        }
      }
    }

//Revenue Recognized Filter
    if($node->widgetConfig->filterName == 'Revenue Recognized') {
      $showAllRecords = isset($node->widgetConfig->showAllRecords) ? $node->widgetConfig->showAllRecords : false;
      if(!$showAllRecords) {
        $params = explode('~', RequestUtilities::get($urlParameter));
        if($params[0]) {
          $variables['unchecked'] = null;
        }
      }
    }

    //Remove N/A from Prime/Sub Industry facets
    if($node->widgetConfig->filterName == 'Prime Industry' || $node->widgetConfig->filterName == 'Sub Industry'){
      foreach($variables['unchecked'] as $key => $value){
        if($value[1] == null) {
          unset($variables['unchecked'][$key]);
        }
      }
      foreach($variables['checked'] as $key=>$value){
        if($value[1] == null) {
          unset($variables['checked'][$key]);
        }
      }
    }
//Checking 'Asian-American' filter in Prime/Sub MWBE Category Facet
    $is_prime_filter = $node->widgetConfig->filterName == 'Prime M/WBE Category';
    $is_sub_filter = $node->widgetConfig->filterName == 'Sub M/WBE Category';
    $is_prime_sub_filter = $node->widgetConfig->filterName == 'M/WBE Category';
    if($is_prime_filter || $is_sub_filter || ($is_prime_sub_filter && $node->widgetConfig->parentNid == 939)){

      $asian_american_count = 0;
      $show_only_prime_certified = $is_prime_filter && ContractUtil::showPrimeMwbeData();
      $show_only_sub_certified = $is_sub_filter && ContractUtil::showSubMwbeData();

      foreach($variables['unchecked'] as $key => $value){
        $id = $value[0];
        $name = $value[1];
        $count = $value[2];
        if($id == 4 || $id == 5){
          $asian_american_count = $asian_american_count + $count;
          unset($variables['unchecked'][$key]);
        }
        else if($id == 7 || $id == 11){
          if($show_only_prime_certified || $show_only_sub_certified) {
            unset($variables['unchecked'][$key]);
          }
        }
        else if(!isset($name)) {
          unset($variables['unchecked'][$key]);
        }
      }

      if($asian_american_count > 0) {
        array_push($variables['unchecked'],array("4~5","Asian American",$asian_american_count));
        usort($variables['unchecked'],
          function($a, $b)
          {
            if ($a[2] == $b[2]) {
              return 0;
            }
            return ($a[2] > $b[2]) ? -1 : 1;
          }
        );
      }
      $asian_american_count = 0;


//    if (isset($checked) && is_array($checked))
      if (isset($variables['checked']) && $variables['checked'])
        foreach($variables['checked'] as $key => $value){
          $id = $value[0];
          $name = $value[1];
          $count = $value[2];
          if($id == 4 || $id == 5){
            $asian_american_count = $asian_american_count + $count;
            unset($variables['checked'][$key]);
          }
          else if($id == 7 || $id == 11){
            if($show_only_prime_certified || $show_only_sub_certified) {
              unset($variables['checked'][$key]);
            }
          }
          else if(!isset($name)) {
            unset($variables['checked'][$key]);
          }
        }

      if($asian_american_count > 0) {
        array_push($variables['checked'],array("4~5","Asian American",$asian_american_count));
        usort($variables['checked'],
          function($a, $b)
          {
            if ($a[2] == $b[2]) {
              return 0;
            }
            return ($a[2] > $b[2]) ? -1 : 1;
          }
        );
      }
    }

//Checking 'Asian-American' filter in MWBE Category Facet
    $count =0;
    if($node->widgetConfig->filterName == 'M/WBE Category' && $node->widgetConfig->parentNid != 939){
      $dashboard = RequestUtilities::get('dashboard');
      foreach($variables['unchecked'] as $key => $value){
        if(isset($dashboard) && $dashboard != 'ss'){
          if($value[0] == 7 || $value[0] == 11){
            unset($variables['unchecked'][$key]);
          }
        }
        //Remove N/A from facet
        if($value[1] == null) {
          unset($variables['unchecked'][$key]);
        }
      }
      if(isset($variables['checked']) && $variables['checked']) {
        foreach ($variables['checked'] as $key => $value) {
          if ($value[0] == 4 || $value[0] == 5) {
            $count = $count + $value[2];
            $id = "4~5";
            unset($variables['checked'][$key]);
          } else {
            array_push($variables['checked'], array($value[0], MappingUtil::getMinorityCategoryById($value[0], true), $value[2]));
            unset($variables['checked'][$key]);
          }
          //Remove N/A from facet
          if ($value[1] == null) {
            unset($variables['checked'][$key]);
          }
        }
      }
      if($count > 0 )array_push($variables['checked'],array($id,'Asian American',$count));
    }

//Data alteration for Vendor Type Facet
//Vendor Type facet for parentNid == 932/939 is a different implementation and should be ignored
    if($node->widgetConfig->filterName == 'Vendor Type'){
      if($node->widgetConfig->parentNid == 932 || $node->widgetConfig->parentNid == 939) {
        $vendor_counts = array();
        // To fix the issue with PM counts are getting added twice to PM~SM
//      if (is_array($checked)) {
        if ($variables['checked'] && $variables['checked']) {
          foreach($variables['checked'] as $row){
            $checked_vendor_types[$row[0]] = $row[2];
          }
        }
//        if (is_array($checked_vendor_types)) {
        if (isset($checked_vendor_types) && $checked_vendor_types) {
          foreach($checked_vendor_types as $key=>$value){
            if(in_array($key,array('P'))){
              $vendor_counts['P~PM'] = $vendor_counts['P~PM']+ $value;
            }
            if(in_array($key,array('S'))){
              $vendor_counts['S~SM'] = $vendor_counts['S~SM']+ $value;
            }
            if(in_array($key,array('PM'))){
              $vendor_counts['PM~SM'] = $vendor_counts['PM~SM']+ $value;
            }
            if(in_array($key,array('SM'))){
              $vendor_counts['PM~SM'] = $vendor_counts['PM~SM']+ $value;
            }
          }
        }
//        if (is_array($unchecked)) {
        if (isset($variables['unchecked']) && $variables['unchecked']) {
          foreach($variables['unchecked'] as $row){
            if(in_array($row[0],array('P'))){
              $vendor_counts['P~PM'] = $vendor_counts['P~PM']+ $row[2];
            }
            if(in_array($row[0],array('S'))){
              $vendor_counts['S~SM'] = $vendor_counts['S~SM']+ $row[2];
            }
            if(in_array($row[0],array('PM'))){
              $vendor_counts['PM~SM'] = $vendor_counts['PM~SM']+ $row[2];
            }
            if(in_array($row[0],array('SM'))){
              $vendor_counts['PM~SM'] = $vendor_counts['PM~SM']+ $row[2];
            }
          }
        }
        $variables['checked']= $variables['unchecked']= array();
        $selected_vendor_types =  RequestUtilities::get('vendortype');
//        if (is_array($vendor_counts)) {
        if (isset($vendor_counts) && $vendor_counts) {
          foreach($vendor_counts as $key=>$value){
            if (strpos($selected_vendor_types, $key) !== false) {
              array_push($variables['checked'], array($key, VendorType::getMixedVendorTypeNames($key),$value));
            }
            else {
              array_push($variables['unchecked'], array($key, VendorType::getMixedVendorTypeNames($key),$value));
            }
          }
        }
      }
      else {
        $vendor_types = RequestUtilities::get('vendortype');
        $vendor_type_data = VendorType::getVendorTypes($variables['checked'], $vendor_types);
        $vendor_type_data = VendorType::getVendorTypes($variables['unchecked'], $vendor_types);
        $variables['checked']= $vendor_type_data['checked'];
        $variables['unchecked'] = $vendor_type_data['unchecked'];
      }
    }

    return $variables;
  }
}
