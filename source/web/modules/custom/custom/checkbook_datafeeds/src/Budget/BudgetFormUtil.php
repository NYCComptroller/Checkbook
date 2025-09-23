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

namespace Drupal\checkbook_datafeeds\Budget;
use Drupal\checkbook_infrastructure_layer\Constants\Common\CheckbookDomain;
use Drupal\checkbook_infrastructure_layer\Constants\Common\Datasource;
use Drupal\checkbook_infrastructure_layer\Utilities\FormattingUtilities;
use Symfony\Component\HttpFoundation\JsonResponse;

class BudgetFormUtil
{
  /**
   * Get Budget Type from Data Controller and format into a FAPI select input #options array.
   * @param $domain
   * @param string $dataSource
   * @param $budgetName
   * @param bool $json
   */
  public static function getBudgetType($domain, string $dataSource = Datasource::NYCHA, $budgetName = null, bool $json = false)
  {
    $where = "WHERE budget_type IS NOT NULL";
    if(!in_array($budgetName, array('Select Budget Name', '', 0, '0', 'null', null), true)){
      $budgetName = str_replace("__", "/", $budgetName);
      $where .= " AND budget_name = '". $budgetName ."' ";
    }
    $query = "SELECT DISTINCT budget_type FROM {$domain} {$where} ORDER BY budget_type ASC";
    $data = _checkbook_project_execute_sql_by_data_source($query, $dataSource);
    $title = 'Select Budget Type';
    $options[''] = $title;
    $option_attributes = array($title => array('title' => $title));
    $matches = array();
    foreach ($data as $row) {
      $text = $row['budget_type'];
      $option_attributes[$text] = array('title' => $text);
      $options[$text] = FormattingUtilities::_ckbk_excerpt($text);
      $matches[] = array('value'=> $text, 'text'=> $options[$text]);
    }
    if($json) {
      return new JsonResponse($matches);
    }else{
      return array('options' => $options, 'option_attributes' => $option_attributes);
    }
  }

  /**
   *  Get Budget Name from Data Controller and format into a FAPI select input #options array.
   * @param $domain
   * @param $dataSource
   * @param $budgetType
   * @param bool $json
   * @return array|JsonResponse
   */
  public static function getBudgetName($domain, string $dataSource = Datasource::NYCHA, $budgetType = null, bool $json = false)
  {
    $where = "WHERE budget_name IS NOT NULL";
    if(!in_array($budgetType, array('Select Budget Name', '', 0, '0', 'null', null), true)){
      $budgetType = str_replace("__", "/", $budgetType);
      $where .=" AND budget_type = '" . $budgetType . "' " ;
    }
    $query = "SELECT DISTINCT budget_name FROM {$domain} {$where} ORDER BY budget_name ASC";
    $data = _checkbook_project_execute_sql_by_data_source($query, $dataSource);
    $title = 'Select Budget Name';
    $options[''] = $title;
    $option_attributes = array($title => array('title' => $title));
    $matches = array();
    foreach ($data as $row) {
      $text = $row['budget_name'];
      $option_attributes[$text] = array('title' => $text);
      $options[$text] = FormattingUtilities::_ckbk_excerpt($text);
      $matches[] = array('value' => $text, 'text' => $options[$text]);
    }
    if ($json) {
      return new JsonResponse($matches);
    } else {
      return array('options' => $options, 'option_attributes' => $option_attributes);
    }
  }

  /**
   * Get Expenditure Category from Data Controller and format into a FAPI select input #options array.
   *
   * @param $domain
   * @param $year
   *   Year
   * @param $agency
   *   Agency code
   * @param $dept
   *   Department code
   * @param string $dataSource
   * @param bool $feeds
   * @return mixed Expenditure object codes and expenditure object names, filtered by agency, department, year
   *   Expenditure object codes and expenditure object names, filtered by agency, department, year
   */
  public static function getBudgetExpCatOptions($domain, $year, $agency, $dept, string $dataSource = Datasource::CITYWIDE, bool $feeds = true)
  {
    if ($dataSource == Datasource::NYCHA) {
      $query = "SELECT DISTINCT expenditure_type_description || ' [' || expenditure_type_code || ']' AS expenditure_object_code,
                                  expenditure_type_id, expenditure_type_description
                  FROM {$domain} ORDER BY expenditure_object_code ASC";
      $results = _checkbook_project_execute_sql_by_data_source($query, $dataSource);
      $title = ($domain == CheckbookDomain::$REVENUE) ? 'Select Revenue Expense Category' : 'Select Expense Category';
      $options[''] = $title;
      $option_attributes = array($title => array('title' => $title));
      foreach ($results as $row) {
        if ($feeds) {
          $text = $row['expenditure_object_code'];
          $option_attributes[$text] = array('title' => $text);
          $options[$text] = FormattingUtilities::_ckbk_excerpt($text);
        } else {
          $text = $row['expenditure_type_description'];
          $option_attributes[$row['expenditure_type_id']] = array('title' => $text);
          $options[$row['expenditure_type_id']] = FormattingUtilities::_ckbk_excerpt($text);
        }
      }
      return array('options' => $options, 'option_attributes' => $option_attributes);
    } else {
      $agency = FormattingUtilities::emptyToZero(urldecode($agency));
      $dept = FormattingUtilities::emptyToZero(urldecode($dept));
      $matches = [];
      if ($agency) {
        $agencyString = " agency_code = '" . $agency . "' ";
        $yearString = ($year) ? " AND budget_fiscal_year = " . ltrim($year, 'FY') . " " : "";
        $deptString = ($dept) ? " AND department_code = '" . $dept . "' " : "";

        $query = "SELECT DISTINCT object_class_name || ' [' || object_class_code || ']' expenditure_object_code  FROM {$domain} WHERE"
          . $agencyString . $yearString . $deptString . "ORDER BY expenditure_object_code ASC";
        $results = _checkbook_project_execute_sql_by_data_source($query, $dataSource);
        $options = array();
        if (count($results) > 0) {
          foreach ($results as $result) {
            $options[$result['expenditure_object_code']] = $result['expenditure_object_code'];
          }
        }
        foreach ($options as $value) {
          $matches[] = htmlentities($value);
        }
      }
      return new JsonResponse($matches);
    }
  }

  public function getBudgetDeptOptions($year, $agency, $feeds) {
    $agency = FormattingUtilities::emptyToZero(urldecode($agency));
    $matches = [];
    if ($agency) {
      if($feeds) {
        $agencystring = " agency_code = '" . $agency . "' ";
        $yearstring = " AND budget_fiscal_year = " . $year . " ";
        $query = "SELECT DISTINCT department_name || ' [' || department_code || ']' department_name FROM budget WHERE" . $agencystring . $yearstring . "ORDER BY department_name ASC";
      }else{
        $agencystring = " agency_id = '" . $agency . "' ";
        $yearstring = " AND budget_fiscal_year_id = " . $year . " ";
        $query = "SELECT DISTINCT department_name FROM budget WHERE" . $agencystring . $yearstring . "ORDER BY department_name ASC";

      }
      $results = _checkbook_project_execute_sql($query);
      if (count($results) > 0) {
        foreach ($results as $result) {
            $options[$result['department_name']] = $result['department_name'];
        }
      }
      foreach ($options as $value) {
        $matches[] = htmlentities($value);
      }
    }
    return new JsonResponse($matches);
  }
}
