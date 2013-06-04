<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/
?>
<?php
    print '<div class="dollar-amounts">';
    print '<div class="total-spending-amount">' . custom_number_formatter_format($node->data[0]['revenue_amount_sum'],2,'$')."<div class='amount-title'>Total Revenue Recognized</div>".'</div>';    
    print '<div class="total-spending-amount">' . custom_number_formatter_format($node->data[0]['current_modified_budget'],2,'$')."<div class='amount-title'>Total Modified Budget</div>".'</div>';
    print '<div class="total-spending-amount">' . custom_number_formatter_format($node->data[0]['adopted_budget'],2,'$')."<div class='amount-title'>Total Adopted Budget</div>" .'</div>' ;    
    print '</div>';