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
namespace Drupal\checkbook_datafeeds\Utilities;

use Drupal\checkbook_infrastructure_layer\Constants\Common\CheckbookDomain;
use Drupal\checkbook_infrastructure_layer\Constants\Common\Datasource;
use Drupal\checkbook_infrastructure_layer\Utilities\FormattingUtilities;
use Drupal\checkbook_log\LogHelper;
use Drupal\checkbook_project\CommonUtilities\CheckbookDateUtil;
use Drupal\checkbook_project\MwbeUtilities\MappingUtil;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;

class FormUtil
{
  public static function getAgencies($data_source = null, bool $json_output = false, $feeds = true) {
    try {
      $dataController = data_controller_get_instance();
      switch ($data_source) {
        case 'checkbook_oge':
          $data = $dataController->queryDataset("checkbook_oge:agency", [
            'agency_code',
            'agency_id',
            'agency_name',
          ], ["is_display" => "Y", "is_oge_agency" => "Y"], 'agency_name');
          break;
        case 'checkbook_nycha':
          $data = $dataController->queryDataset("checkbook_nycha:agency", [
            'agency_code',
            'agency_id',
            'agency_name',
          ], ["is_display" => "Y"], 'agency_name');
          break;
        case 'checkbook_oge_nycha':
          $oge_data = $dataController->queryDataset("checkbook_oge:agency", [
            'agency_code',
            'agency_id',
            'agency_name',
          ], ["is_display" => "Y", "is_oge_agency" => "Y"], 'agency_name');

          $nycha_data = $dataController->queryDataset("checkbook_nycha:agency", [
            'agency_code',
            'agency_id',
            'agency_name',
          ], ["is_display" => "Y"], 'agency_name');
          $data = array_merge($nycha_data, $oge_data);
          break;
        default:
          $data = $dataController->queryDataset("checkbook:agency", [
            'agency_code',
            'agency_id',
            'agency_name',
          ], ["is_display" => "Y"], 'agency_name');
          break;
      }
      return self::getAgencyOptions($data, $data_source, $json_output, $feeds);
    }

    catch (Exception $e) {
      LogHelper::log_error("Error getting data from controller: \n" . $e->getMessage());
      return;
    }
  }

  /**
   * Get the agency options
   * @param $data
   * @param null $data_source
   * @param bool $json_output
   * @return mixed
   */
  public static function getAgencyOptions($data, $data_source = null, $json_output = false, $feeds = true)
  {
    if ($data_source == Datasource::CITYWIDE) {
      $title = 'Citywide (All Agencies)';
      $json_menu_options[] = array('label' => $title, 'value' => $title, 'code' => '');
      if($feeds) {
        $menu_options_attributes[$title] = array('title' => $title);
        $menu_options[$title] = $title;
      }else{
        $menu_options_attributes[""] = array('title' => $title);
        $menu_options[""] = $title;
      }
    }
    foreach ($data ?? [] as $row) {
      if($feeds) {
        $option = $row['agency_name'] . ' [' . $row['agency_code'] . ']';
        //Menu options
        $menu_options[$option] = FormattingUtilities::_ckbk_excerpt($option);
        $json_menu_options[] = array('label' => $menu_options[$option], 'value' => $option, 'code' => $option);
        //Menu option titles
        $menu_options_attributes[$option] = array('title' => $option);
      }else{
        $option = $row['agency_name'];
        //Menu options
        $menu_options[$row['agency_id']] = (strlen($option) > 20) ? substr($option, 0, 20) . '...' : $option;
        //Menu option titles
        $menu_options_attributes[$row['agency_id']] = array('title' => $option);
      }
    }
    $results['options'] = $menu_options;
    $results['options_attributes'] = $menu_options_attributes;
    if ($json_output) {
      return new JsonResponse($json_menu_options);
    }
    return $results;
  }

  /**
   * Get Responsibility center options for NYCHA Contracts
   * A migrated func from module checkbook_advanced_search
   * @param string $data_source
   * @param bool $feeds
   * @return array|void
   */
  public static function getResponsibilityCenters($data_source = Datasource::NYCHA, $feeds = false)
  {
    try {
      // Query update to remove null and junk data from drop-down display
      $query = "SELECT DISTINCT responsibility_center_id, responsibility_center_code, responsibility_center_description FROM ref_responsibility_center
                  WHERE responsibility_center_description IS NOT NULL AND responsibility_center_id NOT IN (1032,2066)
                  ORDER BY responsibility_center_description";
      $results = _checkbook_project_execute_sql_by_data_source($query, $data_source);

      $res_center_key_val_option_attributes = array('title' => 'Select Responsibility Center');
      $res_center_key_val_options = array('Select Responsibility Center');

      foreach ($results as $value) {
        if ($feeds) {
          $text = $value['responsibility_center_description'] . ' [' . $value['responsibility_center_code'] . ']';
          $res_center_key_val_option_attributes[$text] = array('title' => $text);
          $res_center_key_val_options[$text] = FormattingUtilities::_ckbk_excerpt($text);
        } else {
          $keys = $value['responsibility_center_id'];
          $res_center_key_val_option_attributes[$keys] = array('title' => $value['responsibility_center_description']);
          $res_center_key_val_options[$keys] = FormattingUtilities::_ckbk_excerpt($value['responsibility_center_description']);
        }
      }
      return array('options' => $res_center_key_val_options, 'option_attributes' => $res_center_key_val_option_attributes);
    } catch (Exception $e) {
      LogHelper::log_error("Error getting data from controller: \n" . $e->getMessage());
      return;
    }
  }


  /**
   * Get Sub Vendor Subcontract Status from Data Controller and format into a FAPI select input #options array.
   *
   * @return mixed
   *   Sub Vendor Subcontract Status ids and nammes
   */
  public static function getSubvendorStatusInPIP($feeds = true)
  {
    try {
      $dataController = data_controller_get_instance();
      $data = $dataController->queryDataset('checkbook:subcontract_approval_status',
        array('aprv_sta_id', 'aprv_sta_value'),
        NULL, 'sort_order', 0, 10, NULL);

      $menu_options = array('0' => 'Select Status');
      $title = 'Select Status';
      $menu_options_attributes = array($title => array('title' => $title));

      foreach ($data as $row) {
        if ($row['aprv_sta_id'] != "7") {
          if($feeds) {
            $option = $row['aprv_sta_value'] . '[' . $row['aprv_sta_id'] . ']';
          }else{
            $option = $row['aprv_sta_value'];
          }
          //Menu options
          $menu_options[$row['aprv_sta_id']] = FormattingUtilities::_ckbk_excerpt($option);
          //Menu option titles
          $menu_options_attributes[$row['aprv_sta_id']] = array('title' => $option);
        }
      }
      $results['options'] = $menu_options;
      $results['options_attributes'] = $menu_options_attributes;
      return $results;
    } catch (Exception $e) {
      LogHelper::log_error("Error getting data from controller: \n" . $e->getMessage());
      return;
    }
  }

  /**
   * Get Fund Classes from Data Controller and format into a FAPI select input #options array.
   *
   * @return mixed
   *   Fund classes and fund class names
   */
  public static function getFundClassOptions()
  {
    try {
      $dataController = data_controller_get_instance();
      $data = $dataController->queryDataset('checkbook:fund_class', array(
        'fund_class_code',
        'fund_class_name',
      ), NULL, 'fund_class_name');
      foreach ($data as $row) {
        if ($row['fund_class_name']) {
          if (strtolower($row['fund_class_name']) == 'general fund')
            $results[$row['fund_class_name'] . ' [' . $row['fund_class_code'] . ']'] = $row['fund_class_name'] . ' [' . $row['fund_class_code'] . ']';
        }
      }
      return array_unique($results);
    } catch (Exception $e) {
      LogHelper::log_error("Error getting data from controller: \n" . $e->getMessage());
      return;
    }
  }

  /**
   * Get Revenue Categories from Data Controller and format into a FAPI select input #options array.
   *
   * @return mixed
   *   Revenue categories and revenue category names
   */
  public static function getRevenueCategoryOptions()
  {
    try {
      $dataController = data_controller_get_instance();
      $data = $dataController->queryDataset('checkbook:revenue_category', array(
        'revenue_category_code',
        'revenue_category_name',
      ), NULL, 'revenue_category_name');
      $results = array('All Revenue Categories' => 'All Revenue Categories');
      foreach ($data as $row) {
        $results[$row['revenue_category_name'] . ' [' . $row['revenue_category_code'] . ']'] = $row['revenue_category_name'] . ' [' . $row['revenue_category_code'] . ']';
      }
      return array_unique($results);
    } catch (Exception $e) {
      LogHelper::log_error("Error getting data from controller: \n" . $e->getMessage());
      return;
    }
  }

  /**
   * Get Funding Sources from Data Controller and format into a FAPI select input #options array.
   *
   * @return mixed
   *   Funding source codes and funding source names
   */
  public static function getFundingClassOptions()
  {
    try {
      $dataController = data_controller_get_instance();
      $data = $dataController->queryDataset('checkbook:ref_funding_class', array(
        'funding_class_code',
        'funding_class_name',
      ), NULL, 'funding_class_name');
      $results = array('All Funding Classes' => 'All Funding Classes');
      foreach ($data as $row) {
        $results[$row['funding_class_name'] . ' [' . $row['funding_class_code'] . ']'] = $row['funding_class_name'] . ' [' . $row['funding_class_code'] . ']';
      }
      return array_unique($results);
    } catch (Exception $e) {
      LogHelper::log_error("Error getting data from controller: \n" . $e->getMessage());
      return;
    }
  }

  /**
   * @param string $data_source
   * @return array|void
   */
  public static function getFundingSourceOptions($data_source)
  {
    $options = ['Select Funding Source' => 'Select Funding Source'];
    $menu_options_attributes = ['Select Funding Source' => ['title' => 'Select Funding Source']];

    switch ($data_source) {
      case Datasource::NYCHA:
        try {
          $dataController = data_controller_get_instance();
          $data = $dataController->queryDataset('checkbook_nycha:funding_source', array(
            'funding_source_code',
            'funding_source_description',
          ), NULL, 'funding_source_description');
          if ($data) {
            foreach ($data as $row) {
              $key = $row['funding_source_description'] . ' [' . $row['funding_source_code'] . ']';
              $options[$key] = FormattingUtilities::_ckbk_excerpt($key);
              $menu_options_attributes[$key] = ['title' => $key];
            }
          }
        } catch (Exception $e) {
          LogHelper::log_error("Error getting data from controller: \n" . $e->getMessage());
          return;
        }
        break;
      default:
        break;
    }
    return [
      'options' => $options,
      'option_attributes' => $menu_options_attributes
    ];
  }

    /**
     * Get Spending Categories
     *
     * @param string $data_source
     * @return mixed
     *   Spending category codes and display names
     */
    public static function getSpendingCategories($data_source = Datasource::CITYWIDE)
    {
      try {
        $dataController = data_controller_get_instance();

        switch ($data_source) {
          case Datasource::NYCHA:
            $data = $dataController->queryDataset($data_source . ':spending_category', array(
              'spending_category_code',
              'display_spending_category_name',
            ), null, 'spending_category_code');
            break;
          default:
            $data = $dataController->queryDataset($data_source . ':spending_category', array(
              'spending_category_code',
              'display_name',
              'spending_category_name',
            ), null, 'display_order');
        }

        foreach ($data as $row) {
          $option = ($row['spending_category_name'] ?? $row['display_spending_category_name']) . ' [' . $row['spending_category_code'] . ']';
          if ($row['spending_category_code'] == 'ts') {
            $option = 'Total Spending [ts]';
            //Menu options
            $menu_options[''] = 'Total Spending [ts]';
            //Menu option titles
            $menu_options_attributes[$option] = array('title' => $option);
          } else {
            //Menu options
            $menu_options[$option] = FormattingUtilities::_ckbk_excerpt($option);
            //Menu option titles
            $menu_options_attributes[$option] = array('title' => $option);
          }
        }
        $results['options'] = $menu_options;
        $results['options_attributes'] = $menu_options_attributes;
        return $results;
      } catch (Exception $e) {
        LogHelper::log_error("Error getting data from controller: \n" . $e->getMessage());
        return;
      }
    }

    /**
     * Get Contract Type from Data Controller and format into a FAPI select input #options array.
     *
     * @return mixed
     *   Agreement type codes and agreement type names
     */
    public static function getContractTypes($feeds = false)
    {
      try {
        $dataController = data_controller_get_instance();
        $data = $dataController->queryDataset('checkbook:agreement_type', array(
          'agreement_type_id',
          'agreement_type_code',
          'agreement_type_name'
        ), NULL, 'agreement_type_name');
        $title = 'Select Contract Type';
        if($feeds) {
          $menu_options['No Contract Type Selected'] = $title;
        } else {
          $menu_options['0'] = $title;
        }
        $menu_options_attributes = array($title => array('title' => $title));

        foreach ($data as $row) {
          if($feeds) {
            $option = $row['agreement_type_name'] . ' [' . $row['agreement_type_code'] . ']';
            //Menu options
            $menu_options[$option] = FormattingUtilities::_ckbk_excerpt($option);
            //Menu option titles
            $menu_options_attributes[$option] = array('title' => $option);
          }
          else{
            $keys = 'id=>' . $row['agreement_type_id'] . '~code=>' . $row['agreement_type_code'];
            $menu_options[$keys] = $row['agreement_type_name'];
            $menu_options_attributes[$keys] = array('title' => $row['agreement_type_name']);
          }
        }
        $results['options'] = $menu_options;
        $results['options_attributes'] = $menu_options_attributes;
        return $results;
      } catch (Exception $e) {
        LogHelper::log_error("Error getting data from controller: \n" . $e->getMessage());
        return;
      }
    }

  /**
   * Get Program Phase from Data Controller and format into a FAPI select input #options array.
   * @param $dataSource
   * @param $feeds
   * @return array of Program Phase data
   */
  public static function getProgram($dataSource, $feeds = true)
  {
      $query = "SELECT DISTINCT program_phase_description || ' [' || program_phase_code  || ']' AS program,
                  program_phase_description, program_phase_id
                FROM ref_program_phase WHERE program_phase_description NOT iLIKE 'default' ORDER BY program ASC";
      $data = _checkbook_project_execute_sql_by_data_source($query, $dataSource);
      $title = 'Select Program';
      $options[''] = $title;
      $option_attributes = array($title => array('title' => $title));
      foreach ($data as $row) {
        if ($feeds) {
          $text = $row['program'];
          $option_attributes[$text] = array('title' => $text);
          $options[$text] = FormattingUtilities::_ckbk_excerpt($text);
        } else {
          $text = $row['program_phase_description'];
          $option_attributes[$row['program_phase_id']] = array('title' => $text);
          $options[$row['program_phase_id']] = FormattingUtilities::_ckbk_excerpt($text);
        }
      }
      return array('options' => $options, 'option_attributes' => $option_attributes);
  }

  /**
   * Get Program Phase from Data Controller and format into a FAPI select input #options array.
   * @param $dataSource
   * @return array of Program Phase data
   */
  public static function getProject($dataSource, $feeds = true)
  {
        $query = "SELECT gl_project_description || ' [' || gl_project_code  || ']' AS project,
                gl_project_description, gl_project_id FROM ref_gl_project
                WHERE gl_project_description NOT iLIKE 'default' ORDER BY gl_project_description ASC";
        $data = _checkbook_project_execute_sql_by_data_source($query, $dataSource);
        $title = 'Select Project';
        $options[''] = $title;
        $option_attributes[''] = array('title' => $title);
        foreach ($data as $row) {
          if ($feeds) {
            $text = $row['project'];
            $option_attributes[$text] = array('title' => $text);
            $options[$text] = FormattingUtilities::_ckbk_excerpt($text);
          } else {
            $text = $row['gl_project_description'];
            $option_attributes[$row['gl_project_id']] = array('title' => $text);
            $options[$row['gl_project_id']] = FormattingUtilities::_ckbk_excerpt($text);
          }
        }
        return array('options' => $options, 'option_attributes' => $option_attributes);
  }


  /**
   * Renamed D7 _dept_options function
   * Get Department from Data Controller and format into a FAPI select input #options array.
   * @param $agency
   *   Agency code
   * @param $sc
   *   Spending category
   * @param $year
   *   Year
   * @param $data_source
   *   optional parameter to specify the data source (i.e. checkbook, checkbook_oge)
   * @return array
   *  Department codes and department names filtered by agency, spending category, year
   */
  public static function getSpendingDepartment($year, $agency, $spending_cat, $data_source, $feeds = false)
  {
    if($feeds) {//Data-feeds
      $agency = ($data_source == Datasource::OGE) ? Datasource::getEDCCode() : $agency;//FormattingUtilities::emptyToZero($agency);
      $spending_cat = FormattingUtilities::emptyToZero($spending_cat);
      $agencyParam = $agency ? "AND agency_code = '" . $agency . "' " : "";
      $spendingCatParam = $spending_cat ? "AND spending_category_code = '" . $spending_cat . "' ": "";
    }else{//Advanced Search
      $spendingCatParam = $spending_cat ? " AND spending_category_id = '" . $spending_cat . "' " : "";
      $agencyParam = $agency ? " AND agency_id = '" . $agency . "' " : "";
      if(isset($year)) {//Advanced Search year format: fy~all, fy~122
        $yearId = substr($year, 3, strlen($year));
        $yearId = !in_array($yearId, array('all', '', '0', 0)) ? $yearId : null;
      }
    }

    //All years
    $year = !in_array($year, array('all', '', '0', 0, '~all', 'fy~all')) ? $year: null;
    $yearParam = ($feeds) ? (isset($year) ? " AND fiscal_year = '" . substr($year, 2, strlen($year)) . "' " : "")
      : (isset($yearId) ? " AND check_eft_issued_nyc_year_id = '" . substr($year, 3, strlen($year)) . "' " : "");

    $query = "SELECT DISTINCT department_name, department_code
              FROM disbursement_line_item_details
              WHERE 1 =1 {$agencyParam} {$yearParam} {$spendingCatParam}
              ORDER BY department_name ASC";

    $data = _checkbook_project_execute_sql_by_data_source($query, $data_source);

    $menu_options = [];
    if(count($data) > 0) {
      foreach ($data as $row) {
        if ($feeds) {
          $title = htmlentities($row['department_name'].'['.$row['department_code'].']');
          $option = FormattingUtilities::_ckbk_excerpt($title);
          //Menu options
          $menu_options[] = array('option' => $option,'title' => $title);
        } else {
          $title = htmlentities($row['department_name']);
          $name = FormattingUtilities::_ckbk_excerpt($title);
          $code = htmlentities($row['department_code']);
          //Menu options
          $menu_options[] = array('title' => $title,'name' => $name,'code' => $code);
        }
      }
    } else if (!($feeds)) {
      $menu_options[] = 'No Matches Found';
    }
    return new JsonResponse($menu_options);
  }


  /**
   * Renamed D7 _expcat_options function
   * Get Expenditure Category from Data Controller and format into a FAPI select input #options array.
   * @param $agency
   *   Agency code
   * @param $dept
   *   Department code
   * @param $sc
   *   Spending category code
   * @param $year
   *   Year
   *
   * @param null $data_source
   * @return array
   *   Expenditure object codes and expenditure object names, filtered by agency, department, spending category, year
   */
  public static function getSpendingExpenseCategory($year, $agency, $dept, $spending_cat, $data_source, $feeds)
  {
    // Special case for dept code
    if(str_contains($dept, '---------')){
      $deptCode = '---------';
    }
    else{
      $deptCode = FormattingUtilities::emptyToZero($dept);
    }
    //All years
    $year = !in_array($year, array('all', '~all', 'fy~all', '', '0', 0)) ? $year: null;
    if($feeds){
      $year = $year ? substr($year, 2, strlen($year)): null;
      $agencyParam = $agency ? "AND agency_code = '" . FormattingUtilities::emptyToZero($agency) . "' " : "";
      $spendingCatParam = $spending_cat ? "AND spending_category_code = '" . FormattingUtilities::emptyToZero($spending_cat) . "' ": "";
      $deptParam = $dept ? " AND department_code='".trim($deptCode)."'":"";
    } else {
      $yearId = $year ? substr($year, 3, strlen($year)): null;
      $spendingCatParam = $spending_cat ? " AND spending_category_id = '" . $spending_cat . "' " : "";
      $agencyParam = $agency ? " AND agency_id = '" . $agency . "' " : "";
      $deptParam = $dept ? " AND department_code='".trim($deptCode)."'":"";
    }

    if($data_source == Datasource::NYCHA){
      $yearParam = ($feeds) ? (isset($year) ? " AND issue_date_year = '" . $year . "' " : "")
        :(isset($yearId) ? " AND issue_date_year_id = '" . $yearId . "' " : "");

      $query = "SELECT DISTINCT expenditure_type_description AS expenditure_object_name,
                              expenditure_type_code AS expenditure_object_code
              FROM all_disbursement_transactions
              WHERE 1=1 {$yearParam} {$spendingCatParam} {$deptParam}
              ORDER BY expenditure_object_name ASC";
    }else{
      $yearParam = ($feeds) ? (isset($year) ? " AND fiscal_year = '" . $year . "' " : "")
        : (isset($yearId) ? " AND check_eft_issued_nyc_year_id = '" . $yearId . "' " : "");

      $query = "SELECT DISTINCT expenditure_object_code, expenditure_object_name
              FROM disbursement_line_item_details
              WHERE 1=1 {$spendingCatParam} {$yearParam} {$agencyParam} {$deptParam}
              ORDER BY expenditure_object_name ASC";
    }
    //var_dump($query);
    $data = _checkbook_project_execute_sql_by_data_source($query, $data_source);

    $menu_options = [];
    if($data && count($data) > 0) {
      foreach ($data as $row) {
        if ($feeds) {
          $title = htmlentities($row['expenditure_object_name'].'['.$row['expenditure_object_code'].']');
          $option = FormattingUtilities::_ckbk_excerpt($title);
          //Menu options
          $menu_options[] = array('option' => $option,'title' => $title);
        } else {
          $title = htmlentities($row['expenditure_object_name']);
          $name = FormattingUtilities::_ckbk_excerpt($title);
          $code = htmlentities($row['expenditure_object_code']);
          //Menu options
          $menu_options[] = array('title' => $title,'name' => $name,'code' => $code);
        }

      }
    } else if (!$feeds) {
      $menu_options[] = "No Matches Found";
    }
   // if($feeds) {
      return new JsonResponse($menu_options);
  //  }else{
    //  return $menu_options;
   // }
  }


  /**
   * Get mwbe category name and id using mapping
   * Total M/WBE 2,3,4,5,7,9,11
   * Asian American 4,5
   * Black American 2
   * Women 9
   * Hispanic American 3
   * Emerging ?
   * Non-M/WBE 7
   * Individuals and Others 11
   * @return array
   */
  public static function getMWBECategory()
  {
    $minority_cat_map = MappingUtil::getMinorityCategoryMappings();
    $results = array('' => 'Select Category');
    foreach ($minority_cat_map as $category => $minority_types) {
      $results[implode('~', $minority_types)] = $category;
    }
    return $results;
  }

  /**
   * Get the industry type name and id using the data controller
   * @param string $data_source
   * @return array|void
   */
  public static function getIndustry($data_source = Datasource::CITYWIDE, $feeds = false)
  {
    try {
      if($data_source == Datasource::NYCHA) {
        $query = "SELECT DISTINCT display_industry_type_name, industry_type_id, industry_type_code FROM ref_industry_type ORDER BY display_industry_type_name";
      } else{
        $query = "SELECT DISTINCT industry_type_name AS display_industry_type_name, industry_type_id FROM ref_industry_type ORDER BY industry_type_name";
      }
      $results = _checkbook_project_execute_sql_by_data_source($query, $data_source);

      $industry_key_val_option_attributes = array('title' => 'Select Industry');
      $industry_key_val_options = array('Select Industry');

      foreach ($results as $value) {
        if($feeds){
          if($data_source == Datasource::NYCHA) {
            $text = $value['display_industry_type_name'].' ['.$value['industry_type_code'].']';
          } else {
            $text = $value['display_industry_type_name'].' ['.$value['industry_type_id'].']';
          }

          $industry_key_val_option_attributes[$text] = array('title' => $text);
          $industry_key_val_options[$text] = FormattingUtilities::_ckbk_excerpt($text,40);
        }else {
          $keys = $value['industry_type_id'];
          $industry_key_val_option_attributes[$keys] = array('title' => $value['display_industry_type_name']);
          $industry_key_val_options[$keys] = FormattingUtilities::_ckbk_excerpt($value['display_industry_type_name']);
        }
      }
      return array('options' => $industry_key_val_options, 'option_attributes' => $industry_key_val_option_attributes);

    } catch (Exception $e) {
      LogHelper::log_error("Error getting data from controller: \n" . $e->getMessage());
      return;
    }
  }

  /**
   * Get years from Data Controller and format into a FAPI select input #options array.
   *
   * @param string|null $yeartype
   *   All Years or Budget Fiscal Years
   * @param $datasource
   * @param $domain
   * @return mixed
   *   Array of years formatted for FAPI select box #options
   */
  public static function getYearOptions($yeartype = NULL, $datasource = Datasource::CITYWIDE, $domain = NULL)
  {
    switch ($yeartype) {
      case 'all-years':
        try {
          $fydata = CheckbookDateUtil::getFiscalYearOptionsRange($datasource);
          $fyarray = array();
          foreach ($fydata as $row) {
            $fyarray['FY' . $row['year_value']] = 'FY ' . $row['year_value'];
          }
          if ($domain != CheckbookDomain::$SPENDING && $datasource == DataSource::NYCHA) {
            $fyarray['FY2010'] = 'FY 2010';
          }
          //Remove this after Year Filter Separation for Spending Data-feeds
          if ($domain == CheckbookDomain::$SPENDING) {
            $fyarray['FY2010'] = 'FY 2010';
            $fyarray = array_merge(array('FY2021' => 'FY 2021'), $fyarray);
          }
          $results = array_merge(array('0' => 'All Years'), $fyarray);
          return $results;
        } catch (Exception $e) {
          LogHelper::log_error("Error getting data from controller: \n" . $e->getMessage());
          return;
        }

      case 'budget-fiscal-years':
        $query = 'SELECT DISTINCT budget_fiscal_year, budget_fiscal_year_id FROM budget WHERE budget_fiscal_year > 2010 ORDER BY budget_fiscal_year_id DESC';
        $fy_data = _checkbook_project_execute_sql($query);
        $years = array();
        foreach ($fy_data as $year) {
          $years[$year['budget_fiscal_year']] = $year['budget_fiscal_year'];
        }
        return $years;

      default:
        try {
          $fydata = CheckbookDateUtil::getFiscalYearOptionsRange($datasource);
          $cydata = CheckbookDateUtil::getCalendarYearOptionsRange($datasource);
          $fyarray = $cyarray = [];
          foreach ($fydata as $row) {
            $fyarray['FY ' . $row['year_value']] = 'FY ' . $row['year_value'];
          }
          foreach ($cydata as $row) {
            $cyarray['CY ' . $row['year_value']] = 'CY ' . $row['year_value'];
          }
          $results = array_merge($cyarray, $fyarray);
          return $results;
        } catch (Exception $e) {
          LogHelper::log_error("Error getting data from controller: \n" . $e->getMessage());
          return;
        }
    }
  }

  /**
   * Get Contract Type options for NYCHA
   * @param string $data_source
   * @param bool $feeds
   * @return array|void
   */
  public static function getNychaContractTypes($data_source = Datasource::NYCHA, $feeds = false){
    try {
      $query = "SELECT DISTINCT contract_type_id, contract_type_code, contract_type_name, contract_type_description FROM ref_contract_type ORDER BY contract_type_name";
      $results = _checkbook_project_execute_sql_by_data_source($query, $data_source);

      $contract_type_key_val_option_attributes = array('title' => 'Select Contract Type');
      $contract_type_key_val_options = array('Select Contract Type');

      foreach ($results as $value) {
        if($feeds){
          $text = $value['contract_type_name'].' ['.$value['contract_type_code'].']';
          $contract_type_key_val_option_attributes[$text] = array('title' => $text);
          $contract_type_key_val_options[$text] = FormattingUtilities::_ckbk_excerpt($text);
        }else {
          $keys = 'id=>' . $value['contract_type_id'] . '~code=>' . $value['contract_type_code'];
          $contract_type_key_val_option_attributes[$keys] = array('title' => $value['contract_type_name']);
          $contract_type_key_val_options[$keys] = FormattingUtilities::_ckbk_excerpt($value['contract_type_name']);
        }
      }
      return array('options' => $contract_type_key_val_options, 'option_attributes' => $contract_type_key_val_option_attributes);
    } catch (Exception $e) {
      LogHelper::log_error("Error getting data from controller: \n" . $e->getMessage());
      return;
    }
  }

  /**
   * Get NYCHA Award Method options for NYCHA
   * @param string $data_source
   * @param bool $feeds
   * @return array|void
   */
  public static function getAwardMethod($data_source = Datasource::CITYWIDE, $feeds = false){
    try {
      if($data_source != Datasource::NYCHA){
        $query = "SELECT award_method_code, award_method_name FROM ref_award_method
          WHERE active_flag = 'Y' ORDER BY award_method_name";
      }else {
        $query = "SELECT DISTINCT award_method_id, award_method_code, award_method_name FROM ref_award_method
          ORDER BY award_method_name";
      }
      $results = _checkbook_project_execute_sql_by_data_source($query, $data_source);

      $award_method_key_val_option_attributes = array('title' => 'Select Award Method');
      $award_method_key_val_options = array('Select Award Method');

      foreach ($results as $value) {
        if($feeds){
          $text = $value['award_method_name'].' ['.$value['award_method_code'].']';
          $award_method_key_val_option_attributes[$text] = array('title' => $text);
          $award_method_key_val_options[$text] = FormattingUtilities::_ckbk_excerpt($text);
        }else {
          if($data_source != Datasource::NYCHA) {
            $keys = 'id=>' . $value['award_method_code'] . '~code=>' . $value['award_method_code'];
          }else{
            $keys = 'id=>' . $value['award_method_id'] . '~code=>' . $value['award_method_id'];
          }
          $award_method_key_val_option_attributes[$keys] = array('title' => $value['award_method_name']);
          $award_method_key_val_options[$keys] = FormattingUtilities::_ckbk_excerpt($value['award_method_name']);
        }
      }

      return array('options' => $award_method_key_val_options, 'option_attributes' => $award_method_key_val_option_attributes);

    } catch (Exception $e) {
      LogHelper::log_error("Error getting data from controller: \n" . $e->getMessage());
      return;
    }
  }

  public static function getContractIncludesSubvendors(){
    try {
      $dataController = data_controller_get_instance();
      $values = $dataController->queryDataset('checkbook:ref_subcontract_status',
        array('scntrc_status', 'scntrc_status_name', 'display_flag'),
        NULL, 'sort_order', 0, 10, NULL);
        $statusAttributes = [0 => ['title' => 'Select Status']];
        $status = [0 => 'Select Status'];
      foreach ($values as $value) {
        if($value['display_flag'] !== 1) {continue;}
        $statusAttributes[$value['scntrc_status']] = array('title' => $value['scntrc_status_name']);
        $status[$value['scntrc_status']] = FormattingUtilities::_ckbk_excerpt($value['scntrc_status_name'].'['.$value['scntrc_status'].']');
      }
      return array('options' => $status, 'option_attributes' => $statusAttributes);
    } catch (Exception $e) {
      LogHelper::log_error("Error getting data from the controller: \n" . $e->getMessage());
      return;
    }
  }

  /**
   * Get event name and id using the data controller
   * This is _get_event_name_and_id from D7
   *
   * @param string $data_source
   * @return array|void
   */
  public static function getEventNameAndId($attributes = NULL) {
    try {
      $dataController = data_controller_get_instance();
      $data = $dataController->queryDataset('checkbook:event', array(
        'event_id',
        'event_name'
      ), NULL, 'event_id');
      $results = array('0' => 'Select Event');
      foreach ($data as $row) {
        if ($attributes) {
          $results[$row['event_name'] . '[' . $row['event_id'] . ']'] = FormattingUtilities::_ckbk_excerpt($row['event_name']);
        } else {
          $results[$row['event_id']] = $row['event_name'];
        }
      }
      return array_unique($results);
    } catch (Exception $e) {
      LogHelper::log_error("Error getting data from controller: \n" . $e->getMessage());
      return;
    }
  }

}
