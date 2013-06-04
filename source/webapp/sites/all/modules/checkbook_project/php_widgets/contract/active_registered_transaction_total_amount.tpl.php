<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/
?>
<?php
    /*$contactCategory = _getRequestParamValue('contcat');
    $contactCategoryLabel = 'Expense';
    if($contactCategory == 'revenue'){
        $contactCategoryLabel = 'Revenue';
    }*/

    $contactStatus = _getRequestParamValue('contstatus');
    $contactStatusLabel = 'Active';
    if($contactStatus == 'R'){
        $contactStatusLabel = 'Registered';
    }

       print '<div class="transactions-total-amount">$'
          . custom_number_formatter_format($node->data[0]['total_maximum_contract_amount'],2)
          .'<div class="amount-title">Total '.$contactStatusLabel.' Current Contract Amount</div></div>';
