<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/
?>
<?php

$current_year = (int)date('Y');
$q = $_SERVER['REQUEST_URI'];
$array_q = explode('/',$q);

$year_key = array_search('year', $array_q);
$year_id_value = $array_q[$year_key + 1];

$fiscal_year_data_array = array();
$current_fy_id = _getFiscalYearID();

foreach($node->data as $key => $value){

    if($value['year_id'] == $year_id_value){
    	$selected_fiscal_year = 'selected = yes';
    }else{
        $selected_fiscal_year = '';
    }

    if($value['year_value'] <= _getYearValueFromID($current_fy_id)){

        $display_text = 'FY '.$value['year_value'].' (Jul 1, '.($value['year_value']-1).' - Jun 30, '.$value['year_value'].')';

        $yearFromURL = _getRequestParamValue("year");
        $link = preg_replace("/year\/" . $yearFromURL . "/","year/" .  $value['year_id'],$q);

        $fiscal_year_data_array[] = array('display_text' => $display_text,
                                    'link' => $link,
                                    'value' => $value['year_id'],
                                    'selected' => $selected_fiscal_year);
    }
}

$fiscal_year_data_array = array_reverse($fiscal_year_data_array);

$year_list = "<select id='year_list'>";
foreach($fiscal_year_data_array as $key => $value){
    $year_list .= "<option ".$value['selected']." value=".$value['value']." link='" . $value['link'] . "'  >".$value['display_text']."</option>";
}

$year_list .= "</select>";
print "<span class=\"filter\" >Filter: </span>" .$year_list;