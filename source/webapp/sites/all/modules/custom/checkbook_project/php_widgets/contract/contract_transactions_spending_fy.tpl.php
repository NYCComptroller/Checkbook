<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/
?>
<?php
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
                      <td>'. '<div>'.  count($transactions[$key]) .'<a class="contract-transactions-toggle" href="#">Show</a></div>' .'</td>
                      <td>'. custom_number_formatter_format($amount_spent, 2, '$') .'</td>
                  </tr>'.$show_data;
      $show_data = ''; $amount_spent = '';
}
$output .= '</table>';
print $output;
