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

$output = '';
$output .= '<table><tr><th>'. WidgetUtil::getLabel('fiscal_year') .'</th>
                       <th>'. WidgetUtil::getLabel('no_of_transactions') .'</th>
                       <th>'. WidgetUtil::getLabel('contract_status') .'Amount spent to Date</th>
                   </tr>';
$transactions = array();
$count = 1;
foreach($node->data as $key=>$value){
    $transactions[$value['fiscal_year']][$count]['fiscal_year'] = $value['fiscal_year'];
    $transactions[$value['fiscal_year']][$count]['date'] = $value['date'];
    $transactions[$value['fiscal_year']][$count]['expense_id'] = $value['expense_id'];
    $transactions[$value['fiscal_year']][$count]['check_amount'] = $value['check_amount'];
    $count++;
}


foreach($transactions as $key=>$value){

      $show_data .= '<tbody id="hidden_data" style="display:none"><tr><th>Date</th><th>Expense ID</th><th>Amount</th></tr>';
      foreach($value as $a=>$b){
          $amount_spent += $b['check_amount'];
          $show_data .= '<tr><td>'. $b['date'] .'</td>
                             <td>'. $b['expense_id'] .'</td>
                             <td>'. custom_number_formatter_format($b['check_amount'], 2, '$') .'</td>
                         </tr>';
      }
      $show_data .= '</tbody>';

      $output .= '<tr><td>'. $key .'</td>
                      <td>'. '<div>'.  count($transactions[$key]) .'<a class="contract-transactions-toggle" >Show</a></div>' .'</td>
                      <td>'. custom_number_formatter_format($amount_spent, 2, '$') .'</td>
                  </tr>'.$show_data;
      $show_data = ''; $amount_spent = '';
}
$output .= '</table>';
print $output;
