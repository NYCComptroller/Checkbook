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


//log_error($node->results_contract_history);
//log_error($node->results_spending);

$vendor_contract_summary = array();
$vendor_contract_yearly_summary = array();


foreach ($node->results_contract_history as $contract_row) {
    if (!isset($vendor_contract_summary[$contract_row['vendor_name']]['current_amount'])) {
        $vendor_contract_summary[$contract_row['vendor_name']]['current_amount'] = $contract_row['current_amount_commodity_level'];
    }
    if (!isset($vendor_contract_summary[$contract_row['vendor_name']]['original_amount'])) {
        $vendor_contract_summary[$contract_row['vendor_name']]['original_amount'] = $contract_row['original_amount'];
    }

    if (!isset($vendor_contract_yearly_summary[$contract_row['vendor_name']][$contract_row['document_fiscal_year']]['current_amount'])) {
        $vendor_contract_yearly_summary[$contract_row['vendor_name']][$contract_row['document_fiscal_year']]['current_amount'] = $contract_row['current_amount_commodity_level'];
    }
    if (!isset($vendor_contract_yearly_summary[$contract_row['vendor_name']][$contract_row['document_fiscal_year']]['original_amount'])) {
        $vendor_contract_yearly_summary[$contract_row['vendor_name']][$contract_row['document_fiscal_year']]['original_amount'] = $contract_row['original_amount'];
    }
    $vendor_contract_yearly_summary[$contract_row['vendor_name']][$contract_row['document_fiscal_year']]['no_of_mods'] += 1;
}


$vendor_spending_yearly_summary = array();
foreach ($node->results_spending as $spending_row) {
    $vendor_spending_yearly_summary[$spending_row['vendor_name']][$spending_row['fiscal_year']]['no_of_trans'] += 1;
    $vendor_spending_yearly_summary[$spending_row['vendor_name']][$spending_row['fiscal_year']]['amount_spent'] += $spending_row['check_amount'];
    $vendor_contract_summary[$spending_row['vendor_name']]['check_amount'] += $spending_row['check_amount'];
}

//log_error($vendor_contract_yearly_summary);

/* SPENDING BY PRIME VENDOR */

//Main table header
$tbl_spending['header']['title'] = "<h3>SPENDING BY PRIME VENDOR</h3>";
$tbl_spending['header']['columns'] = array(
    array('value' => WidgetUtil::generateLabelMappingNoDiv("prime_vendor_name"), 'type' => 'text'),
    array('value' => WidgetUtil::generateLabelMappingNoDiv("current_amount"), 'type' => 'number'),
    array('value' => WidgetUtil::generateLabelMappingNoDiv("original_amount"), 'type' => 'number'),
    array('value' => WidgetUtil::generateLabelMappingNoDiv("spent_to_date"), 'type' => 'number')
);

$index_spending = 0;
foreach ($vendor_contract_summary as $vendor => $vendor_summary) {

    $open = $index_spending == 0 ? '' : 'open';
    //Main table columns
    $tbl_spending['body']['rows'][$index_spending]['columns'] = array(
        array('value' => "<a class='showHide " . $open . " expandTwo' ></a>" . $vendor, 'type' => 'text'),
        array('value' => custom_number_formatter_format($vendor_summary['current_amount'], 2, '$'), 'type' => 'number'),
        array('value' => custom_number_formatter_format($vendor_summary['original_amount'], 2, '$'), 'type' => 'number'),
        array('value' => custom_number_formatter_format($vendor_summary['check_amount'], 2, '$'), 'type' => 'number')
    );


    /* CONTRACT HISTORY BY PRIME VENDOR */
    //Main table header
    $tbl_contract_history = array();
    $tbl_contract_history['header']['title'] = "<h3>CONTRACT HISTORY BY PRIME VENDOR</h3>";
    $tbl_contract_history['header']['columns'] = array(
        array('value' => WidgetUtil::generateLabelMappingNoDiv("fiscal_year"), 'type' => 'text'),
        array('value' => WidgetUtil::generateLabelMappingNoDiv("no_of_mod"), 'type' => 'number'),
        array('value' => WidgetUtil::generateLabelMappingNoDiv("current_amount"), 'type' => 'number'),
        array('value' => WidgetUtil::generateLabelMappingNoDiv("original_amount"), 'type' => 'number'),
        array('value' => WidgetUtil::generateLabelMappingNoDiv("increase_decrease"), 'type' => 'number')
    );

    $index_contract_history = 0;
    foreach ($vendor_contract_yearly_summary[$vendor] as $year => $results_contract_history_fy) {

        $open = $index_contract_history == 0 ? '' : 'open';
        //Main table columns
        $tbl_contract_history['body']['rows'][$index_contract_history]['columns'] = array(
            array('value' => "<a class='showHide " . $open . "' ></a>FY " . $year, 'type' => 'text'),
            array('value' => $results_contract_history_fy['no_of_mods'], 'type' => 'number'),
            array('value' => custom_number_formatter_format($results_contract_history_fy['current_amount'], 2, '$'), 'type' => 'number'),
            array('value' => custom_number_formatter_format($results_contract_history_fy['original_amount'], 2, '$'), 'type' => 'number'),
            array('value' => custom_number_formatter_format($results_contract_history_fy['current_amount'] - $results_contract_history_fy['original_amount'], 2, '$'), 'type' => 'number')
        );
        //Inner table header
        $tbl_contract_history_inner = array();
        $tbl_contract_history_inner['header']['columns'] = array(
            array('value' => WidgetUtil::generateLabelMappingNoDiv('start_date'), 'type' => 'date'),
            array('value' => WidgetUtil::generateLabelMappingNoDiv('end_date'), 'type' => 'date'),
            array('value' => WidgetUtil::generateLabelMappingNoDiv('contract_purpose'), 'type' => 'text'),
            array('value' => WidgetUtil::generateLabelMappingNoDiv('commodity_line'), 'type' => 'number'),
            array('value' => WidgetUtil::generateLabelMappingNoDiv('current_amount'), 'type' => 'number'),
            array('value' => WidgetUtil::generateLabelMappingNoDiv('original_amount'), 'type' => 'number'),
            array('value' => WidgetUtil::generateLabelMappingNoDiv('increase_decrease'), 'type' => 'number')
        );
        $index_contract_history_inner = 0;
        foreach ($node->results_contract_history as $contract_history) {
            if ($contract_history['document_fiscal_year'] == $year && $contract_history['vendor_name'] == $vendor) {
                //Inner table columns
                $tbl_contract_history_inner['body']['rows'][$index_contract_history_inner]['columns'] = array(
                    array('value' => date_format(new DateTime($contract_history['start_date']), 'm/d/Y'), 'type' => 'date'),
                    array('value' => date_format(new DateTime($contract_history['end_date']), 'm/d/Y'), 'type' => 'date'),
                    array('value' => $contract_history['description'], 'type' => 'text'),
                    array('value' => $contract_history['fms_commodity_line'], 'type' => 'number'),
                    array('value' => custom_number_formatter_format($contract_history['current_amount_commodity_level'], 2, '$'), 'type' => 'number'),
                    array('value' => custom_number_formatter_format($contract_history['original_amount'], 2, '$'), 'type' => 'number'),
                    array('value' => custom_number_formatter_format($contract_history['current_amount_commodity_level'] - $contract_history['original_amount'], 2, '$'), 'type' => 'number')
                );
                $index_contract_history_inner++;
            }
        }
        $index_contract_history++;
        $tbl_contract_history['body']['rows'][$index_contract_history]['inner_table'] = $tbl_contract_history_inner;
        $index_contract_history++;
    }
    /* SPENDING TRANSACTIONS BY PRIME VENDOR */
    //Main table header
    $tbl_spending_transaction = array();
    $tbl_spending_transaction['header']['title'] = "<h3>SPENDING TRANSACTIONS BY PRIME VENDOR</h3>";
    $tbl_spending_transaction['header']['columns'] = array(
        array('value' => WidgetUtil::generateLabelMappingNoDiv("fiscal_year"), 'type' => 'text'),
        array('value' => WidgetUtil::generateLabelMappingNoDiv("no_of_transactions"), 'type' => 'number'),
        array('value' => WidgetUtil::generateLabelMappingNoDiv("amount_spent"), 'type' => 'number')
    );

    $index_spending_transaction = 0;
    if (count($vendor_spending_yearly_summary[$vendor]) > 0) {
        foreach ($vendor_spending_yearly_summary[$vendor] as $year => $results_spending_history_fy) {

            $open = $index_spending_transaction == 0 ? '' : 'open';
            //Main table columns
            $tbl_spending_transaction['body']['rows'][$index_spending_transaction]['columns'] = array(
                array('value' => "<a class='showHide " . $open . "' ></a>FY " . $year, 'type' => 'text'),
                array('value' => $results_spending_history_fy['no_of_trans'], 'type' => 'number'),
                array('value' => custom_number_formatter_format($results_spending_history_fy['amount_spent'], 2, '$'), 'type' => 'number')
            );
            //Inner table header
            $tbl_spending_transaction_inner = array();
            $tbl_spending_transaction_inner['header']['columns'] = array(
//                array('value' => WidgetUtil::generateLabelMappingNoDiv('start_date'), 'type' => 'text'),
                array('value' => WidgetUtil::generateLabelMappingNoDiv('check_amount'), 'type' => 'number'),
                array('value' => WidgetUtil::generateLabelMappingNoDiv('expense_category'), 'type' => 'text'),
                array('value' => WidgetUtil::generateLabelMappingNoDiv('agency_name'), 'type' => 'text'),
                array('value' => WidgetUtil::generateLabelMappingNoDiv('dept_name'), 'type' => 'text')
            );
            $index_spending_transaction_inner = 0;
            foreach ($node->results_spending as $contract_spending) {
                if ($contract_spending['fiscal_year'] == $year && $contract_spending['vendor_name'] == $vendor) {
                    //Inner table columns
                    $tbl_spending_transaction_inner['body']['rows'][$index_spending_transaction_inner]['columns'] = array(
//                        array('value' => 'N/A', 'type' => 'text'),
                        array('value' => custom_number_formatter_format($contract_spending['check_amount'], 2, '$'), 'type' => 'number'),
                        array('value' => $contract_spending['expenditure_object_name'], 'type' => 'text'),
                        array('value' => $contract_spending['agency_name'], 'type' => 'text'),
                        array('value' => $contract_spending['department_name'], 'type' => 'text')
                    );
                    $index_spending_transaction_inner++;
                }
                
            }
            $index_spending_transaction++;
            $tbl_spending_transaction['body']['rows'][$index_spending_transaction]['inner_table'] = $tbl_spending_transaction_inner;
            

            
            $index_spending_transaction++;
        }
        
    }

    $index_spending++;
    $tbl_spending['body']['rows'][$index_spending]['child_tables'] = array($tbl_contract_history, $tbl_spending_transaction);
    $index_spending++;

}
$html = "<div class='contracts-oge-spending-bottom'>" . WidgetUtil::generateTable($tbl_spending) . "</div>" ;
echo $html;






