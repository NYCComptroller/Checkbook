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
 * @file Displays output for Spending-Vendors Landing-Top 5 Agencies by Spending
 *
 * Available variables:
 * - $node
 *   - $node->data
 *
 * @see checkbook_project_theme()
 *
 * @ingroup themeable
 */
?>
<h2 class="pane-title">City Spending</h2>
<h4>Top 5 Agencies</h4>
<?php
foreach ($node->data as $key => $value){
    print '<div>'.$value['agency_agency_agency_name']. ' - ' . $value['top5agencies_format'].'</div>';
}