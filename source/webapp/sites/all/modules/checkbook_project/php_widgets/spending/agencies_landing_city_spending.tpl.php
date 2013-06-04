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
foreach($node->data as $key=>$value){
   $sum += $value['check_amount_sum'];
   $output .= $value['category_category_spending_category_name']. ' - ' . custom_number_formatter_format($value['check_amount_sum'],2,'$') .'<br/>';
}
$sum = custom_number_formatter_format($sum,2,'$');
$output = '<h2 class="pane-title">City Spending</h2>'. '<h4>Agencies Spending</h4><h4>'. $sum. '</h4>'. $output;
print $output;