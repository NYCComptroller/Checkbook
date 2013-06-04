<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/
?>
<?php
    print '<div class="dollar-amounts"><div class="total-spending-amount">$' . custom_number_formatter_format($node->data[0]['check_amount_sum'],2).'<div class="amount-title">Total Spending Amount</div></div></div>';