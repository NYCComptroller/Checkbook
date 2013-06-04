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
 * @file Displays output for Budget-NYC/Agency-Sidebar
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
<div class="field-label">Adopted</div>
<div class="field-items"><?php print $node->data[0]['adopted_format']?></div>
<div class="field-label">Modified</div>
<div class="field-items"><?php print $node->data[0]['current_format']?></div>
<br /><br />
<div class="field-label">Expenditure</div>
<div class="field-items"><?php print $node->data[0]['ytd_format']?></div>
<div class="field-label">Pre-encumbered</div>
<div class="field-items"><?php print $node->data[0]['preencumbered_format']?></div>
<div class="field-label">Encumbered</div>
<div class="field-items"><?php print $node->data[0]['encumbered_format']?></div>
<div class="field-label">Request for Payments</div>
<div class="field-items"><?php print $node->data[0]['rfp_format']?></div>
<div class="field-label">Cash Payments</div>
<div class="field-items"><?php print $node->data[0]['cash_format']?></div>
<div class="field-label">Post Adjustments</div>
<div class="field-items"><?php print $node->data[0]['post_format']?></div>