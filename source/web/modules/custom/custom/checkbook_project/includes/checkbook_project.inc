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

use Drupal\checkbook_infrastructure_layer\Constants\Common\Datasource;
use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;
use Drupal\checkbook_project\CommonUtilities\RequestUtil;
use Drupal\checkbook_project\PayrollUtilities\PayrollUtil;


/**
 * Return the name for the reference object... this will be used to get agency name, department name, vendor name etc from a give id
 * @param $keyword
 * @param $id
 * @return string
 */
function _checkbook_project_get_name_for_argument($keyword, $id, $data_source = null){
  if(!isset($data_source)) {
    $refURL = RequestUtilities::getRefUrl();
    if(isset($refURL)){
      $data_source = RequestUtilities::get('datasource', ['q'=>$refURL]);
    }else {
      $data_source = Datasource::getCurrent();
    }
  }
  $data_source = $data_source ?? Datasource::CITYWIDE;
  switch ($keyword) {
    case "agency_id":
      $agencies = _checkbook_project_querydataset($data_source . ':agency', array('agency_id', 'agency_name'), array($keyword => $id));
      return isset($agencies[0]['agency_name']) ? htmlentities(ucwords(strtolower($agencies[0]['agency_name']))) : NULL;
    case "object_class_id":
      $objects = _checkbook_project_querydataset($data_source . ':object_class', array('object_class_id', 'object_class_name'), array($keyword => $id));
      return isset($objects[0]['object_class_name']) ? ucwords(strtolower($objects[0]['object_class_name'])) : NULL ;
    case "budget_code_id":
      $budget_codes = _checkbook_project_querydataset($data_source . ':budget_code', array('budget_code_id', 'attribute_name'), array($keyword => $id));
      return isset($budget_codes[0]['attribute_name']) ? ucwords(strtolower($budget_codes[0]['attribute_name'])) : NULL;
    case "revenue_category_id":
      if ($data_source == Datasource::NYCHA) {
        $revcat_ids = _checkbook_project_querydataset($data_source . ':revenue', array('revenue_category_id', 'revenue_category'), array($keyword => $id));
        return isset($revcat_ids[0]['revenue_category'])? ucwords(strtolower($revcat_ids[0]['revenue_category'])) : NULL;
      }else {
        $revcat_ids = _checkbook_project_querydataset($data_source . ':revenue_category', array('revenue_category_id', 'revenue_category_name'), array($keyword => $id));
        return isset($revcat_ids[0]['revenue_category_name']) ? ucwords(strtolower($revcat_ids[0]['revenue_category_name'])) : NULL;
      }
    case "funding_class_id":
      $fundsrc_ids = _checkbook_project_querydataset($data_source . ':ref_funding_class', array('funding_class_id', 'funding_class_name'), array($keyword => $id));
      return isset($fundsrc_ids[0]['funding_class_name']) ? ucwords(strtolower($fundsrc_ids[0]['funding_class_name'])) : NULL;
    case "funding_class_code":
      $revcat_codes = _checkbook_project_querydataset($data_source . ':funding_class', array('funding_class_code', 'funding_class_name'), array($keyword => $id));
      return isset($revcat_codes[0]['funding_class_name']) ? ucwords(strtolower($revcat_codes[0]['funding_class_name'])) : NULL;
    case "funding_source_id":
        $fundsrc_ids = _checkbook_project_querydataset($data_source . ':funding_source', array('funding_source_id', 'display_funding_source_descr'), array($keyword => $id));
        return isset($fundsrc_ids[0]['display_funding_source_descr']) ? ucwords(strtolower($fundsrc_ids[0]['display_funding_source_descr'])) : NULL;
    case "responsibility_center_id":
      $respcenter_ids = _checkbook_project_querydataset($data_source . ':resp_centers', array('responsibility_center_id', 'responsibility_center_description'), array($keyword => $id));
      return isset($respcenter_ids[0]['responsibility_center_description']) ? ucwords(strtolower($respcenter_ids[0]['responsibility_center_description'])) : NULL;
    case "vendor_id":
      if ($data_source == Datasource::NYCHA) {
        $vendors = _checkbook_project_querydataset($data_source . ':vendor', array('vendor_id', 'vendor_name'), array($keyword => $id));
        return isset($vendors[0]['vendor_name']) ? ucwords(strtolower($vendors[0]['vendor_name'])) :NULL;
      } else {
        $vendors = _checkbook_project_querydataset($data_source . ':vendor', array('vendor_id', 'legal_name'), array($keyword => $id));
        return isset($vendors[0]['legal_name']) ? ucwords(strtolower($vendors[0]['legal_name'])) : NULL;
      }
    case "year_id":
      $years = _checkbook_project_querydataset($data_source . ':year', array('year_id', 'year_value'), array($keyword => $id));
      return isset($years[0]['year_value']) ? ucwords(strtolower($years[0]['year_value'])) : NULL;
    case "department_id":
      $depts = _checkbook_project_querydataset($data_source . ':department', array('department_id', 'department_name'), array($keyword => $id));
      return isset($depts[0]['department_name']) ? ucwords(strtolower($depts[0]['department_name'])) : NULL;
    case "department_code":
      if ($data_source == Datasource::NYCHA) {
        $deptcodes = _checkbook_project_querydataset($data_source . ':department', array('department_code', 'department_name'), array($keyword => $id));
        return isset($deptcodes[0]['department_name']) ? ucwords(strtolower($deptcodes[0]['department_name'])) : NULL;
      }else{
        // Add agency id filter to get the correct department name for Citywide budget
        $agency_id = RequestUtilities::get('agency');
        $deptcodes = _checkbook_project_querydataset($data_source . ':budget', array('department_code', 'department_name'), array($keyword => $id,'agency_id' => $agency_id));
      return isset($deptcodes[0]['department_name']) ? ucwords(strtolower($deptcodes[0]['department_name'])) : NULL;}
    case "award_method_id":
      $award_methods = _checkbook_project_querydataset($data_source . ':award_method', array('award_method_id', 'award_method_name'), array($keyword => $id));
      return isset($award_methods[0]['award_method_name']) ? ucwords(strtolower($award_methods[0]['award_method_name'])) : NULL;
    case "award_method_code":
      $parameters = array($keyword => $id);
      if ($data_source != 'checkbook_nycha') {
        $parameters['active_flag'] = 'Y';
      }
      $award_methods = _checkbook_project_querydataset($data_source . ':award_method', array('award_method_code', 'award_method_name'), $parameters);
      return isset($award_methods[0]['award_method_name']) ? ucwords(strtolower($award_methods[0]['award_method_name'])) : NULL;
    case "award_size_id":
      $award_sizes = _checkbook_project_querydataset($data_source . ':award_size', array('award_size_id', 'award_size_name'), array($keyword => $id));
      return isset($award_sizes[0]['award_size_name']) ? ucwords(strtolower('Contracts ' . $award_sizes[0]['award_size_name'])) : NULL;
    case "industry_type_id":
      if ($data_source == Datasource::NYCHA) {
        $industry_types = _checkbook_project_querydataset($data_source . ':industry_type', array('industry_type_id', 'display_industry_type_name'), array($keyword => $id));
        return isset($industry_types[0]['display_industry_type_name']) ? ucwords(strtolower($industry_types[0]['display_industry_type_name'])) : NULL;
      } else {
        $industry_types = _checkbook_project_querydataset($data_source . ':industry_type', array('industry_type_id', 'industry_type_name'), array($keyword => $id));
        return isset($industry_types[0]['industry_type_name']) ? ucwords(strtolower($industry_types[0]['industry_type_name'])) : NULL;
      }
    case "pending_contracts_vendor_id":
      $keyword = 'vendor_id';
      $pending_contracts_vendors = _checkbook_project_querydataset($data_source . ':pending_contracts', array('vendor_id', 'vendor_legal_name'), array($keyword => $id));
      return isset($pending_contracts_vendors[0]['vendor_legal_name']) ? ucwords(strtolower($pending_contracts_vendors[0]['vendor_legal_name'])) : NULL;
    case "pending_contracts_award_method":
      $keyword = 'award_method_code';
      $pending_contracts_award_method = _checkbook_project_querydataset($data_source . ':pending_contracts', array('award_method_code', 'award_method_name'), array($keyword => $id));
      return isset($pending_contracts_award_method[0]['award_method_name']) ? ucwords(strtolower($pending_contracts_award_method[0]['award_method_name'])) : NULL;
    case "employee_id":
      $employees = _checkbook_project_querydataset($data_source . ':employee', array('civil_service_title', 'employee_id'), array($keyword => $id));
      return isset($employees[0]['civil_service_title']) ? ucwords(strtolower($employees[0]['civil_service_title'])) : NULL;
    case "expenditure_object_id":
      $exp_categorys = _checkbook_project_querydataset($data_source . ':expenditure_object', array('expenditure_object_name', 'expenditure_object_id'), array($keyword => $id));
      return isset($exp_categorys[0]['expenditure_object_name']) ? ucwords(strtolower($exp_categorys[0]['expenditure_object_name'])) : NULL;
    case "expenditure_type_code":
      $exp_categorys = _checkbook_project_querydataset($data_source . ':expenditure_type', array('expenditure_type_description', 'expenditure_type_code'), array($keyword => $id));
      return isset($exp_categorys[0]['expenditure_type_description']) ? ucwords(strtolower($exp_categorys[0]['expenditure_type_description'])) : NULL;
    case "expenditure_type_id":
      $exp_categorys = _checkbook_project_querydataset($data_source . ':budget', array('expenditure_type_description', 'expenditure_type_id'), array($keyword => $id));
      return isset($exp_categorys[0]['expenditure_type_description']) ? ucwords(strtolower($exp_categorys[0]['expenditure_type_description'])) : NULL;
    case "rev_expenditure_type_id":
      $keyword = "expenditure_type_id";
      $exp_categorys = _checkbook_project_querydataset($data_source . ':revenue', array('expenditure_type_description', 'expenditure_type_id'), array($keyword => $id));
      return isset($exp_categorys[0]['expenditure_type_description']) ? ucwords(strtolower($exp_categorys[0]['expenditure_type_description'])) : NULL;
    case "program_phase_id":
      $program = _checkbook_project_querydataset($data_source . ':budget', array('program_phase_description', 'program_phase_id'), array($keyword => $id));
      return isset($program[0]['program_phase_description']) ? ucwords(strtolower($program[0]['program_phase_description'])) : NULL;
    case "rev_program_phase_id":
      $keyword = "program_phase_id";
      $prog_categorys = _checkbook_project_querydataset($data_source . ':revenue', array('program_phase_description', 'program_phase_id'), array($keyword => $id));
      return isset($prog_categorys[0]['program_phase_description']) ? ucwords(strtolower($prog_categorys[0]['program_phase_description'])) : NULL;
    case "gl_project_id":
      $project = _checkbook_project_querydataset($data_source . ':budget', array('gl_project_description', 'gl_project_id'), array($keyword => $id));
      return isset($project[0]['gl_project_description']) ? ucwords(strtolower($project[0]['gl_project_description'])) : NULL;
    case "rev_gl_project_id":
      $keyword ="gl_project_id";
      $proj_categorys = _checkbook_project_querydataset($data_source . ':revenue', array('gl_project_description', 'gl_project_id'), array($keyword => $id));
      return isset($proj_categorys[0]['gl_project_description']) ? ucwords(strtolower($proj_categorys[0]['gl_project_description'])) : NULL;
    case "agreement_id":
      $contracts = _checkbook_project_querydataset($data_source . ':history_agreement', array('contract_number', 'agreement_id'), array($keyword => $id));
      return isset($contracts[0]['contract_number']) ? ucwords(strtolower($contracts[0]['contract_number'])) : NULL;
    case "master_agreement_id":
      $contracts = _checkbook_project_querydataset($data_source . ':history_master_agreement', array('contract_number', 'master_agreement_id'), array($keyword => $id));
      return isset($contracts[0]['contract_number']) ? ucwords(strtolower($contracts[0]['contract_number'])) : NULL;
    case "document_code":
      $agencies = _checkbook_project_querydataset($data_source . ':ref_document_code', array('document_code', 'document_code_id'), array($keyword => $id));
      return isset($agencies[0]['document_code_id']) ? htmlentities(ucwords(strtolower($agencies[0]['document_code_id']))) : NULL;
    case "sub_vendor_id":
      $keyword = "vendor_id";
      $sub_vendors = _checkbook_project_querydataset($data_source . ':sub_vendor', array('vendor_id', 'legal_name'), array($keyword => $id));
      return isset($sub_vendors[0]['legal_name']) ? htmlentities(ucwords(strtolower($sub_vendors[0]['legal_name']))) : NULL;
    case "minority_type_id":
      $keyword = "minority_type_id";
      $mwbe = _checkbook_project_querydataset($data_source . ':minority_type', array('minority_type_id', 'minority_type_name'), array($keyword => $id));
      return isset($mwbe[0]['minority_type_name']) ? htmlentities(ucwords(strtolower($mwbe[0]['minority_type_name']))) : NULL;
    case "title":
      //For payroll title use code to retrieve name
      $result = PayrollUtil::getTitleByCode($id);
      return isset($result) ? htmlentities(ucwords($result)) : NULL;
    case "spending_category_code":
      $spending_category_code = _checkbook_project_querydataset($data_source . ':spending_category', array('spending_category_id', "spending_category_code"), array($keyword => $id));
      return isset($spending_category_code[0]['spending_category_id']) ? htmlentities(ucwords(strtolower($spending_category_code[0]['spending_category_id']))) : NULL;
    default:
      return null;
  }
}

/**
 * @return bool
 */
function _checkbook_check_isEDCPage(){
  return Datasource::isOGE();
}

/** Returns Boolean if this is an mwbe page */
function _checkbook_check_is_mwbe_page(){
  return null != RequestUtilities::getTransactionsParams('mwbe');
}

/** Returns Boolean if this is a sub vendor page */
function _checkbook_check_is_sub_vendor_page(){
  $url = \Drupal::service('path.current')->getPath();
  return preg_match('"/dashboard\/dsv"', $url);
}

/**
 * @param $string
 *
 * @return mixed
 */
function _checkbook_regex_replace_pattern($string){
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

function request_uri() {
  if (\Drupal::request()->server->get('REQUEST_URI') !== null) {
    $uri = \Drupal::request()->server->get('REQUEST_URI') ;
  }
  else {
    if (\Drupal::request()->server->get('argv') !== null) {
      $uri = \Drupal::request()->server->get('SCRIPT_NAME') . '?' . \Drupal::request()->server->get(['argv'][0]);
    }
    elseif (\Drupal::request()->server->get('QUERY_STRING') !== null) {
      $uri = \Drupal::request()->server->get('SCRIPT_NAME') . '?' . \Drupal::request()->server->get('QUERY_STRING');
    }
    else {
      $uri = \Drupal::request()->server->get('SCRIPT_NAME');
    }
  }
  // Prevent multiple slashes to avoid cross site requests via the Form API.
  $uri = '/' . ltrim($uri, '/');

  return $uri;
}


