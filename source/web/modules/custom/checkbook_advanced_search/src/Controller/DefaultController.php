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

namespace Drupal\checkbook_advanced_search\Controller;

use Drupal;
use Drupal\checkbook_infrastructure_layer\Utilities\FormattingUtilities;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Response;

require_once(Drupal::service('extension.list.module')->getPath('checkbook_advanced_search') . "/src/Results/checkbook_advanced_search.inc");
/**
 * Default controller for the checkbook_advanced_search module.
 */
class DefaultController extends ControllerBase {

  public function checkbook_advanced_search_display() {
    return \Drupal::formBuilder()->getForm('Drupal\checkbook_advanced_search\Form\CheckbookAdvancedSearchForm');
  }

  /**
   * @param $fiscal_year
   * @param $agency
   * @param $dept
   * @return mixed for Expense Category based on selected agency, Department and year
   */
  function checkbook_advanced_search_budget_expcategory($fiscal_year, $agency, $dept) {
    $params =  array("agency_id"=>$agency,"budget_fiscal_year_id"=>$fiscal_year);
    if($dept != '0' ) {
      $params["department_name"] = str_replace('__','/',$dept);
    }
    $results = get_db_results(false, 'checkbook:budget', array("object_class_name.object_class_name"), $params,"object_class_name.object_class_name");
    if ($results && count($results ) > 0) {
      $matches = array();
      foreach ($results as $key=>$value) {
        $matches[] = $value['object_class_name.object_class_name'];
      }
      return $this->drupal_json_output($matches);
    }
    else {
      return $this->drupal_json_output(array(array('label'=>'No Matches Found','value'=>'')));
    }
  }


  /**
   * @param $fiscal_year
   * @param $agency
   * @param $dept
   * @param $expcategory
   * @param $budget_code
   * @return mixed for budget name based on selected agency, department, expense category, year and budget code
   */
  function checkbook_advanced_search_budget_budgetname($fiscal_year, $agency, $dept, $expcategory, $budget_code, $conditional_categories) {
    $params =  array("budget_fiscal_year_id"=>$fiscal_year);
    if($agency != '0' ) {
      $params["agency_id"] = $agency;
    }
    if($dept != '0' ) {
      $params["department_name"] = str_replace('__','/',$dept);
    }
    if($expcategory != '0' ) {
      $params["object_class_name"] = str_replace('__','/',$expcategory);
    }
    if($budget_code != '0' ) {
      $params["budget_code"] = $budget_code;
    }
    if($conditional_categories != '0' ) {
      $params["event_id"] = $conditional_categories;
    }
    $results = get_db_results(false, 'checkbook:budget', array("budget_code_name.budget_code_name"), $params,"budget_code_name.budget_code_name");
    if((!$results || $results == 0) && $agency != '0'){
      unset($params["budget_code"]);
      $results = get_db_results(false, 'checkbook:budget', array("budget_code_name.budget_code_name"), $params,"budget_code_name.budget_code_name");
    }
    if ($results && count($results) > 0) {
      $matches = array();
      foreach ($results as $key=>$value) {
        $budget_name_value = $value['budget_code_name.budget_code_name'];
        $budget_name_text = FormattingUtilities::_ckbk_excerpt($budget_name_value);
        $matches[] = array('label'=> $budget_name_text,'value' => $budget_name_value, 'code' => $value["budget_code_name.budget_code_name"]);
      }
      return $this->drupal_json_output($matches);
    }
    else {
      return $this->drupal_json_output(array(array('label'=>'No Matches Found','value'=>'')));
    }
  }

  /**
   * @param $fiscal_year
   * @param $agency
   * @param $dept
   * @param $expcategory
   * @param $budget_name
   * @return mixed for budget code based on selected agency, department, expense category, year and budget name
   */
  function checkbook_advanced_search_budget_budgetcode($fiscal_year, $agency, $dept, $expcategory, $budget_name, $conditional_categories) {

    $params =  array("budget_fiscal_year_id"=>$fiscal_year);
    if($agency != '0' ) {
      $params["agency_id"] = $agency;
    }
    if($dept != '0' ) {
      $params["department_name"] = str_replace('__','/',$dept);
    }
    if($expcategory != '0' ) {
      $params["object_class_name"] = str_replace('__','/',$expcategory);
    }
    if($budget_name != '0' ) {
      $params["budget_code_name"] = str_replace('__','/',$budget_name);
    }
    if($conditional_categories != '0' ) {
      $params["event_id"] = $conditional_categories;
    }
    $results = get_db_results(false, 'checkbook:budget', array("budget_code_code.budget_code_code"), $params,"budget_code_code.budget_code_code");
    if((!$results || count($results) == 0) && $agency != '0'){
      unset($params["budget_code_name"]);
      $results = get_db_results(false, 'checkbook:budget', array("budget_code_code.budget_code_code"), $params,"budget_code_code.budget_code_code");
    }
    if ($results && count($results ) > 0) {
      $matches = array();
      foreach ($results as $key=>$value) {
        $matches[] = $value['budget_code_code.budget_code_code'];
      }
      return $this->drupal_json_output($matches);
    }
    else {
      return $this->drupal_json_output(array(array('label'=>'No Matches Found','value'=>'')));
    }
  }

  function drupal_json_output($var = NULL) {
    // We are returning JSON, so tell the browser.
    $response = new Response();
    if (isset($var)) {
      $response->setContent(json_encode($var));
      $response->headers->set('Content-Type', 'application/json');
      return $response;
    }
  }
}
