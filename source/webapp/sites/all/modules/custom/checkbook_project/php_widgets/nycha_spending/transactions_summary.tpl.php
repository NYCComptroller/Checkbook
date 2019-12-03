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

//Transactions Page main title
$title = NychaSpendingUtil::getTransactionsTitle();

//Transactions Page sub title
$url = $_REQUEST['expandBottomContURL'];
$url = isset($url) ? $url : drupal_get_path_alias($_GET['q']);
$AmountSpent = NychaSpendingUtil::getAmountSpent($url);
//print_r($node->data);
if(isset($url)) {
  $widget = RequestUtil::getRequestKeyValueFromURL('widget', $url);
  $cat_id = RequestUtilities::get('category');
  $categoryName = NychaSpendingUtil::getCategoryName();
  $total_spending = $node->data[0]['invoice_amount_sum'];
  $vendor_value = RequestUtilities::get('vendor_inv');

  // Widget Details page static text
  if (strpos($widget, 'wt_') !== false) {
    if ($categoryName == 'Total'){$total_spending = NychaSpendingUtil::getTotalSpendingAmount($categoryName,$url);}
    if ($categoryName == 'Payroll'){$total_spending = $node->data[0]['check_amount_sum'];}
    $aggregatedAmountTitle = $categoryName." Spending Amount";
  }
  // Widget YTD page static text
  if(strpos($widget, 'ytd_') !== false){
    if ($categoryName == 'Payroll'){$total_spending = $node->data[0]['check_amount_sum'];}
    $aggregatedAmountTitle = WidgetUtil::getLabel("ytd_spending");
    $wtitle = NychaSpendingUtil::getTransactionsSubTitle($widget, $url);
    if ($widget != 'ytd_contract'){
      $subTitle2 = "<div class='spending-tx-subtitle'>{$wtitle}</div>";
    }
    if ($widget == 'ytd_contract' || $widget == 'ytd_vendor'){
      $summaryDetails = NychaSpendingUtil::getTransactionsStaticSummary($widget, $url);

      if($widget == 'ytd_contract') {
        $contractSummary = "<div class='contract-information contract-summary-block'>
                        <ul>
                          <li class=\"spendingtxsubtitle\">
	                            <span class=\"gi-list-item\"><b>Contract ID:</b></span> {$summaryDetails['contract_id']}
                          </li>
                          <li class=\"spendingtxsubtitle\">
	                            <span class=\"gi-list-item\"><b>Purpose:</b></span> {$summaryDetails['contract_purpose']}
                          </li>
                          <li class=\"spendingtxsubtitle\">
                              <span class=\"gi-list-item\"><b>Vendor:</b></span> {$summaryDetails['vendor_name']}
                          </li>
                        </ul>
                      </div>";
        $subTitle2 = $contractSummary;
      }
      $aggregatedAmount = '$' . custom_number_formatter_format($summaryDetails['total_contract_amount_sum'], 2);
      $aggregateTotalContractTitle = WidgetUtil::getLabel("total_contract_amount");
      $amountSummaryTotalContract =  "<div class=\'total-spending-amount\'>".$aggregatedAmount.
        "<div class=\'amount-title\'>".$aggregateTotalContractTitle."</div>
                                      </div>";
    }

  }

  //Widget Invoiced Amount Link static text
  if ($widget == 'inv_contract') {
    $inv_contractDetails = NychaSpendingUtil::getContractsTransactionsStaticSummary($widget, $url);
    $tcode = RequestUtil::getRequestKeyValueFromURL('tcode', $url);
    $spendtodateTitle = WidgetUtil::getLabel("contract_spend_to_date");
    $spendtodateAmount = '$' . custom_number_formatter_format($inv_contractDetails['spend_to_date'], 2);
    $originalAmountTitle = WidgetUtil::getLabel("original_amount");
    $originalAmount = '$' . custom_number_formatter_format($inv_contractDetails['original_amount'], 2);
    $currentAmountTitle = WidgetUtil::getLabel("current_amount");
    $currentAmount = '$' . custom_number_formatter_format($inv_contractDetails['total_amount'], 2);
    $totalAmountTitle ="NYCHA Amount Spent";
    $AmountSpent = NychaSpendingUtil::getAmountSpent($url);
    $totalAmount = '$' . custom_number_formatter_format($AmountSpent[0]['amount_spent'], 2);
    if ($tcode == 'BA' || $tcode == 'BAM' || $tcode == 'PA'|| $tcode == 'PAM'|| $tcode == 'PO') {
      $inv_contractSummary = "<div class='contract-information contract-summary-block'>
                        <ul>
                          <li class=\"spendingtxsubtitle\">
                      <span class=\"gi-list-item\"><b>Contract ID:</b></span> {$inv_contractDetails['contract_id']}
                          </li>
                          <li class=\"spendingtxsubtitle\">
	                            <span class=\"gi-list-item\"><b>Purpose:</b></span> {$inv_contractDetails['purpose']}
                          </li>
                          <li class=\"spendingtxsubtitle\">
                              <span class=\"gi-list-item\"><b>Vendor:</b></span> {$inv_contractDetails['vendor_name']}
                          </li>
                        </ul>
                      </div>";
      $subTitle2 = $inv_contractSummary;
    }
    // if ($tcode == 'VO' || $tcode == 'AWD' || $tcode == 'DEP'|| $tcode == 'IND'|| $tcode == 'RESC' || $tcode == 'SZ') {
    else{ $tcode_title = NYCHAContractUtil::getTitleByCode($tcode);
      if ( $tcode == 'VO'){ $inv_contractName = $inv_contractDetails['vendor_name'];}
      if ( $tcode == 'AWD'){ $inv_contractName = $inv_contractDetails['award_method_name'];}
      if ( $tcode == 'DEP'){ $inv_contractName = $inv_contractDetails['department_name'];}
      if ( $tcode == 'IND'){ $inv_contractName = $inv_contractDetails['industry_type_name'];}
      if ( $tcode == 'RESC'){ $inv_contractName = $inv_contractDetails['responsibility_center_descr'];}
      if ( $tcode == 'SZ'){ $inv_contractName = $inv_contractDetails['award_size_name'];}
      $inv_contractSummary = "<b>{$tcode_title}:</b> {$inv_contractName}";
      $subTitle2 = "<div class='spending-tx-subtitle'>{$inv_contractSummary}</div>";
    }

    // Static amount display for invoice amount link
    $amountsSummary = "<div class='dollar-amounts' style='width:480px;margin-top:10px;'>
                        <div class='total-spending-amount' style='margin-left:14px'>{$totalAmount}
                          <div class='amount-title'>{$totalAmountTitle}
                        <div class='information'><span class='tooltiptext' style='width:490px;left:-190%;margi-left:-190px;padding-bottom: 0px;'>
                        Amount displayed is the sum of ‘Amount spent’ by NYCHA for the selected FY</span></div></div>
                        </div>
                        <div class='spend-to-date' style='margin-left:14px'>{$spendtodateAmount}
                          <div class='amount-title'>{$spendtodateTitle}</div>
                        </div>
                        <div class='original-amount' style='margin-left:14px'>{$originalAmount}
                          <div class='amount-title'>{$originalAmountTitle}</div>
                        </div>
                        <div class='current-amount' style='margin-left:14px'>{$currentAmount}
                          <div class='amount-title'>{$currentAmountTitle}</div>
                        </div>

                      </div></div>";

  }

  //Contract ID detials link static text
  if ( $widget == 'inv_contract_id')
  {
    $inv_contractDetails = NychaSpendingUtil::getContractsTransactionsStaticSummary($widget, $url);
    $id_title = 'NYCHA Spending Transactions';
    $spendtodateAmount = '$' . custom_number_formatter_format($inv_contractDetails['spend_to_date'], 2);
    $totalAmountTitle ="NYCHA Amount Spent";
    $AmountSpent = NychaSpendingUtil::getAmountSpent($url);
    $totalAmount = '$' . custom_number_formatter_format($AmountSpent[0]['amount_spent'], 2);
    $amountsSummary = "<div class='dollar-amounts' style='width:480px;margin-top:10px;'>
                        <div class='total-spending-amount' style='margin-left:14px'>{$totalAmount}
                          <div class='amount-title'>{$totalAmountTitle}
                        <div class='information'><span class='tooltiptext' style='width:490px;left:-190%;margi-left:-190px;padding-bottom: 0px;'>
                        Amount displayed is the sum of ‘Amount spent’ by NYCHA for the selected FY</span></div></div>
                        </div>
                        <div class='spend-to-date' style='margin-left:14px'>{$spendtodateAmount}
                          <div class='amount-title'>{$spendtodateTitle}</div>
                        </div>
                      </div></div>";
    $titleSummary = "<div class='contract-details-heading'>
                  <div class='contract-id'>
                    <h2 class='contract-title'>{$id_title}</h2>
                    </div>";
  }
  $totalSpendingAmount = '$' . custom_number_formatter_format($total_spending, 2);
}

if ($amountsSummary == null) {
  $amountsSummary = "<div class='dollar-amounts' style='width:480px;margin-top:10px;'>
                        <div class='total-spending-amount'>{$totalSpendingAmount}
                          <div class='amount-title'>{$aggregatedAmountTitle}
                          <div class='information'><span class='tooltiptext'>
                        Amount displayed is the sum of ‘Amount spent’ by NYCHA for the selected FY</span></div></div>
                        </div>" . $amountSummaryTotalContract .
    "</div></div>";
}
if ($titleSummary == null) {
  $titleSummary = "<div class='contract-details-heading'>
                  <div class='contract-id'>
                    <h2 class='contract-title'>{$title}</h2>
                    {$subTitle2}</div>";
}

//DISPLAY Static text
echo $titleSummary . $amountsSummary . $subTitle;

