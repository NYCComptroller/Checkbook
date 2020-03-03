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


$filter_years = _checkbook_max_data_year();
$q = request_uri();
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

    if($value['year_value'] <= $filter_years['year_value']){

        $display_text = 'FY '.$value['year_value'].' (Jul 1, '.($value['year_value']-1).' - Jun 30, '.$value['year_value'].')';

        $yearFromURL = RequestUtilities::get("year");
        $link = preg_replace("/year\/" . $yearFromURL . "/","year/" .  $value['year_id'],$q);

        $fiscal_year_data_array[] = array('display_text' => $display_text,
                                    'link' => $link,
                                    'value' => $value['year_id'],
                                    'selected' => $selected_fiscal_year);
    }
}

$fiscal_year_data_array = $fiscal_year_data_array;

$year_list = "<select id='year_list'>";
foreach($fiscal_year_data_array as $key => $value){
    $year_list .= "<option ".$value['selected']." value=".$value['value']." link='" . $value['link'] . "'  >".$value['display_text']."</option>";
}

$year_list .= "</select>";
print "<span class=\"filter\" >Filter: </span>" .$year_list;
