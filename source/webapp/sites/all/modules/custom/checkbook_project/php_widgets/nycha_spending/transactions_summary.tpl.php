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
  if (strpos($widget, 'ytd_') !== false) {
    $subTitle = NychaSpendingUtil::getTransactionsSubTitle($widget, $url);
  }
}

//Title section
$titleSummary = "<div class='contract-details-heading'>
                  <div class='contract-id'>
                    <h2 class='contract-title'>{$title}</h2>
                    <div class='spending-tx-subtitle'>{$subTitle}</div>
                  </div>
                </div>";

//Aggregated Amounts section
$aggregatedAmount = '$'.custom_number_formatter_format($node->data[0]['check_amount_sum'],2);
$aggregatedAmountTitle = $categoryName. " Spending Amount";
$amountsSummary = "<div class='dollar-amounts'>
                        <div class='total-spending-amount'>{$aggregatedAmount}
                          <div class='amount-title'>{$aggregatedAmountTitle}</div>
                        </div>
                      </div>";

echo $titleSummary . $amountsSummary;
