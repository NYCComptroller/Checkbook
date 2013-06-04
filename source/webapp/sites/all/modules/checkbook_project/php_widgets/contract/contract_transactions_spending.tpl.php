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
 $output .= '<table><tr><th>Detailed Expense Category</th><th>Current Amount</th><th>Amount spent to Date</th></tr>';
foreach($node->data as $key=>$value){
    $output .= '<tr><td>'. $value['category']. '</td><td>'.
               custom_number_formatter_format($value['current_amount'],2,'$').'</td><td>'.
               custom_number_formatter_format($value['spent_to_date'],2,'$').'</td></tr>';
}
 $output .= '</table>';
print $output;