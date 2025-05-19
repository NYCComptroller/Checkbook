<?php

namespace Drupal\widget_config\Twig\Contracts;

use DateTime;
use Drupal\checkbook_infrastructure_layer\Constants\Common\Datasource;
use Drupal\checkbook_infrastructure_layer\Utilities\FormattingUtilities;
use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;
use Drupal\checkbook_project\CommonUtilities\CheckbookDateUtil;
use Drupal\checkbook_project\EdcUtilities\EdcUtilities;
use Drupal\checkbook_project\MwbeUtilities\MappingUtil;
use Drupal\checkbook_project\WidgetUtilities\WidgetUtil;
use stdClass;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ContractsDetailsConfigExtension extends AbstractExtension
{
  public function getFunctions()
  {
    return [
      'contracts_details_mma_master_agreement' => new TwigFunction('contracts_details_mma_master_agreement', [
        $this,
        'contracts_details_mma_master_agreement',
      ]),
      'contracts_cta_history_table_body' => new TwigFunction('contracts_cta_history_table_body', [
        $this,
        'contracts_cta_history_table_body',
      ]),
      'contracts_cta_spending_bottom_page' => new TwigFunction('contracts_cta_spending_bottom_page', [
        $this,
        'contracts_cta_spending_bottom_page',
      ]),
      'contracts_cta_spending_by_exp_cat_table_body' => new TwigFunction('contracts_cta_spending_by_exp_cat_table_body', [
        $this,
        'contracts_cta_spending_by_exp_cat_table_body',
      ]),
      'contracts_cta_spending_history_table_body' => new TwigFunction('contracts_cta_spending_history_table_body', [
        $this,
        'contracts_cta_spending_history_table_body',
      ]),
      'contracts_ma_history_table_body' => new TwigFunction('contracts_ma_history_table_body', [
        $this,
        'contracts_ma_history_table_body',
      ]),
      'contracts_oge_cta_all_vendor_info_page' => new TwigFunction('contracts_oge_cta_all_vendor_info_page', [
        $this,
        'contracts_oge_cta_all_vendor_info_page',
      ]),
      'contracts_oge_cta_spending_bottom_page' => new TwigFunction('contracts_oge_cta_spending_bottom_page', [
        $this,
        'contracts_oge_cta_spending_bottom_page',
      ]),
      'contracts_ca_details_spending_link' => new TwigFunction('contracts_ca_details_spending_link', [
        $this,
        'contracts_ca_details_spending_link',
      ]),
      'contracts_ca_details_spending_table' => new TwigFunction('contracts_ca_details_spending_table', [
        $this,
        'contracts_ca_details_spending_table',
      ]),
    ];
  }

  public function contracts_details_mma_master_agreement($node) {
    if (RequestUtilities::get('datasource') == 'checkbook_oge' && !preg_match('/newwindow/', \Drupal::request()->query->get('q')) && $node->data_source_amounts_differ) {
      $alt_txt = "This master agreement has additional information as a prime vendor.<br><br> Click this icon to view this contract as a prime vendor. ";
      $url = "/contract_details/magid/" . RequestUtilities::getTransactionsParams('magid') . "/datasource/checkbook_oge/doctype/MMA1/newwindow";
      return  "<div class='contractLinkNote contractIcon'><a class='new_window' href='". $url ."' alt='" . $alt_txt . "' >Open in New Window</a></div>";
    }
    elseif (!preg_match('/newwindow/', \Drupal::request()->query->get('q')) && EdcUtilities::_checkbook_is_oge_parent_contract($node->data[0]['contract_number']) && $node->data_source_amounts_differ) {
      $alt_txt = "This master agreement has additional information as an agency <br><br> Click this icon to view this contract as an agency ";
      $url = "/contract_details/magid/" .  RequestUtilities::getTransactionsParams('magid') . "/doctype/MMA1/datasource/checkbook_oge/newwindow";
      return "<div class='contractLinkNote contractIcon'><a class='new_window' href='". $url ."' alt='" . $alt_txt . "' >Open in New Window</a></div>";
    }
  }

  public function contracts_cta_history_table_body($node) {
    $output = '';
    $sortedArray = array();
    $currentFY = $node->data[0]['source_updated_fiscal_year'];
    $reg_date = $node->data[count($node->data) - 1]['date@checkbook:date_id/registered_date_id@checkbook:history_agreement'];
    foreach ($node->data as $row) {
      if (isset($row['original_maximum_amount'])) {
        $row['original_contract_amount'] = $row['original_maximum_amount'];
        $row['maximum_contract_amount'] = $row['revised_maximum_amount'];
      }
      if($row['status'] != 'Registered' || isset($row['source_updated_fiscal_year'])){
        $sortedArray[$row['source_updated_fiscal_year']][] = $row;
      }
    }
    if (is_array($sortedArray) && (count($sortedArray) > 0)) {
      //TODO To be clarified
      $keys = array_keys($sortedArray);
      $lastKey = $keys[sizeof($sortedArray) - 1];
      $lastFYArray = $sortedArray[$lastKey];
      //$sortedArray[$lastKey][sizeof($sortedArray[$lastKey])-1]['updated_date'] = $sortedArray[$lastKey][sizeof($sortedArray[$lastKey])-1]['date@checkbook:date_id/registered_date_id@checkbook:history_agreement'];

      $showCondition = "";

      $count1 = 0;

      //      log_error($sortedArray);

      foreach ($sortedArray as $key => $items) {
        if ($key != null) {
          if ($count1 % 2 == 0) {
            $class1 = "odd";
          }
          else {
            $class1 = "even";
          }
          $output .= "<tr class='outer " . $class1 . "'>";
          $output .= "<td class='text'><div><a class=\"showHide $showClass\"></a> FY " . $key . "</div></td>";
          $output .= "<td class='text'><div>" . count($items) . " Modifications</div></td>";
          $showClass = 'open';
          $orig_amount_sum = 0;
          $curr_amount_sum = 0;
          foreach ($items as $item) {
            $curr_amount_sum = $item['maximum_contract_amount'];
            $orig_amount_sum = $item['original_contract_amount'];
            break;
          }
          $output .= "<td class='number'><div>" . FormattingUtilities::custom_number_formatter_format($curr_amount_sum, 2, '$') . "</div></td>";
          $output .= "<td class='number endCol'><div>" . FormattingUtilities::custom_number_formatter_format($orig_amount_sum, 2, '$') . "</div></td>";
          $output .= "</tr>";
          $count1 += 1;
          $output .= "<tr id='showHidectahis" . $key . "' class='showHide " . $class1 . "' " . $showCondition . ">";
          $showCondition = "style='display:none'";
          $output .= "<td colspan='4' >";
          $output .= "<div class='scroll'>";
          $output .= "<table class='sub-table col9'>";
          $output .= "<thead>
                  <tr>
                    <th class='number thVNum'>".WidgetUtil::generateLabelMapping("oca_number")."</th>
                    <th class='number thVNum'>".WidgetUtil::generateLabelMapping("version_number")."</th>
                    <th class='text thStartDate'>".WidgetUtil::generateLabelMapping("start_date")."</th>
                    <th class='text thEndDate'>".WidgetUtil::generateLabelMapping("end_date")."</th>
                    <th class='text thRegDate'>".WidgetUtil::generateLabelMapping("reg_date")."</th>
                    <th class='text thLastMDate'>".WidgetUtil::generateLabelMapping("last_mod_date")."</th>
                    <th class='number thCurAmt'>".WidgetUtil::generateLabelMapping("current_amount")."</th>
                    <th class='number thOrigAmt'>".WidgetUtil::generateLabelMapping("original_amount")."</th>
                    <th class='number thIncDec'>".WidgetUtil::generateLabelMapping("increase_decrease")."</th>
                    <th class='text thVerStat status'>".WidgetUtil::generateLabelMapping("version_status")."</th>
                  </tr></thead><tbody>";
          $count = 0;

          foreach ($items as $item) {
            if ($count % 2 == 0) {
              $class = "class=\"odd\"";
            }
            else {
              $class = "class=\"even\"";
            }
            $output .= "<tr " . $class . ">";
            $output .= "<td class='number thVNum' ><div>" . $item['oca_number'] . "</div></td>";
            $output .= "<td class='number thVNum' ><div>" . $item['document_version'] . "</div></td>";
            $output .= "<td class='text thStartDate'><div>" . $item['start_date'] . "</div></td>";
            $output .= "<td class='text thEndDate'><div>" . $item['end_date'] . "</div></td>";
            $output .= "<td class='text thRegDate'><div>" . $reg_date . "</div></td>";
            if (isset($item['cif_received_date'])) {
              $output .= "<td class='text thLastMDate'><div>" . $item['cif_received_date'] . "</div></td>";
            }
            elseif (trim($item['document_version']) == "1") {
              $output .= "<td class='thLastMDate'><div></div></td>";
            }
            else {
              $output .= "<td class='text thLastMDate'><div>" . $item['date@checkbook:date_id/source_updated_date_id@checkbook:history_agreement'] . "</div></td>";
            }
            $output .= "<td class='number thCurAmt' ><div>" . FormattingUtilities::custom_number_formatter_format($item['maximum_contract_amount'], 2, '$') . "</div></td>";
            $output .= "<td class='number thOrigAmt' ><div>" . FormattingUtilities::custom_number_formatter_format($item['original_contract_amount'], 2, '$') . "</div></td>";
            $output .= "<td class='number thIncDec' ><div>" . FormattingUtilities::custom_number_formatter_format(($item['maximum_contract_amount'] - $item['original_contract_amount']), 2, '$') . "</div></td>";
            $output .= "<td class='text thVerStat'><div>" . $item['status'] . "</div></td>";
            $output .= "</tr>";
            $count += 1;
          }
          $output .= "</tbody>";
          $output .= "</table></div>";
          $output .= "</td>";
          $output .= "</tr>";
        }
      }
    }
    else {
      $output .= '<tr class="odd">';
      $output .= '<td class="dataTables_empty" valign="top" colspan="4">' .
        '<div id="no-records-datatable" class="clearfix">
                 <span>No Matching Records Found</span>
           </div>' . '</td>';
      $output .= '</tr>';
    }
    return $output;
  }

  public function contracts_cta_spending_bottom_page($node) {
    $html_output = '';
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

      $vendor_contract_summary[$spending_row['vendor_name']]['check_amount'] += $spending_row['check_amount'];
      $vendor_spending_yearly_summary[$spending_row['sub_contract_id']][$spending_row['fiscal_year']]['no_of_trans'] += 1;
      $vendor_spending_yearly_summary[$spending_row['sub_contract_id']][$spending_row['fiscal_year']]['amount_spent'] += $spending_row['check_amount'];

    }

    /* SPENDING BY SUB VENDOR */

    //Main table header
    $tbl_spending['header']['title'] = "<h3>SPENDING BY SUB VENDOR</h3>";

    if(RequestUtilities::getTransactionsParams("doctype")=="CT1" || RequestUtilities::getTransactionsParams("doctype")=="CTA1"){
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

    $results4 = _checkbook_project_execute_sql_by_data_source($querySubVendorinfo,Datasource::getCurrent());
    $res = new StdClass();
    $res->data = $results4;

    $total_current_amount = $res->data[0]['total_current_amt'];
    $total_original_amount = $res->data[0]['total_original_amt'];
    $total_spent_todate = $res->data[0]['total_spent_todate'];

    $html_output .= "<div class='dollar-amounts'>";
    $total_spent_todate_number = FormattingUtilities::custom_number_formatter_format($total_spent_todate, 2, '$');
    $html_output .= "<div class='spent-to-date'>$total_spent_todate_number";
      $html_output .= "<div class='amount-title'>Total Spent to Date</div>";
    $html_output .= "</div>";
    $total_original_amount_number = FormattingUtilities::custom_number_formatter_format($total_original_amount, 2, '$');
    $html_output .= "<div class='original-amount'>$total_original_amount_number";
      $html_output .= "<div class='amount-title'>Total Original Amount</div>";
    $html_output .= "</div>";
    $total_current_amount_number = FormattingUtilities::custom_number_formatter_format($total_current_amount, 2, '$');
    $html_output .= "<div class='current-amount'>$total_current_amount_number";
      $html_output .= "<div class='amount-title'>Total Current Amount</div>";
    $html_output .= "</div></div>";

    $index_spending = 0;
    foreach ($vendor_contract_summary as $vendor => $vendor_summary) {

      $original_amount = $vendor_summary['original_amount'];
      $current_amount = $vendor_summary['current_amount'];

      $open = $index_spending == 0 ? '' : 'open';

      //Main table columns

      if(RequestUtilities::getTransactionsParams("doctype")=="CT1" || RequestUtilities::getTransactionsParams("doctype")=="CTA1"){
        $vendor_where = isset($vendor_summary['sub_vendor_id'])? " AND a.vendor_id = '" . $vendor_summary['sub_vendor_id']."'" : "";

          $querySubVendorStatusInPIP = "SELECT
                                        c.aprv_sta_id,
                                        c.aprv_sta_value AS sub_vendor_status_pip
                                    FROM sub_agreement_snapshot a
                                    LEFT JOIN subcontract_approval_status c ON c.aprv_sta_id = COALESCE(a.aprv_sta,6)
                                    WHERE a.latest_flag = 'Y'
                                    AND a.contract_number = '" . $contract_number ."'"
            . $vendor_where
            . " ORDER BY c.sort_order ASC LIMIT 1";

          $results5 = _checkbook_project_execute_sql_by_data_source($querySubVendorStatusInPIP, Datasource::getCurrent());
          $result = new StdClass();
          $result->data = $results5;
          $subVendorStatusInPIP = ($result->data[0]['aprv_sta_id'] == 4 && $vendor_summary['check_amount'] == 0) ? "No Subcontract Payments Submitted" : $result->data[0]['sub_vendor_status_pip'];

        if((is_countable($sub_contract_reference[$vendor]) && count($sub_contract_reference[$vendor]) > 1) && $index_spending == 0){
          $viewAll = "<a class='subContractViewAll'>Hide All<<</a>";
        }else{
          $viewAll = (is_countable($sub_contract_reference[$vendor]) && count($sub_contract_reference[$vendor]) > 1) ? "<a class='subContractViewAll'>View All>></a>" : '';
        }

        $tbl_spending['body']['rows'][$index_spending]['columns'] = array(
          array('value' => "<a class='showHide " . $open . " expandTwo' ></a>" . $vendor, 'type' => 'text'),
          array('value' => MappingUtil::getMinorityCategoryById($vendor_summary['minority_type_id']), 'type' => 'text'),
          array('value' => $subVendorStatusInPIP . $viewAll, 'type' => 'text'),
          array('value' => FormattingUtilities::custom_number_formatter_format($current_amount, 2, '$'), 'type' => 'number'),
          array('value' => FormattingUtilities::custom_number_formatter_format($original_amount, 2, '$'), 'type' => 'number'),
          array('value' => FormattingUtilities::custom_number_formatter_format($vendor_summary['check_amount'], 2, '$'), 'type' => 'number')
        );
      }else{
        $tbl_spending['body']['rows'][$index_spending]['columns'] = array(
          array('value' => "<a class='showHide " . $open . " expandTwo' ></a>" . $vendor, 'type' => 'text'),
          array('value' => MappingUtil::getMinorityCategoryById($vendor_summary['minority_type_id']), 'type' => 'text'),
          array('value' => FormattingUtilities::custom_number_formatter_format($current_amount, 2, '$'), 'type' => 'number'),
          array('value' => FormattingUtilities::custom_number_formatter_format($original_amount, 2, '$'), 'type' => 'number'),
          array('value' => FormattingUtilities::custom_number_formatter_format($vendor_summary['check_amount'], 2, '$'), 'type' => 'number')
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
                                    AND a.contract_number = '" . $contract_number ."'"
            . $vendor_where
            . " AND a.sub_contract_id ='" . $reference_id
            . "' ORDER BY c.sort_order ASC LIMIT 1";

          $results6 = _checkbook_project_execute_sql_by_data_source($querySubContractStatusInPIP, Datasource::getCurrent());
          $result = new StdClass();
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

        if(is_countable($vendor_contract_yearly_summary[$ref_id]) && count($vendor_contract_yearly_summary[$ref_id]) > 0){
          foreach ($vendor_contract_yearly_summary[$ref_id] as $year => $results_contract_history_fy) {

            $open = $index_contract_history == 0 ? '' : 'open';
            //Main table columns
            $tbl_contract_history['body']['rows'][$index_contract_history]['columns'] = array(
              array('value' => "<a class='showHide " . $open . "' ></a>FY " . $year, 'type' => 'text'),
              array('value' => $results_contract_history_fy['no_of_mods'], 'type' => 'number'),
              array('value' => FormattingUtilities::custom_number_formatter_format($results_contract_history_fy['current_amount'], 2, '$'), 'type' => 'number'),
              array('value' => FormattingUtilities::custom_number_formatter_format($results_contract_history_fy['original_amount'], 2, '$'), 'type' => 'number'),
              // array('value' => FormattingUtilities::custom_number_formatter_format($results_contract_history_fy['current_amount'] - $results_contract_history_fy['original_amount'], 2, '$'), 'type' => 'number')
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
                  array('value' => FormattingUtilities::custom_number_formatter_format($contract_history['maximum_contract_amount'], 2, '$'), 'type' => 'number'),
                  array('value' => FormattingUtilities::custom_number_formatter_format($contract_history['original_contract_amount'], 2, '$'), 'type' => 'number'),
                  array('value' => FormattingUtilities::custom_number_formatter_format($contract_history['maximum_contract_amount'] - $contract_history['original_contract_amount'], 2, '$'), 'type' => 'number')
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
        if ((!is_null($vendor_spending_yearly_summary[$ref_id])) && count($vendor_spending_yearly_summary[$ref_id]) > 0) {
          foreach ($vendor_spending_yearly_summary[$ref_id] as $year => $results_spending_history_fy) {

            $open = $index_spending_transaction == 0 ? '' : 'open';
            //Main table columns
            $tbl_spending_transaction['body']['rows'][$index_spending_transaction]['columns'] = array(
              array('value' => "<a class='showHide " . $open . "' ></a>FY " . $year, 'type' => 'text'),
              array('value' => $results_spending_history_fy['no_of_trans'], 'type' => 'number'),
              array('value' => FormattingUtilities::custom_number_formatter_format($results_spending_history_fy['amount_spent'], 2, '$'), 'type' => 'number')
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
                  array('value' => FormattingUtilities::custom_number_formatter_format($contract_spending['check_amount'], 2, '$'), 'type' => 'number'),
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
    $html_output .= "<div class='contracts-spending-bottom'>" . WidgetUtil::generateTable($tbl_spending) . "</div>" ;
    return $html_output;
  }

  public function contracts_cta_spending_by_exp_cat_table_body($node) {
    if ( RequestUtilities::get("datasource") == "checkbook_oge") {
      $datasource ="/datasource/checkbook_oge";
    }

    //Main table header
    $tbl['header']['title'] = "<h3>Spending by Expense Category</h3>";
    $tbl['header']['columns'] = array(
      array('value' => WidgetUtil::generateLabelMappingNoDiv("expense_category"), 'type' => 'text'),
      array('value' => WidgetUtil::generateLabelMappingNoDiv("encumbered_amount"), 'type' => 'number'),
      array('value' => WidgetUtil::generateLabelMappingNoDiv("spent_to_date"), 'type' => 'number')
    );
    $count = 0;
    if (count($node->data) > 0) {
      foreach ($node->data as $row) {

        $spent_to_date_value = FormattingUtilities::custom_number_formatter_format($row['spending_amount'], 2, '$');
        $spent_to_date = FormattingUtilities::custom_number_formatter_format($row['spending_amount'], 2, '$');

        //Main table columns
        $tbl['body']['rows'][$count]['columns'] = array(
          array('value' => $row['expenditure_object_name'], 'type' => 'text'),
          array('value' => FormattingUtilities::custom_number_formatter_format($row['encumbered_amount'], 2, '$'), 'type' => 'number'),
          array('value' => $spent_to_date_value, 'type' => 'number_link', 'link_value' => $spent_to_date)
        );
        $count += 1;
      }
    }

    $html = WidgetUtil::generateTable($tbl);
    return $html;
  }

  public function contracts_cta_spending_history_table_body($node) {
    $output = '';
    $sortedArray = array();
    $currentFY = $node->data[0]['fiscal_year'];
    foreach ($node->data as $row) {
      $sortedArray[$row['fiscal_year']][] = $row;
    }
    if (is_array($sortedArray) && count($sortedArray) > 0) {
      $showCondition = "";
      //$showClass = 'close';
      $count1 = 0;
      foreach ($sortedArray as $key => $items) {
        if ($key != null) {
          if ($count1 % 2 == 0) {
            $class1 = "odd";
          }
          else {
            $class1 = "even";
          }
          $output .= "<tr class='outer " . $class1 . "'>";
          $output .= "<td class='text'><div><a class=\"showHide $showClass\"></a> FY " . $key . "</div></td>";
          $output .= "<td class='text'><div>" . count($items) . " Transactions</div></td>";
          $showClass = 'open';
          $check_amount_sum = 0;
          foreach ($items as $item) {
            $check_amount_sum += $item['check_amount'];
          }
          $output .= "<td class='number endCol'><div>" . FormattingUtilities::custom_number_formatter_format($check_amount_sum, 2, '$') . "</div></td>";
          $output .= "</tr>";
          $count1 += 1;
          $output .= "<tr id='showHidectaspe" . $key . "' class='showHide " . $class1 . "' " . $showCondition . ">";
          $showCondition = "style='display:none'";
          $output .= "<td colspan='3' >";
          $output .= "<div class='scroll'>";
          $output .= "<table class='sub-table col6'>";
          $output .= "<thead><tr><th class='text th1'><div><span>Date</span></div></th>
                           <th class='text th2'>". WidgetUtil::generateLabelMapping("document_id")."</th>
                           <th class='number th3'>". WidgetUtil::generateLabelMapping("check_amount")."</th>
                           <th class='text th4'>". WidgetUtil::generateLabelMapping("expense_category")."</th>
                           <th class='text th5'>". WidgetUtil::generateLabelMapping("agency_name")."</th>
                           <th class='text th6'>". WidgetUtil::generateLabelMapping("dept_name")."</th></tr></thead><tbody>";
          $count = 0;
          foreach ($items as $item) {
            if ($count % 2 == 0) {
              $class = "class=\"odd\"";
            }
            else {
              $class = "class=\"even\"";
            }
            $output .= "<tr " . $class . ">";
            $output .= "<td class='text td1'><div>" . $item['date@checkbook:date_id/check_eft_issued_date_id@checkbook:disbursement_line_item_details'] . "</div></td>";
            $output .= "<td class='text td2'><div>" . $item['document_id'] . "</div></td>";
            $output .= "<td class='number td3'><div>" . FormattingUtilities::custom_number_formatter_format($item['check_amount'], 2, '$') . "</div></td>";
            $output .= "<td class='text td4'><div>" . $item['expenditure_object_name'] . "</div></td>";
            $output .= "<td class='text td5'><div>" . $item['agency_name'] . "</div></td>";
            $output .= "<td class='text td6'><div>" . $item['department_name'] . "</div></td>";
            $output .= "</tr>";
            $count += 1;
          }
          $output .= "</tbody>";
          $output .= "</table></div>";
          $output .= "</td>";
          $output .= "</tr>";
        }
      }
    }
    else {
      $output .= '<tr class="odd">';
      $output .= '<td class="dataTables_empty" valign="top" colspan="3">' .
        '<div id="no-records-datatable" class="clearfix">
                 <span>No Matching Records Found</span>
           </div>' . '</td>';
      $output .= '</tr>';
    }
    return $output;
  }

  public function contracts_ma_history_table_body($node) {
    $output = '';
    $sortedArray = array();

    foreach ($node->data as $row) {
      if (isset($row['original_maximum_amount'])) {
        $row['original_contract_amount'] = $row['original_maximum_amount'];
        $row['maximum_spending_limit'] = $row['revised_maximum_amount'];
      }
      $sortedArray[$row['source_updated_fiscal_year']][] = $row;
    }

    if (count($sortedArray) > 0 && !isset($sortedArray[""])) {
      $currentFY = $node->data[0]['source_updated_fiscal_year'];
      $reg_date = $node->data[count($node->data) - 1]['date@checkbook:date_id/registered_date_id@checkbook:history_master_agreement'];
      // $node->data[sizeof($sortedArray)-1]['updated_date'] = $node->data[sizeof($sortedArray)-1]['date@checkbook:date_id/registered_date_id@checkbook:history_master_agreement'];

      //TODO To be clarified
      $keys = array_keys($sortedArray);
      $lastKey = $keys[sizeof($sortedArray) - 1];
      $lastFYArray = $sortedArray[$lastKey];
      $sortedArray[$lastKey][sizeof($sortedArray[$lastKey]) - 1]['updated_date'] = $sortedArray[$lastKey][sizeof($sortedArray[$lastKey]) - 1]['date@checkbook:date_id/registered_date_id@checkbook:history_master_agreement'];
      $showCondition = "";
      $count1 = 0;
      foreach ($sortedArray as $key => $items) {
        if ($key != null) {
          if ($count1 % 2 == 0) {
            $class1 = "odd";
          }
          else {
            $class1 = "even";
          }
          $output .= "<tr class='outer " . $class1 . "'>";
          $output .= "<td class='text'><div><a class=\"showHide $showClass\"></a> FY " . $key . "</div></td>";
          $output .= "<td class='text'><div>" . count($items) . " Modifications</div></td>";
          $showClass = 'open';
          $curr_amount_sum = 0;
          $orig_amount_sum = 0;
          foreach ($items as $item) {
            $curr_amount_sum = $item['maximum_spending_limit'];
            $orig_amount_sum = $item['original_contract_amount'];
            break;
          }
          $output .= "<td class='number'><div>" . FormattingUtilities::custom_number_formatter_format($curr_amount_sum, 2, '$') . "</div></td>";
          $output .= "<td class='number'><div>" . FormattingUtilities::custom_number_formatter_format($orig_amount_sum, 2, '$') . "</div></td>";
          $output .= "</tr>";
          $count1 += 1;
          $output .= "<tr id='showHidemashis" . $key . "' class='showHide " . $class1 . "' " . $showCondition . ">";
          $showCondition = "style='display:none'";
          $output .= "<td colspan='4' >";
          $output .= "<div class='scroll'>";
          $output .= "<table class='sub-table col9'>";
          //For IE9, tables cannot have line breaks between table elements
          $output .= "<thead>";

          $output .= "<tr>";
          $output .=  "<th class='number thVNum'>";
          $output .= WidgetUtil::generateLabelMapping("oca_number");
          $output .=  "</th>";
          $output .=  "<th class='number thVNum'>";
          $output .= WidgetUtil::generateLabelMapping("version_number");
          $output .=  "</th>";
          $output .=  "<th class='text thStartDate'>";
          $output .= WidgetUtil::generateLabelMapping("start_date");
          $output .= "</th>";
          $output .=  "<th class='text thEndDate'>";
          $output .= WidgetUtil::generateLabelMapping("end_date");
          $output .=  "</th>";
          $output .=  "<th class='text thRegDate'>";
          $output .= WidgetUtil::generateLabelMapping("reg_date");
          $output .=  "</th>";
          $output .=  "<th class='text thLastMDate'>";
          $output .= WidgetUtil::generateLabelMapping("last_mod_date");
          $output .=  "</th>";
          $output .=  "<th class='number thCurAmt'>";
          $output .= WidgetUtil::generateLabelMapping("current_amount");
          $output .=  "</th>";
          $output .=  "<th class='number thOrigAmt'>";
          $output .= WidgetUtil::generateLabelMapping("original_amount");
          $output .=  "</th>";
          $output .=  "<th class='number thIncDec'>";
          $output .= WidgetUtil::generateLabelMapping("increase_decrease");
          $output .=  "</th>";
          $output .=  "<th class='text thVerStat'>";
          $output .= WidgetUtil::generateLabelMapping("version_status");
          $output .=  "</th>";
          $output .= "</tr>";

          $output .= "</thead><tbody>";
          $count = 0;
          foreach ($items as $item) {
            if ($count % 2 == 0) {
              $class = "class=\"inner odd\"";
            }
            else {
              $class = "class=\"inner even\"";
            }
            $output .= "<tr " . $class . ">";
            $output .= "<td class='number thVNum'><div>" . $item['oca_number'] . "</div></td>";
            $output .= "<td class='number thVNum'><div>" . $item['document_version'] . "</div></td>";
            $output .= "<td class='text thStartDate'><div>" . $item['start_date'] . "</div></td>";
            $output .= "<td class='text thEndDate'><div>" . $item['end_date'] . "</div></td>";
            $output .= "<td class='text thRegDate'><div>" . $reg_date . "</div></td>";

            if (isset($item['cif_received_date'])) {
              $output .= "<td class='text thLastMDate'><div>" . $item['cif_received_date'] . "</div></td>";
            }
            elseif (trim($item['document_version']) == "1") {
              $output .= "<td></td>";
            }
            else {
              $output .= "<td class='text thLastMDate'><div>" . $item['date@checkbook:date_id/source_updated_date_id@checkbook:history_master_agreement'] . "</div></td>";
            }
            $output .= "<td class='number thCurAmt'><div>" . FormattingUtilities::custom_number_formatter_format($item['maximum_spending_limit'], 2, '$') . "</div></td>";
            $output .= "<td class='number thOrigAmt'><div>" . FormattingUtilities::custom_number_formatter_format($item['original_contract_amount'], 2, '$') . "</div></td>";
            $output .= "<td class='number thIncDec'><div>" . FormattingUtilities::custom_number_formatter_format(($item['maximum_spending_limit'] - $item['original_contract_amount']), 2, '$') . "</div></td>";
            $output .= "<td class='text thVerStat'><div>" . $item['status'] . "</div></td>";
            $output .= "</tr>";
            $count += 1;
          }
          $output .= "</tbody>";
          $output .= "</table>";
          $output .= "</div>";
          $output .= "</td>";
          $output .= "</tr>";
        }
      }
    }
    else {
      $output .= '<tr class="odd">';
      $output .= '<td class="dataTables_empty" valign="top" colspan="4">' .
        '<div id="no-records-datatable" class="clearfix">
                 <span>No Matching Records Found</span>
           </div>' . '</td>';
      $output .= '</tr>';
    }
    return $output;
  }

  public function contracts_oge_cta_all_vendor_info_page($node) {
    //Main table header
    $tbl['header']['title'] = "<div class='tableHeader'><h3>Prime Vendor Information<span class='contCount superscript'>Number of Prime Vendors: ".count($node->vendors_list)." </span></h3></div>";
    $tbl['header']['columns'] = array(
      array('value' => WidgetUtil::generateLabelMappingNoDiv("prime_vendor_name"), 'type' => 'text'),
      array('value' => $node->widget_count_label, 'type' => 'number'),
      array('value' => WidgetUtil::generateLabelMappingNoDiv("spent_to_date"), 'type' => 'number'),
      array('value' => WidgetUtil::generateLabelMappingNoDiv("prime_vendor_address"), 'type' => 'text')
    );

    //ticket NYCCHKBK-13156 - moved out of for loop
    $current_fiscal_year_id = CheckbookDateUtil::getCurrentFiscalYearId();

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

        if(preg_match("/newwindow/",\Drupal::request()->query->get('q'))) {
          $vendor_name = $vendor['vendor_name'];
        }
        else {
          $vendor_name =  "<a href='/contracts_landing/status/A/year/" . $current_fiscal_year_id . "/yeartype/B/agency/" . $vendor['agency_id'] .
            "/datasource/checkbook_oge/vendor/" . $vendor['vendor_id']  . "'>" . $vendor['vendor_name']  . "</a>";
        }

        $spent_to_date_value =  FormattingUtilities::custom_number_formatter_format($vendor['check_amount_sum'], 2, '$');
        if(preg_match("/newwindow/",\Drupal::request()->query->get('q'))) {
          $spent_to_date_link =  FormattingUtilities::custom_number_formatter_format($vendor['check_amount_sum'], 2, '$');
        }
        else {
          $spent_to_date_link = "<a class='new_window' target='_new' href='" . $spending_link . "'>" . FormattingUtilities::custom_number_formatter_format($vendor['check_amount_sum'], 2, '$')  . "</a>";
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
    return $html;
  }

  public function contracts_oge_cta_spending_bottom_page($node) {
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
        array('value' => FormattingUtilities::custom_number_formatter_format($vendor_summary['current_amount'], 2, '$'), 'type' => 'number'),
        array('value' => FormattingUtilities::custom_number_formatter_format($vendor_summary['original_amount'], 2, '$'), 'type' => 'number'),
        array('value' => FormattingUtilities::custom_number_formatter_format($vendor_summary['check_amount'], 2, '$'), 'type' => 'number')
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
          array('value' => FormattingUtilities::custom_number_formatter_format($results_contract_history_fy['current_amount'], 2, '$'), 'type' => 'number'),
          array('value' => FormattingUtilities::custom_number_formatter_format($results_contract_history_fy['original_amount'], 2, '$'), 'type' => 'number'),
          array('value' => FormattingUtilities::custom_number_formatter_format($results_contract_history_fy['current_amount'] - $results_contract_history_fy['original_amount'], 2, '$'), 'type' => 'number')
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
              array('value' => FormattingUtilities::custom_number_formatter_format($contract_history['current_amount_commodity_level'], 2, '$'), 'type' => 'number'),
              array('value' => FormattingUtilities::custom_number_formatter_format($contract_history['original_amount'], 2, '$'), 'type' => 'number'),
              array('value' => FormattingUtilities::custom_number_formatter_format($contract_history['current_amount_commodity_level'] - $contract_history['original_amount'], 2, '$'), 'type' => 'number')
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
      if (is_countable($vendor_spending_yearly_summary[$vendor]) && count($vendor_spending_yearly_summary[$vendor]) > 0) {
        foreach ($vendor_spending_yearly_summary[$vendor] as $year => $results_spending_history_fy) {

          $open = $index_spending_transaction == 0 ? '' : 'open';
          //Main table columns
          $tbl_spending_transaction['body']['rows'][$index_spending_transaction]['columns'] = array(
            array('value' => "<a class='showHide " . $open . "' ></a>FY " . $year, 'type' => 'text'),
            array('value' => $results_spending_history_fy['no_of_trans'], 'type' => 'number'),
            array('value' => FormattingUtilities::custom_number_formatter_format($results_spending_history_fy['amount_spent'], 2, '$'), 'type' => 'number')
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
                array('value' => FormattingUtilities::custom_number_formatter_format($contract_spending['check_amount'], 2, '$'), 'type' => 'number'),
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
    return $html;
  }

  public function contracts_ca_details_spending_link($node) {
    if(Datasource::getCurrent() != Datasource::CITYWIDE && !_checkbook_check_isEDCPage()) {
      $results = $this->_contracts_ca_details_spending_results($node);
      $label = '% of contract expenses budgeted for Covid or Asylum Seekers';
      echo $results ? '<a href="#" class="contracts-details-spending-link">' . $label . '</a>' : '<span class="contracts-details-spending-link">' . $label . '</span>';
    }
  }

  public function contracts_ca_details_spending_table($node) {
    $results = $this->_contracts_ca_details_spending_results($node);
    if ($results) {
      $header = [];
      foreach ($results[0] as $key => $value) {
        switch ($key) {
          case 'contract_number':
            $key = 'Contract number';
            break;
          case 'spending_amount':
            $key = 'Spending amount';
            break;
          case 'maximum_contract_amount':
            $key = 'Maximum contract amount';
            break;
          case 'budget_category':
            $key = 'Budget category';
            break;
          case 'percent_spent':
            $key = '% Spend';
            break;
          default:
            break;
        }
        $header[] = $key;
      }

      $rows = [];
      $index = 0;
      foreach ($results as $result) {
        $row = [];
        foreach ($result as $key => $value) {
          switch ($key) {
            case 'budget_category':
              $value = strtoupper($value);
              break;
            case 'spending_amount':
            case 'maximum_contract_amount':
              $value = number_format($value, 2);
              break;
            case 'percent_spent':
              $value .= '%';
              break;
            default:
              break;
          }
          $row[] = $value;
        }
        $rows[] = [
          'data' => $row,
          'class' => [$index % 2 ? 'odd' : 'even'],
        ];
        $index++;
      }
      $id = 'table_' . widget_unique_identifier($node);
      $table = [
        '#theme' => 'table',
        '#header' => $header,
        '#rows' => $rows,
        '#attributes' => [
          'id' => $id,
          'class' => ['hidden','dataTable', 'outerTable', 'contracts-spending-table'],
        ],
      ];
      echo \Drupal::service('renderer')->render($table);
    }
  }

  public function _contracts_ca_details_spending_results($node) {
    // Execute the query only for Citywide
    if (Datasource::getCurrent() != Datasource::CITYWIDE && !_checkbook_check_isEDCPage() && !empty($node->data[0]['contract_number'])) {
      $query = "SELECT contract_number, budget_category, spending_amount, maximum_contract_amount, percent_spent
                FROM event_contracts_spending WHERE contract_number = '" . $node->data[0]['contract_number'] . "'";
      return _checkbook_project_execute_sql_by_data_source($query, Datasource::getCurrent());
    }
  }
}
