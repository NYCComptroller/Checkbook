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
$sub_contract_reference = array();
$vendor_contract_summary = array();
$vendor_contract_yearly_summary = array();

foreach ($node->results_contract_history as $contract_row) {

    $sub_contract_reference[$contract_row['legal_name']][$contract_row['sub_contract_id']][] = $contract_row['sub_contract_id'];

    if (!isset($vendor_contract_yearly_summary[$contract_row['sub_contract_id']][$contract_row['source_updated_fiscal_year']]['current_amount'])) {
        $vendor_contract_yearly_summary[$contract_row['sub_contract_id']][$contract_row['source_updated_fiscal_year']]['current_amount'] = $contract_row['maximum_contract_amount'];
    }
    if (!isset($vendor_contract_yearly_summary[$contract_row['sub_contract_id']][$contract_row['source_updated_fiscal_year']]['original_amount'])) {
        $vendor_contract_yearly_summary[$contract_row['sub_contract_id']][$contract_row['source_updated_fiscal_year']]['original_amount'] = $contract_row['original_contract_amount'];
    }

    $vendor_contract_yearly_summary[$contract_row['sub_contract_id']][$contract_row['source_updated_fiscal_year']]['no_of_mods'] += 1;
    $vendor_contract_summary[$contract_row['legal_name']][$contract_row['sub_contract_id']]['original_amount'] = $contract_row['original_contract_amount'];
    $vendor_contract_summary[$contract_row['legal_name']][$contract_row['sub_contract_id']]['current_amount'] = $contract_row['maximum_contract_amount'];
    $vendor_contract_summary[$contract_row['legal_name']][$contract_row['sub_contract_id']]['minority_type_id'] = $contract_row['minority_type_id'];

    if($contract_row['latest_flag'] == 'Y'){
        $vendor_contract_summary[$contract_row['legal_name']]['current_amount'] = $vendor_contract_summary[$contract_row['legal_name']][$contract_row['sub_contract_id']]['current_amount'];
        $vendor_contract_summary[$contract_row['legal_name']]['original_amount'] =  $vendor_contract_summary[$contract_row['legal_name']][$contract_row['sub_contract_id']]['original_amount'];
        if (!isset($vendor_contract_summary[$contract_row['legal_name']]['minority_type_id'])) {
            $vendor_contract_summary[$contract_row['legal_name']]['minority_type_id'] = $contract_row['minority_type_id'];
        }
        $vendor_contract_summary[$contract_row['legal_name']]['sub_vendor_id'] =  $contract_row['vendor_id'];
    }

}

$vendor_spending_yearly_summary = array();
foreach ($node->results_spending as $spending_row) {
    $vendor_spending_yearly_summary[$spending_row['vendor_name']][$spending_row['sub_contract_id']][] = $spending_row['sub_contract_id'];

//    $vendor_spending_yearly_summary[$spending_row['vendor_name']][$spending_row['fiscal_year']]['no_of_trans'] += 1;
//    $vendor_spending_yearly_summary[$spending_row['vendor_name']][$spending_row['fiscal_year']]['amount_spent'] += $spending_row['check_amount'];

    $vendor_contract_summary[$spending_row['vendor_name']]['check_amount'] += $spending_row['check_amount'];
    $vendor_spending_yearly_summary[$spending_row['sub_contract_id']][$spending_row['fiscal_year']]['no_of_trans'] += 1;
    $vendor_spending_yearly_summary[$spending_row['sub_contract_id']][$spending_row['fiscal_year']]['amount_spent'] += $spending_row['check_amount'];

}

//log_error($vendor_contract_yearly_summary);

/* SPENDING BY SUB VENDOR */

//Main table header
$tbl_spending['header']['title'] = "<h3>SPENDING BY SUB VENDOR</h3>";

if(RequestUtilities::getRequestParamValue("doctype")=="CT1" || RequestUtilities::getRequestParamValue("doctype")=="CTA1"){
    $tbl_spending['header']['columns'] = array(
        array('value' => WidgetUtil::generateLabelMappingNoDiv("sub_vendor_name"), 'type' => 'text'),
        array('value' => WidgetUtil::generateLabelMappingNoDiv("mwbe_category"), 'type' => 'text'),
        array('value' => WidgetUtil::generateLabelMappingNoDiv("subvendor_status_pip"), 'type' => 'text'),
        array('value' => WidgetUtil::generateLabelMappingNoDiv("current_amount"), 'type' => 'number'),
        array('value' => WidgetUtil::generateLabelMappingNoDiv("original_amount"), 'type' => 'number'),
        array('value' => WidgetUtil::generateLabelMappingNoDiv("spent_to_date"), 'type' => 'number')
    );
}else{
   $tbl_spending['header']['columns'] = array(
        array('value' => WidgetUtil::generateLabelMappingNoDiv("sub_vendor_name"), 'type' => 'text'),
        array('value' => WidgetUtil::generateLabelMappingNoDiv("mwbe_category"), 'type' => 'text'),
        array('value' => WidgetUtil::generateLabelMappingNoDiv("current_amount"), 'type' => 'number'),
        array('value' => WidgetUtil::generateLabelMappingNoDiv("original_amount"), 'type' => 'number'),
        array('value' => WidgetUtil::generateLabelMappingNoDiv("spent_to_date"), 'type' => 'number')
    );
}

$contract_number = $node->results_contract_history[0]['contract_number'];

$querySubVendorinfo = "SELECT SUM(maximum_contract_amount) AS total_current_amt, SUM(original_contract_amount) AS total_original_amt, SUM(rfed_amount) AS total_spent_todate
FROM {subcontract_details}
WHERE contract_number = '".$contract_number."'
AND latest_flag = 'Y'
LIMIT 1";

$results4 = _checkbook_project_execute_sql_by_data_source($querySubVendorinfo,_get_current_datasource());
$res->data = $results4;

$total_current_amount = $res->data[0]['total_current_amt'];
$total_original_amount = $res->data[0]['total_original_amt'];
$total_spent_todate = $res->data[0]['total_spent_todate'];
?>
<div class="dollar-amounts">
    <div class="spent-to-date"><?php echo custom_number_formatter_format($total_spent_todate, 2, "$");?>
<div class="amount-title">Total Spent
    to Date
</div>
</div>
<div class="original-amount"><?php echo custom_number_formatter_format($total_original_amount, 2, '$');?>
    <div class="amount-title">Total Original
        Amount
    </div>
</div>
<div class="current-amount"><?php echo custom_number_formatter_format($total_current_amount, 2, '$');?>
    <div class="amount-title">Total Current
        Amount
    </div>
</div>
</div>
<?php
$index_spending = 0;
foreach ($vendor_contract_summary as $vendor => $vendor_summary) {

    $original_amount = $vendor_summary['original_amount'];
    $current_amount = $vendor_summary['current_amount'];

    $open = $index_spending == 0 ? '' : 'open';

    //Main table columns

    if(RequestUtilities::getRequestParamValue("doctype")=="CT1" || RequestUtilities::getRequestParamValue("doctype")=="CTA1"){

        $querySubVendorStatusInPIP = "SELECT
                                        c.aprv_sta_id, 
                                        c.aprv_sta_value AS sub_vendor_status_pip
                                    FROM sub_agreement_snapshot a
                                    LEFT JOIN subcontract_approval_status c ON c.aprv_sta_id = COALESCE(a.aprv_sta,6)
                                    WHERE a.latest_flag = 'Y'
                                    AND a.contract_number = '". $contract_number
                                    ."' AND a.vendor_id = ". $vendor_summary['sub_vendor_id']
                                    . " ORDER BY c.sort_order ASC LIMIT 1";

        $results5 = _checkbook_project_execute_sql_by_data_source($querySubVendorStatusInPIP,_get_current_datasource());
        $result->data = $results5;
        $subVendorStatusInPIP = ($result->data[0]['aprv_sta_id'] == 4 && $vendor_summary['check_amount'] == 0) ? "No Subcontract Payments Submitted" : $result->data[0]['sub_vendor_status_pip'];

        if(count($sub_contract_reference[$vendor]) > 1 && $index_spending == 0){
            $viewAll = "<a class='subContractViewAll'>Hide All<<</a>";
        }else{
            $viewAll = (count($sub_contract_reference[$vendor]) > 1) ? "<a class='subContractViewAll'>View All>></a>" : '';
        }

        $tbl_spending['body']['rows'][$index_spending]['columns'] = array(
            array('value' => "<a class='showHide " . $open . " expandTwo' ></a>" . $vendor, 'type' => 'text'),
            array('value' => MappingUtil::getMinorityCategoryById($vendor_summary['minority_type_id']), 'type' => 'text'),
            array('value' => $subVendorStatusInPIP . $viewAll, 'type' => 'text'),
            array('value' => custom_number_formatter_format($current_amount, 2, '$'), 'type' => 'number'),
            array('value' => custom_number_formatter_format($original_amount, 2, '$'), 'type' => 'number'),
            array('value' => custom_number_formatter_format($vendor_summary['check_amount'], 2, '$'), 'type' => 'number')
        );
    }else{
        $tbl_spending['body']['rows'][$index_spending]['columns'] = array(
            array('value' => "<a class='showHide " . $open . " expandTwo' ></a>" . $vendor, 'type' => 'text'),
            array('value' => MappingUtil::getMinorityCategoryById($vendor_summary['minority_type_id']), 'type' => 'text'),
            array('value' => custom_number_formatter_format($current_amount, 2, '$'), 'type' => 'number'),
            array('value' => custom_number_formatter_format($original_amount, 2, '$'), 'type' => 'number'),
            array('value' => custom_number_formatter_format($vendor_summary['check_amount'], 2, '$'), 'type' => 'number')
        );
    }

    /* SUB CONTRACT REFERENCE ID*/
    $index_sub_contract_reference = 0;
    $tbl_subcontract_reference = array();

    foreach($sub_contract_reference[$vendor] as $reference_id => $value){
        $querySubContractStatusInPIP = "SELECT
                                        c.aprv_sta_id, c.aprv_sta_value AS sub_contract_status_pip
                                    FROM sub_agreement_snapshot a
                                    LEFT JOIN subcontract_approval_status c ON c.aprv_sta_id = COALESCE(a.aprv_sta,6)
                                    WHERE a.latest_flag = 'Y'
                                    AND a.contract_number = '". $contract_number
                                    ."' AND a.vendor_id = ". $vendor_summary['sub_vendor_id']
                                    ."  AND a.sub_contract_id ='".$reference_id
                                    . "' ORDER BY c.sort_order ASC LIMIT 1";

        $results6 = _checkbook_project_execute_sql_by_data_source($querySubContractStatusInPIP,_get_current_datasource());
        $result->data = $results6;
        $subContractStatusInPIP = ($result->data[0]['aprv_sta_id'] == 4 && $vendor_summary['check_amount'] == 0) ? "No Subcontract Payments Submitted" : $result->data[0]['sub_contract_status_pip'];

        $ref_id = $reference_id;
        $open = $index_sub_contract_reference == 0 ? '' : 'open';
        $tbl_subcontract_reference['body']['rows'][$index_sub_contract_reference]['columns'] = array(
            array('value' => "<a class='showHide " . $open . " expandTwo' ></a>SUB CONTRACT REFERENCE ID: " . $ref_id . "<span class='subContractStatus'>".$subContractStatusInPIP."</span>"
                , 'type' => 'text'),
        );


    /* CONTRACT HISTORY BY SUB VENDOR */
    //Main table header
    $tbl_contract_history = array();
    $tbl_contract_history['header']['title'] = "<h3>CONTRACT HISTORY BY SUB VENDOR</h3>";
    $tbl_contract_history['header']['columns'] = array(
        array('value' => WidgetUtil::generateLabelMappingNoDiv("fiscal_year"), 'type' => 'text'),
        array('value' => WidgetUtil::generateLabelMappingNoDiv("no_of_mod"), 'type' => 'number'),
        array('value' => WidgetUtil::generateLabelMappingNoDiv("current_amount"), 'type' => 'number'),
        array('value' => WidgetUtil::generateLabelMappingNoDiv("original_amount"), 'type' => 'number')
        //array('value' => WidgetUtil::generateLabelMappingNoDiv("increase_decrease"), 'type' => 'number')
    );

    $index_contract_history = 0;

    if(count($vendor_contract_yearly_summary[$ref_id]) > 0){
    foreach ($vendor_contract_yearly_summary[$ref_id] as $year => $results_contract_history_fy) {

        $open = $index_contract_history == 0 ? '' : 'open';
        //Main table columns
        $tbl_contract_history['body']['rows'][$index_contract_history]['columns'] = array(
            array('value' => "<a class='showHide " . $open . "' ></a>FY " . $year, 'type' => 'text'),
            array('value' => $results_contract_history_fy['no_of_mods'], 'type' => 'number'),
            array('value' => custom_number_formatter_format($results_contract_history_fy['current_amount'], 2, '$'), 'type' => 'number'),
            array('value' => custom_number_formatter_format($results_contract_history_fy['original_amount'], 2, '$'), 'type' => 'number'),
           // array('value' => custom_number_formatter_format($results_contract_history_fy['current_amount'] - $results_contract_history_fy['original_amount'], 2, '$'), 'type' => 'number')
        );
        //Inner table header
        $tbl_contract_history_inner = array();
        $tbl_contract_history_inner['header']['columns'] = array(
            array('value' => WidgetUtil::generateLabelMappingNoDiv('start_date'), 'type' => 'date'),
            array('value' => WidgetUtil::generateLabelMappingNoDiv('end_date'), 'type' => 'date'),
            array('value' => WidgetUtil::generateLabelMappingNoDiv('contract_purpose'), 'type' => 'text'),
            //array('value' => WidgetUtil::generateLabelMappingNoDiv('commodity_line'), 'type' => 'number'),
            array('value' => WidgetUtil::generateLabelMappingNoDiv('current_amount'), 'type' => 'number'),
            array('value' => WidgetUtil::generateLabelMappingNoDiv('original_amount'), 'type' => 'number'),
            array('value' => WidgetUtil::generateLabelMappingNoDiv('increase_decrease'), 'type' => 'number')
        );
        $index_contract_history_inner = 0;

        foreach ($node->results_contract_history as $contract_history) {
            if ($contract_history['source_updated_fiscal_year'] == $year && $contract_history['sub_contract_id'] == $ref_id) {
                //Inner table columns
                $tbl_contract_history_inner['body']['rows'][$index_contract_history_inner]['columns'] = array(
                    array('value' => date_format(new DateTime($contract_history['start_date']), 'm/d/Y'), 'type' => 'date'),
                    array('value' => date_format(new DateTime($contract_history['end_date']), 'm/d/Y'), 'type' => 'date'),
                    array('value' => $contract_history['description'], 'type' => 'text'),
                   // array('value' => $contract_history['fms_commodity_line'], 'type' => 'number'),
                    array('value' => custom_number_formatter_format($contract_history['maximum_contract_amount'], 2, '$'), 'type' => 'number'),
                    array('value' => custom_number_formatter_format($contract_history['original_contract_amount'], 2, '$'), 'type' => 'number'),
                    array('value' => custom_number_formatter_format($contract_history['maximum_contract_amount'] - $contract_history['original_contract_amount'], 2, '$'), 'type' => 'number')
                );
                $index_contract_history_inner++;
            }
        }
        $index_contract_history++;
        $tbl_contract_history['body']['rows'][$index_contract_history]['inner_table'] = $tbl_contract_history_inner;
        $index_contract_history++;
    }
   }
    /* SPENDING TRANSACTIONS BY SUB VENDOR */
    //Main table header
    $tbl_spending_transaction = array();
    $tbl_spending_transaction['header']['title'] = "<h3>SPENDING TRANSACTIONS BY SUB VENDOR</h3>";
    $tbl_spending_transaction['header']['columns'] = array(
        array('value' => WidgetUtil::generateLabelMappingNoDiv("fiscal_year"), 'type' => 'text'),
        array('value' => WidgetUtil::generateLabelMappingNoDiv("no_of_transactions"), 'type' => 'number'),
        array('value' => WidgetUtil::generateLabelMappingNoDiv("amount_spent"), 'type' => 'number')
    );

    $index_spending_transaction = 0;
    if (count($vendor_spending_yearly_summary[$ref_id]) > 0) {
        foreach ($vendor_spending_yearly_summary[$ref_id] as $year => $results_spending_history_fy) {

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
                array('value' => WidgetUtil::generateLabelMappingNoDiv('date'), 'type' => 'text'),
                array('value' => WidgetUtil::generateLabelMappingNoDiv('check_amount'), 'type' => 'number'),
                //array('value' => WidgetUtil::generateLabelMappingNoDiv('expense_category'), 'type' => 'text'),
                array('value' => WidgetUtil::generateLabelMappingNoDiv('agency_name'), 'type' => 'text'),
                //array('value' => WidgetUtil::generateLabelMappingNoDiv('dept_name'), 'type' => 'text')
            );
            $index_spending_transaction_inner = 0;
            foreach ($node->results_spending as $contract_spending) {
                if ($contract_spending['fiscal_year'] == $year && $contract_spending['sub_contract_id'] == $ref_id) {
                    //Inner table columns
                    $tbl_spending_transaction_inner['body']['rows'][$index_spending_transaction_inner]['columns'] = array(
                        array('value' => date_format(new DateTime($contract_spending['check_eft_issued_date']), 'm/d/Y'), 'type' => 'date'),
                        array('value' => custom_number_formatter_format($contract_spending['check_amount'], 2, '$'), 'type' => 'number'),
                        //array('value' => $contract_spending['expenditure_object_name'], 'type' => 'text'),
                        array('value' => $contract_spending['agency_name'], 'type' => 'text'),
                        //array('value' => $contract_spending['department_name'], 'type' => 'text')
                    );
                    $index_spending_transaction_inner++;
                }
            }
            $index_spending_transaction++;
            $tbl_spending_transaction['body']['rows'][$index_spending_transaction]['inner_table'] = $tbl_spending_transaction_inner;
            $index_spending_transaction++;
        }
    }
        $index_sub_contract_reference++;
        $tbl_subcontract_reference['body']['rows'][$index_sub_contract_reference]['child_tables'] = array($tbl_contract_history, $tbl_spending_transaction);
        $index_sub_contract_reference++;
    }

    $index_spending++;
    $tbl_spending['body']['rows'][$index_spending]['child_tables'] = array($tbl_subcontract_reference);
    $index_spending++;
}
$html = "<div class='contracts-spending-bottom'>" . WidgetUtil::generateTable($tbl_spending) . "</div>" ;
echo $html;

?>




