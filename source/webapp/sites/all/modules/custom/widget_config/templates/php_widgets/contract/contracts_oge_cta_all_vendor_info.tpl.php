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


//Main table header
$tbl['header']['title'] = "<div class='tableHeader'><h3>Prime Vendor Information</h3> <span class='contCount'>Number of Prime Vendors: ".count($node->vendors_list)." </span></div>";
$tbl['header']['columns'] = array(
    array('value' => WidgetUtil::generateLabelMappingNoDiv("prime_vendor_name"), 'type' => 'text'),
    array('value' => $node->widget_count_label, 'type' => 'number'),
    array('value' => WidgetUtil::generateLabelMappingNoDiv("spent_to_date"), 'type' => 'number'),
    array('value' => WidgetUtil::generateLabelMappingNoDiv("prime_vendor_address"), 'type' => 'text')
);

$vendor_cont_count = array();
foreach($node->vendor_contracts_count as $vendor_cont){
    $vendor_cont_count[$vendor_cont['vendor_id']]['count'] = $vendor_cont['count'];
    $vendor_cont_count[$vendor_cont['vendor_id']]['count'] = $vendor_cont['count'];
}

$count = 0;
if(count($node->vendors_list) > 0){
    foreach($node->vendors_list as $vendor){
        
        if(isset($vendor['vendor_id'])){
            $spending_link = "/spending/transactions/vendor/" . $vendor['vendor_id'] . "/fvendor/" . $vendor['vendor_id'] . "/datasource/checkbook_oge/newwindow";
        }
         
        if(preg_match("/newwindow/",$_GET['q'])) {
            $vendor_name = $vendor['vendor_name'];
        }
        else {
            $vendor_name =  "<a href='/contracts_landing/status/A/year/" . CheckbookDateUtil::getCurrentFiscalYearId() . "/yeartype/B/agency/" . $vendor['agency_id'] .
                "/datasource/checkbook_oge/vendor/" . $vendor['vendor_id']  . "?expandBottomCont=true'>" . $vendor['vendor_name']  . "</a>";
        }

        $spent_to_date_value =  custom_number_formatter_format($vendor['check_amount_sum'], 2, '$');
        if(preg_match("/newwindow/",$_GET['q'])) {
            $spent_to_date_link =  custom_number_formatter_format($vendor['check_amount_sum'], 2, '$');
        }
        else {
           $spent_to_date_link = "<a class='new_window' target='_new' href='" . $spending_link . "'>" . custom_number_formatter_format($vendor['check_amount_sum'], 2, '$')  . "</a>";
        }

        //Main table columns
        $tbl['body']['rows'][$count]['columns'] = array(
            array('value' => $vendor_name, 'type' => 'text'),
            array('value' => $vendor_cont_count[$vendor['vendor_id']]['count'], 'type' => 'number'),
            array('value' => $spent_to_date_value, 'type' => 'number_link', 'link_value' => $spent_to_date_link),
            array('value' => (strlen($vendor['address']) > 0) ? $vendor['address']: 'N/A', 'type' => 'text')
        );
        $count++;
    }
}

$html = WidgetUtil::generateTable($tbl);
echo $html;


