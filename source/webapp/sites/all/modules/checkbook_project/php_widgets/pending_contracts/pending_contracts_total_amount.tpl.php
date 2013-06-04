<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/
?>
<?php
echo "Total Pending Contracts Amount: ".custom_number_formatter_format($node->data[0]['current_amount_sum'],2,'$');
