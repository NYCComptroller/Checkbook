<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/
?>
<?php
/**
 * @file Displays output for Spending-Vendors Landing-Top 5 Vendors by Spending
 *
 * Available variables:
 *
 * - $node
 *   - $node->data
 *
 * @see checkbook_project_theme()
 *
 * @ingroup themeable
 */
?>
<h4>Top 5 Vendors by Spending</h4>
<?php
foreach ($node->data as $key => $value){
    print '<div>'.$value['vendor_vendor_legal_name']. ' - ' . $value['top5vendors_format'].'</div>';
}