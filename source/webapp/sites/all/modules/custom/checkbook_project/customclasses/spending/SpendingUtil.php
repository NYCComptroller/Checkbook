<?php
/**
* This file is part of the Checkbook NYC financial transparency software.
* 
* Copyright (C) 2012, 2013 New York City
* 
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU Affero General Public License as
* published by the Free Software Foundation, either version 3 of the
* License, or (at your option) any later version.
* 
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU Affero General Public License for more details.
* 
* You should have received a copy of the GNU Affero General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/


class SpendingUtil{

    static $landingPageParams = array("agency"=>"agency","vendor"=>"vendor","category"=>"category");

    static function getSpendingCategoryDetails($categoryId, $columns=array('spending_category_id','display_name')){
        if(!isset($categoryId)){
            return NULL;
        }

        $categoryDetails = _checkbook_project_querydataset('checkbook:category',$columns, array('spending_category_id'=>$categoryId));
        return $categoryDetails;
    }
    
    
    static  public function getSpendingTransactionsTitle(){
      $agency_id = _getRequestParamValue("agency");
      $title = "";                
      //if(isset($agency_id)){
        $title = '';// _checkbook_project_get_name_for_argument("agency_id",RequestUtil::getRequestKeyValueFromURL("agency",current_path()));
        if(preg_match('/category\/1/',current_path())){
          $title = $title . ' Contract' ;
        }
        elseif(preg_match('/category\/2/',current_path())){
          $title = $title. ' Payroll' ;
        }
        elseif(preg_match('/category\/3/',current_path())){
          $title = $title . ' Capital Contracts' ;
        }
        elseif(preg_match('/category\/4/',current_path())){
          $title = $title. ' Others' ;
        }
        elseif(preg_match('/category\/5/',current_path())){
          $title = $title . ' Trust & Agency' ;
        }
        else{
          $title = $title . ' Total' ;
        }        
      //}
      $title = $title . " Spending Transactions";
      return $title ;
    }

    static public function getDepartmentIds(){
        $bottomURL = $_REQUEST['expandBottomContURL'];
        $deptId = NULL;$deptIds = array();

        if(isset($bottomURL) && preg_match("/dept/",$bottomURL)){
             $pathParams = explode('/', $bottomURL);
             $index = array_search('dept',$pathParams);
                $deptId =  filter_xss($pathParams[($index+1)]);
        }

        if($deptId){
            $query1 = "SELECT agency_id, fund_class_id, department_code FROM ref_department WHERE department_id = " .$deptId;
            $deptInfo = _checkbook_project_execute_sql($query1);

            $query2 = "SELECT department_id, fiscal_year, year_id FROM ref_department d
                       LEFT JOIN ref_year y ON d.fiscal_year = y.year_value
                       WHERE agency_id = ".$deptInfo[0]['agency_id']
                      ." AND fund_class_id = ".$deptInfo[0]['fund_class_id']
                      ." AND department_code = ".$deptInfo[0]['department_code'];

            $result = _checkbook_project_execute_sql($query2);

            foreach($result as $key => $value){
                $deptIds[$value['year_id']] = $value['department_id'];
            }

        }

        return $deptIds;

    }


    static public function getExpenseCatIds(){
        $bottomURL = $_REQUEST['expandBottomContURL'];
        $expCatId = NULL;$expCatIds = array();

        if(isset($bottomURL) && preg_match("/expcategory/",$bottomURL)){
             $pathParams = explode('/', $bottomURL);
             $index = array_search('expcategory',$pathParams);
                $expCatId =  filter_xss($pathParams[($index+1)]);
        }

        if($expCatId){
            $query1 = "SELECT expenditure_object_code FROM ref_expenditure_object WHERE expenditure_object_id = " .$expCatId;
            $expCatInfo = _checkbook_project_execute_sql($query1);

            $query2 = "SELECT expenditure_object_id, fiscal_year, year_id FROM ref_expenditure_object e
                       LEFT JOIN ref_year y ON e.fiscal_year = y.year_value
                       WHERE expenditure_object_code = '".$expCatInfo[0]['expenditure_object_code'] . "'";

            $result = _checkbook_project_execute_sql($query2);

            foreach($result as $key => $value){
                $expCatIds[$value['year_id']] = $value['expenditure_object_id'];
            }

        }

        return $expCatIds;

    }

}
