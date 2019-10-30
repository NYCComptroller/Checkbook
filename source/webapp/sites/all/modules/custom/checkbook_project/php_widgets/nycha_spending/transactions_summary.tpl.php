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
if(isset($url)) {
  $widget = RequestUtil::getRequestKeyValueFromURL('widget', $url);
  // Display static content ytd link transaction pages
  if (strpos($widget, 'ytd_') !== false) {
    $aggregatedYtdTitle = WidgetUtil::getLabel("ytd_spending");
    $aggregatedAmountTitle = WidgetUtil::getLabel("total_contract_amount");
    $wtitle = NychaSpendingUtil::getTransactionsSubTitle($widget, $url);
    //echo $wtitle;
    if ($widget != 'ytd_contract'){ 
      
      $subTitle2 = "<div class='spending-tx-subtitle'>{$wtitle}</div>";

    }

    $summaryDetails = NychaSpendingUtil::getTransactionsStaticSummary($widget, $url);
    $ytdAmount = '$' . custom_number_formatter_format($summaryDetails['check_amount_sum'], 2);
    $aggregatedAmount = '$' . custom_number_formatter_format($summaryDetails['total_contract_amount_sum'], 2);

    if ($widget == 'ytd_contract') {
      $contractDetails = NychaSpendingUtil::getTransactionsStaticSummary($widget, $url);
      $ytdAmount = '$' . custom_number_formatter_format($contractDetails['check_amount_sum'], 2);
      $aggregatedAmount = '$' . custom_number_formatter_format($contractDetails['total_contract_amount'], 2);
      $contractSummary = "<div class='contract-information contract-summary-block'>
                        <ul>
                          <li class=\"spendingtxsubtitle\">
	                            <span class=\"gi-list-item\"><b>Contract ID:</b></span> {$contractDetails['contract_id']}
                          </li>
                          <li class=\"spendingtxsubtitle\">
	                            <span class=\"gi-list-item\"><b>Purpose:</b></span> {$contractDetails['contract_purpose']}
                          </li>
                          <li class=\"spendingtxsubtitle\">
                              <span class=\"gi-list-item\"><b>Vendor:</b></span> {$contractDetails['vendor_name']}
                          </li>
                        </ul>
                      </div>";
      $subTitle2 = $contractSummary;
    }
    
  }
  //Nycha contract invoice amount links transaction pages
  if (strpos($widget, 'inv_') !== false) {
    $inv_contractDetails = NychaSpendingUtil::getContractsTransactionsStaticSummary($widget, $url);
    $tcode = RequestUtil::getRequestKeyValueFromURL('tcode', $url);
    $spendtodateTitle = WidgetUtil::getLabel("contract_spend_to_date");
    $spendtodateAmount = '$' . custom_number_formatter_format($inv_contractDetails['spend_to_date'], 2);
    $originalAmountTitle = WidgetUtil::getLabel("original_amount");
    $originalAmount = '$' . custom_number_formatter_format($inv_contractDetails['original_amount'], 2);
    $currentAmountTitle = WidgetUtil::getLabel("current_amount");
    $currentAmount = '$' . custom_number_formatter_format($inv_contractDetails['total_amount'], 2);
    $totalAmountTitle ="NYCHA Total Amount";
    $totalAmount = '$' . custom_number_formatter_format($inv_contractDetails['test'], 2);
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
  }
  // Display static content for details link transaction pages
  else{
    //$title = NychaSpendingUtil::getTransactionsTitle();
    //$wtitle = NychaSpendingUtil::getTransactionsSubTitle($widget, $url);
    //$subTitle2 = "<div class='spending-tx-subtitle'>{$wtitle}</div>";
    $categoryName = NychaSpendingUtil::getCategoryName();
    $aggregatedAmountTitle = $categoryName." Spending Amount";
    $totalSpendingAmount = '$' . custom_number_formatter_format($node->data[0]['invoice_amount_sum'], 2);
    $amountsSummary = "<div class='dollar-amounts'>
                        <div class='total-spending-amount'>{$totalSpendingAmount}
                          <div class='amount-title'>{$aggregatedAmountTitle} 
                          <div class='information'><span class='tooltiptext'> 
                        Amount displayed is the sum of ‘Amount spent’ by NYCHA for the selected FY</span></div></div>                        
                        </div>
                      </div></div>";

  }
}

//Title section
$titleSummary = "<div class='contract-details-heading'>
                  <div class='contract-id'>
                    <h2 class='contract-title'>{$title}</h2>
                    {$subTitle2}</div>";
if (strpos($widget, 'ytd_') !== false) {
  if (isset($widget) && ($widget == 'ytd_contract' || $widget == 'ytd_vendor')) {
    $amountsSummary = "<div class='dollar-amounts'>
                        <div class='ytd-spending-amount'>{$ytdAmount}
                          <div class='amount-title'>{$aggregatedYtdTitle}
                          <div class='information'><span class='tooltiptext'> 
                        Amount displayed is the sum of ‘Amount spent’ by NYCHA for the selected FY</span></div></div>
                        </div>
                        <div class='total-spending-amount'>{$aggregatedAmount}
                          <div class='amount-title'>{$aggregatedAmountTitle}</div>
                        </div>
                      </div></div>";
  }
  else
  {
    $amountsSummary = "<div class='dollar-amounts'>
                        <div class='ytd-spending-amount'>{$ytdAmount}
                          <div class='amount-title'>{$aggregatedYtdTitle}
                          <div class='information'><span class='tooltiptext'> 
                        Amount displayed is the sum of ‘Amount spent’ by NYCHA for the selected FY</span></div></div>
                        </div>
                      </div></div>";
  }
}

if (strpos($widget, 'inv_') !== false) {
  $amountsSummary = "<div class='dollar-amounts'>
                        <div class='spend-to-date'>{$spendtodateAmount}
                          <div class='amount-title'>{$spendtodateTitle}</div>
                        </div>
                        <div class='original-amount'>{$originalAmount}
                          <div class='amount-title'>{$originalAmountTitle}</div>
                        </div>
                        <div class='current-amount'>{$currentAmount}
                          <div class='amount-title'>{$currentAmountTitle}</div>
                        </div>
                        <div class='total-spending-amount'>{$totalAmount}
                          <div class='amount-title'>{$totalAmountTitle}</div>
                        </div>
                      </div></div>";
}

echo $titleSummary . $amountsSummary . $subTitle;
