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

//Transactions Details Page main title
$title = NychaBudgetUtil::getTransactionsTitle();

//Transactions Page sub title
$url = $_REQUEST['expandBottomContURL'];
$url = isset($url) ? $url : drupal_get_path_alias($_GET['q']);
$widget = RequestUtil::getRequestKeyValueFromURL('widget', $url);

if((strpos($widget, 'comm_') !== false) || ($widget == 'wt_year')) {
  $subTitle = NychaBudgetUtil::getTransactionsSubTitle($widget, $url);
}
$subTitle = isset($subTitle) ? $subTitle : ' ';

$titleSummary = "<div class='contract-details-heading'>
                  <div class='contract-id'>
                    <h2 class='contract-title'>{$title}</h2></br>
                    {$subTitle}</div>";
echo $titleSummary;

// Amount values
print '<div class="dollar-amounts">';
print '<div class="total-spending-amount">' . custom_number_formatter_format($node->data[0]['budget_committed'],2,'$')."<div class='amount-title'>Total Committed<br />Expense Budget</div>".'</div>';
print '<div class="total-spending-amount">' . custom_number_formatter_format($node->data[0]['budget_adopted_amount'],2,'$')."<div class='amount-title'>Total Modified<br />Expense Budget</div>".'</div>';
print '<div class="total-spending-amount">' . custom_number_formatter_format($node->data[0]['budget_adopted_amount'],2,'$')."<div class='amount-title'>Total Adopted<br />Expense Budget</div>" .'</div>' ;
print '<div class="total-spending-amount">' . custom_number_formatter_format($node->data[0]['budget_remaining'],2,'$')."<div class='amount-title'>Total Remaining<br />Expense Budget</div>" .'</div>' ;
print '</div></div>';

