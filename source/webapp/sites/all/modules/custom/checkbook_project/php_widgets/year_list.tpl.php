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
?>
<?php


$filter_years = _checkbook_max_data_year();
//$q is the new url 
$q = $_SERVER['REQUEST_URI'];

if(preg_match("/trends/",$q)){
  $q = "/spending_landing/yeartype/B/year/" ;
  $trends = true;
}else if(preg_match("/smart_search/",$q)){
  $q = "/spending_landing/yeartype/B/year/" ;
  $search = true;
}
else if(preg_match("/contracts_pending_exp_landing/",$q)){
  $q =preg_replace("/contracts_pending_exp_landing/","contracts_landing/status/A",$q );
}
else if(preg_match("/contracts_pending_rev_landing/",$q)){
  $q =preg_replace("/contracts_pending_rev_landing/","contracts_landing/status/A",$q );
}

$array_q = explode('/',preg_replace("/\?.*/","",$q));

$year_key = array_search('year', $array_q);
if(preg_match("/contracts_pending_exp_landing/",$_GET['q']) || preg_match("/contracts_pending_rev_landing/",$_GET['q'])){
  $array_q[$year_key + 1] =  _getFiscalYearID();
  $year_type_key = array_search('yeartype', $array_q);
  $year_type_value = 'B';
  $array_q[$year_type_key + 1] = 'B';
}

$chkdate =  _getRequestParamValue("chkdate");
$display = true;
// if page is spending transactions page coming from advanced search
if(preg_match('/^spending\/search\/transactions/',$_GET['q']) || preg_match('/yeartype/',$_GET['q'])){
  // both dates are not given
 
  if(isset($chkdate) && $chkdate !=""){
    $dates =explode('~',$chkdate); 
    if(isset($dates[0])){
      $start_fy = _checkbook_project_querydataset('checkbook:date_id',array('nyc_year_id'),array("date"=>$dates[0]));
      $start_fy = $start_fy[0]['nyc_year_id'];
      $start_cy_year = substr($dates[0],0,4);      
    }else{
      $start_fy = 0;
      $start_cy_year = 0;
    }
    if(isset($dates[1])){
      $end_fy = _checkbook_project_querydataset('checkbook:date_id',array('nyc_year_id'),array("date"=>$dates[1]));
      $end_fy = $end_fy[0]['nyc_year_id'];
      $end_cy_year = substr($dates[1],0,4);
    }else{
      $end_fy =  0;
      $end_cy_year = 0;
    }
    if( $end_cy_year >= 2010 ){ 
      // Dates are in different fiscal year
      if($start_fy != $end_fy ){
        if($start_cy_year == $end_cy_year ){
          $year_id_value = ($start_fy > 0)? $start_fy: $end_fy;
          $q= preg_replace("/\/chkdate\/[^\/]*/","", $q);
          $year_type_value = 'C';
          $cal_year_id_value = $year_id_value;
          $q =  $q . "/yeartype/C/year/"  ;
          $calYearSet = true;
        }else{
          $display = false;
        }
      }      
      else{
        $year_id_value = ($start_fy > 0)? $start_fy: $end_fy;
        $q= preg_replace("/\/chkdate\/[^\/]*/","", $q);
        if(_getYearValueFromID($year_id_value) == 2010){
          $year_type_value = 'C';
          $cal_year_id_value = $year_id_value;
          $q =  $q . "/yeartype/C/year/"  ;
          $calYearSet = true;
        }
        else{
          $year_type_value = 'B';
          $q =  $q . "/yeartype/B/year/"  ;
        }
      }
    }else{      
      $display = false;
    }
    
  }  
  else if(!preg_match('/yeartype/',$_GET['q'])){
    $display = false;
  }
}
if($display){

   $dept_Ids = SpendingUtil::getDepartmentIds();
   $expCatIds = SpendingUtil::getExpenseCatIds();

    $bottomURL = $_REQUEST['expandBottomContURL'];
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


    if(!isset($year_id_value )){
      $year_id_value = $array_q[$year_key + 1];
      if(!($year_id_value > 0)){
        $year_id_value = _getFiscalYearID();
      }
    }
    if(!isset($year_type_value )){
      $year_type_key = array_search('yeartype', $array_q);
      $year_type_value = $array_q[$year_type_key + 1];
    }
    
    $cal_year_key = array_search('calyear', $array_q);
    $cal_year_id_value = ($cal_year_key)? $array_q[$cal_year_key + 1] : $year_id_value;
    
    $fiscal_year_data_array = array();
    $calendar_year_data_array = array();
    $current_fy_id = _getFiscalYearID();
    
    $isSelected = false;
    if((_getYearValueFromID($year_id_value) == '2010') && ($year_type_value == 'B' || !$year_type_value)){
            $year_id_value = _getYearIDFromValue('2011');
    }
    foreach($node->data as $key => $value){
    
        if($value['year_id'] == $year_id_value && ($year_type_value == 'B' || !$year_type_value)){
        	$selected_fiscal_year = 'selected = yes';
            $selected_cal_year = '';
            $isSelected =  true;
        }elseif($value['year_id'] == $cal_year_id_value && $year_type_value == 'C'){
            $selected_fiscal_year = '';
            $selected_cal_year = 'selected = yes';
        }else{
            $selected_fiscal_year = '';
            $selected_cal_year = '';
            $isSelected =  true;
        }
        
        if($value['year_value'] <= $filter_years['year_value'] && $value['year_value'] != '2010'){
        	$display_text = 'FY '.$value['year_value'].' (Jul 1, '.($value['year_value']-1).' - Jun 30, '.$value['year_value'].')';        	 
        	if($trends || $search){
        		$link = $q .$value['year_id'] ; 
        	}else{
        		
	            $yearFromURL = _getRequestParamValue("year");
	            if($yearFromURL ==''){
	            	$yearFromURL = _getRequestParamValue("calyear");
	            	$link = preg_replace("/calyear\/" . $yearFromURL . "/","year/" .  $value['year_id'],$q);
	            }else{
	            	$link = preg_replace("/year\/" . $yearFromURL . "/","year/" .  $value['year_id'],$q);
	            }
	            $link = preg_replace("/yeartype\/./","yeartype/B",$link);
	            $link = str_replace("/dept/".$deptId,"/dept/".$dept_Ids[$value['year_id']],$link);
	            $link = str_replace("/expcategory/".$expCatId,"/expcategory/".$expCatIds[$value['year_id']],$link);
	
	            if(preg_match("/expandBottomContURL/",$link) && (preg_match("/spending/",$link) || preg_match("/payroll/",$link))){
	              $link_parts = explode("?expandBottomContURL=",$link);
                    $url = $link_parts[0];
                    $bottom_url = preg_replace("/\/calyear\//","/year/" ,$link_parts[1]);
                    $bottom_url_year_id = RequestUtil::getRequestKeyValueFromURL("year",$bottom_url);
                    $bottom_url = preg_replace('/\/year\/'.$bottom_url_year_id.'/','/year/'.$value['year_id'],$bottom_url);
                    $link = $url . '?expandBottomContURL='. $bottom_url;
	            }
        	}
            /*For the charts with the months links, need to persist the month param for the newly selected year*/
            if(isset($bottomURL) && preg_match('/month/',$bottomURL)){
                $old_month_id = RequestUtil::getRequestKeyValueFromURL("month",$bottomURL);
                $year_id = $value['year_id'];
                if(isset($old_month_id) && isset($year_id)) {
                    $new_month_id = _translateMonthIdByYear($old_month_id,$year_id);
                    $link = preg_replace('/\/month\/'.$old_month_id.'/','/month/'.$new_month_id,$link);
                }

            }
            $fiscal_year_data_array[] = array('display_text' =>$display_text ,
                                         'link' => $link,
                                        'value' => $value['year_id'].'~B',
                                        'selected' => $selected_fiscal_year);
        }
        if($value['year_value'] <= $filter_years['cal_year_value'] && preg_match("/payroll/",$q)){  
        	if($trends || $search){
        		$link = $q .$value['year_id'] ;
        		$link = "/spending_landing/yeartype/C/year/" .  $value['year_id'];
        	}else{      
	        	$yearFromURL = _getRequestParamValue("year");
	        	if($yearFromURL =="") {
	        		$yearFromURL = _getRequestParamValue("calyear");
	        	}
	            if($calYearSet || preg_match('/transactions/',$_GET['q'])){
	              $link = preg_replace("/year\/" . $yearFromURL . "/","calyear/" .  $value['year_id'],$q);
	            }
                else{
	              $link = preg_replace("/year\/" . $yearFromURL . "/","year/" .  $value['year_id'],$q);
	            }
                if(preg_match("/expandBottomContURL/",$link) && (preg_match("/spending/",$link) || preg_match("/payroll/",$link))){
                    $link_parts = explode("?expandBottomContURL=",$link);
                    $url = $link_parts[0];
                    $bottom_url = preg_replace("/\/year\//","/calyear/" ,$link_parts[1]);
                    $bottom_url_year_id = RequestUtil::getRequestKeyValueFromURL("calyear",$bottom_url);
                    $bottom_url = preg_replace('/\/calyear\/'.$bottom_url_year_id.'/','/calyear/'.$value['year_id'],$bottom_url);
                    $link = $url . '?expandBottomContURL='. $bottom_url;
                }

                $link = preg_replace("/yeartype\/./","yeartype/C",$link);
	            $link = str_replace("/dept/".$deptId,"/dept/".$dept_Ids[$value['year_id']],$link);
	            $link = str_replace("/expcategory/".$expCatId,"/expcategory/".$expCatIds[$value['year_id']],$link);
        	}
            /*For the charts with the months links, need to persist the month param for the newly selected year*/
            if(isset($bottomURL) && preg_match('/month/',$bottomURL)){
                $old_month_id = RequestUtil::getRequestKeyValueFromURL("month",$bottomURL);
                $year_id = $value['year_id'];
                if(isset($old_month_id) && isset($year_id)) {
                    $new_month_id = _translateMonthIdByYear($old_month_id,$year_id,"C");
                    $link = preg_replace('/\/month\/'.$old_month_id.'/','/month/'.$new_month_id,$link);
                }
            }
            $calendar_year_data_array[] = array('display_text' => 'CY '.$value['year_value'].' (Jan 1, '.$value['year_value'].' - Dec 31, '.$value['year_value'].')',
                                                'value' => $value['year_id'].'~C',
                                              'link' => $link,
                                                'selected' => $selected_cal_year
                                            );
        }
    }
    $year_data_array = array_merge($calendar_year_data_array, $fiscal_year_data_array);

    if(_getRequestParamValue('contstatus') == 'P'){
        return;
    }
    $year_list = "<select id='year_list'>";
    foreach($year_data_array as $key => $value){
        $year_list .= "<option ".$value['selected']." value=".$value['value']." link='" . $value['link'] . "'  >".$value['display_text']."</option>";
    }
    
    $year_list .= "</select>";

    if($isSelected)
        print "<span class=\"filter\" >Filter: </span>" . $year_list;
 }


