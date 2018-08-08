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


//Hide the Date Filter
//on Spending Advanced Search page when 'Check Date' parameter is present in the URL &
//on Pending Contracts Advanced Search page
if((preg_match('/^spending\/search\/transactions/',$_GET['q']) && (RequestUtilities::getRequestParamValue('chkdate') || !RequestUtilities::getRequestParamValue('year'))) || RequestUtilities::getRequestParamValue('contstatus') == 'P'
    || preg_match('/^contract\/all\/transactions/',$_GET['q'])){
    return;
}

//Get Year list from DB
$filter_years = _checkbook_max_data_year();

//$q is the new URL for the Date Filter options
$q = $_SERVER['REQUEST_URI'];
$url_parts = parse_url($q);
if(!isset($_REQUEST['expandBottomContURL']) && $url_parts['query']){
  $q = str_replace('?'.$url_parts['query'], "", $q);
}

//Set the default Domain to be 'Spending' for Trends and Smart Search
if(preg_match("/trends/",$q)){
    $q = "/spending_landing/yeartype/B/year/" ;
    $trends = true;
}else if(preg_match("/smart_search/",$q)){
    $q = "/spending_landing/yeartype/B/year/" ;
    $search = true;
}

//Pending Contracts do not have year filter applicable, so the Date Filter options are set to navigate to Active Expense Contracts landing page for the latest Fiscal Year
if(preg_match("/contracts_pending_exp_landing/",$q)){
   $q =preg_replace("/contracts_pending_exp_landing/","contracts_landing/status/A",$q );
}else if(preg_match("/contracts_pending_rev_landing/",$q)){
   $q =preg_replace("/contracts_pending_rev_landing/","contracts_landing/status/A",$q );
}

//Set $url_year_id_value and $url_year_type_value from the current URL
if(RequestUtilities::getRequestParamValue('year')){
    $url_year_id_value = RequestUtilities::getRequestParamValue('year');
}else if(RequestUtilities::getRequestParamValue('calyear')){
    $url_year_id_value = RequestUtilities::getRequestParamValue('calyear');
}

$url_year_type_value = (RequestUtilities::getRequestParamValue('yeartype')) ? RequestUtilities::getRequestParamValue('yeartype') : 'B';

//Set $year_id_value to current Fiscal Year ID for Pending Contracts
if(preg_match("/contracts_pending_exp_landing/",$_GET['q']) || preg_match("/contracts_pending_rev_landing/",$_GET['q'])){
    $url_year_id_value = _getFiscalYearID();
    $url_year_type_value = 'B';
}

$bottomURL = $_REQUEST['expandBottomContURL'];

$dept_Ids = SpendingUtil::getDepartmentIds();
$expCatIds = SpendingUtil::getExpenseCatIds();
if(isset($bottomURL)){
    $pathParams = explode('/', $bottomURL);
    if(preg_match("/dept/",$bottomURL)){
        $index = array_search('dept',$pathParams);
        $deptId =  filter_xss($pathParams[($index+1)]);
    }
    if(preg_match("/expcategory/",$bottomURL)){
        $index = array_search('expcategory',$pathParams);
        $expCatId =  filter_xss($pathParams[($index+1)]);
    }
}

$fiscal_year_data_array = array();
$calendar_year_data_array = array();
$current_fy_id = _getCurrentYearID();
$isSelected = false;


foreach($node->data as $key => $value){
    if($value['year_id'] == $url_year_id_value && $url_year_type_value == 'B'){
        $selected_fiscal_year = 'selected = yes';
        $selected_cal_year = '';
        $isSelected =  true;
    }elseif($value['year_id'] == $url_year_id_value && $url_year_type_value == 'C'){
        $selected_fiscal_year = '';
        $selected_cal_year = 'selected = yes';
    }else{
        $selected_fiscal_year = '';
        $selected_cal_year = '';
        $isSelected =  true;
    }
    //For Trends and Smart Search, set the default year value to current NYC fiscal year
    if($trends || $search){
        if($value['year_id'] == $current_fy_id){
            $selected_fiscal_year = 'selected = yes';
        }
    }
    /*********  Begining of Fiscal Year Options   ********/
    if($value['year_value'] <= $filter_years['year_value'] && $value['year_value'] != '2010'){

        $display_text = 'FY '.$value['year_value'].' (Jul 1, '.($value['year_value']-1).' - Jun 30, '.$value['year_value'].')';

        //For Trends and Smart Search append the year value for 'Spending' link
        if($trends || $search){
            $link = $q .$value['year_id'] ;
        }else{
            if(RequestUtilities::getRequestParamValue("calyear")){
                $link = preg_replace("/calyear\/" . $url_year_id_value . "/","year/" .  $value['year_id'],$q);
            }else{
                $link = preg_replace("/year\/" . $url_year_id_value . "/","year/" .  $value['year_id'],$q);
            }

          // $link = str_replace("/dept/".$deptId,"/dept/".$dept_Ids[$value['year_id']],$link);
            $link = str_replace("/expcategory/".$expCatId,"/expcategory/".$expCatIds[$value['year_id']],$link);

            //For Transaction pages replace the year ID and Year type in 'expandBottomContURL'
            if(preg_match("/expandBottomContURL/",$link) && (preg_match("/spending/",$link) || preg_match("/payroll/",$link))){
                $link_parts = explode("?expandBottomContURL=",$link);
                $url = $link_parts[0];
                $bottom_url = preg_replace("/\/calyear\//","/year/" ,$link_parts[1]);
                $bottom_url_year_id = RequestUtil::getRequestKeyValueFromURL("year",$bottom_url);
                $bottom_url = preg_replace('/\/year\/'.$bottom_url_year_id.'/','/year/'.$value['year_id'],$bottom_url);
                $link = $url . '?expandBottomContURL='. $bottom_url;
            }
        }

        //For the charts with the months links, need to persist the month param for the newly selected year
        if(isset($bottomURL) && preg_match('/month/',$bottomURL)){
            $old_month_id = RequestUtil::getRequestKeyValueFromURL("month",$bottomURL);
            $year_id = $value['year_id'];
            if(isset($old_month_id) && isset($year_id)) {
                $new_month_id = _translateMonthIdByYear($old_month_id,$year_id);
                $link = preg_replace('/\/month\/'.$old_month_id.'/','/month/'.$new_month_id,$link);
            }
        }

        //Set year type 'B' for all Fiscal year options
        $link = preg_replace("/yeartype\/./","yeartype/B",$link);

        $fiscal_year_data_array[] = array('display_text' =>$display_text ,
                                    'link' => $link,
                                    'value' => $value['year_id'].'~B',
                                    'selected' => $selected_fiscal_year);
    }
    /*********  End of Fiscal Year Options   ********/

    /*********  Begining of Calendar Year Options (Applicable for Payroll domain only)   ********/
    if(preg_match('/payroll/',$_SERVER['REQUEST_URI'])){
        if($value['year_value'] <= $filter_years['cal_year_value']){
            if(RequestUtilities::getRequestParamValue("calyear")){
                $link = preg_replace("/calyear\/" . $url_year_id_value . "/","calyear/" .  $value['year_id'],$q);
            }else{
                $link = preg_replace("/year\/" . $url_year_id_value . "/","year/" .  $value['year_id'],$q);
            }
            $link = str_replace("/dept/".$deptId,"/dept/".$dept_Ids[$value['year_id']],$link);
            $link = str_replace("/expcategory/".$expCatId,"/expcategory/".$expCatIds[$value['year_id']],$link);

            //For Transaction pages replace the year ID and Year type in 'expandBottomContURL'
            if(preg_match("/expandBottomContURL/",$link) && (preg_match("/spending/",$link) || preg_match("/payroll/",$link))){
                $link_parts = explode("?expandBottomContURL=",$link);
                $url = $link_parts[0];
                $bottom_url = preg_replace("/\/year\//","/calyear/" ,$link_parts[1]);
                $bottom_url_year_id = RequestUtil::getRequestKeyValueFromURL("calyear",$bottom_url);
                $bottom_url = preg_replace('/\/calyear\/'.$bottom_url_year_id.'/','/calyear/'.$value['year_id'],$bottom_url);
                $link = $url . '?expandBottomContURL='. $bottom_url;
            }

            //For the charts with the months links, need to persist the month param for the newly selected year
            if(isset($bottomURL) && preg_match('/month/',$bottomURL)){
                $old_month_id = RequestUtil::getRequestKeyValueFromURL("month",$bottomURL);
                $year_id = $value['year_id'];
                if(isset($old_month_id) && isset($year_id)) {
                    $new_month_id = _translateMonthIdByYear($old_month_id,$year_id,"C");
                    $link = preg_replace('/\/month\/'.$old_month_id.'/','/month/'.$new_month_id,$link);
                }
            }

            //Set year type 'C' for all calendar year options
            $link = preg_replace("/yeartype\/./","yeartype/C",$link);

            $calendar_year_data_array[] = array('display_text' => 'CY '.$value['year_value'].' (Jan 1, '.$value['year_value'].' - Dec 31, '.$value['year_value'].')',
                                                    'value' => $value['year_id'].'~C',
                                                    'link' => $link,
                                                    'selected' => $selected_cal_year
                                                    );
        }
    }
    /*********  End of Calendar Year Options (Applicable for Payroll domain only)   ********/
}

$year_data_array = array_merge($calendar_year_data_array, $fiscal_year_data_array);

//HTML for Date Filter
$year_list = "<select id='year_list'>";
foreach($year_data_array as $key => $value){
    $year_list .= "<option ".$value['selected']." value=".$value['value']." link='" . $value['link'] . "'  >".$value['display_text']."</option>";
}
$year_list .= "</select>";
if($isSelected){
    print "<span class=\"filter\" >Filter: </span>" . $year_list;
}



