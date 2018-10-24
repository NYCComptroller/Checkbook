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


/* SPENDING BY PRIME VENDOR */

//Main table header
$tbl_spending = array();
$tbl_spending['header']['title'] = "<h3>SPENDING BY PRIME VENDOR</h3>";
$tbl_spending['header']['columns'] = array(
    array('value' => WidgetUtil::generateLabelMappingNoDiv("prime_vendor_name"), 'type' => 'text'),
    array('value' => WidgetUtil::generateLabelMappingNoDiv("current_amount"), 'type' => 'number'),
    array('value' => WidgetUtil::generateLabelMappingNoDiv("original_amount"), 'type' => 'number'),
    array('value' => WidgetUtil::generateLabelMappingNoDiv("spent_to_date"), 'type' => 'number')
);

$contract_vendor_details = '';
$spending_vendor_details = '';
$index_spending = 0;

foreach ($node->results_prime_vendor_info as $vendor => $vendor_summary) {

    $open = $index_spending == 0 ? '' : 'open';
    //Main table columns
    $tbl_spending['body']['rows'][$index_spending]['columns'] = array(
        array('value' => "<a class='showHide " . $open . " expandTwo' ></a>" . $vendor_summary['vendor_name'], 'type' => 'text'),
        array('value' => custom_number_formatter_format($vendor_summary['current_amount'], 2, '$'), 'type' => 'number'),
        array('value' => custom_number_formatter_format($vendor_summary['original_amount'], 2, '$'), 'type' => 'number'),
        array('value' => custom_number_formatter_format($vendor_summary['spent_to_date'], 2, '$'), 'type' => 'number')
    );

    if ( RequestUtilities::get("datasource") != "checkbook_oge") {
        $contract_vendor_details ='<div id = "contract_history">';
        $nid = 426;
        $node = node_load($nid);
        node_build_content($node);
        $contract_vendor_details .= drupal_render($node->content);
        $contract_vendor_details .= '</div>';

        $spending_vendor_details = '<div id = "spending_transactions">';
        $nid = 427;
        $node = node_load($nid);
        node_build_content($node);
        $spending_vendor_details .= drupal_render($node->content);

        $spending_vendor_details .='</div>';
    }

   $index_spending++;
   $tbl_spending['body']['rows'][$index_spending]['embed_node'] = array($contract_vendor_details, $spending_vendor_details);
   $index_spending++;
}

$html = "<div class='contracts-spending-top'>" . WidgetUtil::generateTable($tbl_spending) . "</div>" ;
echo $html;
