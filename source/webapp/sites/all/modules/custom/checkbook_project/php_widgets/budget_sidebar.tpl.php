<?php
/**
* This file is part of the Checkbook NYC financial transparency software.
* 
* Copyright (C) 2012, 2013 New York City
* 
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU Affero General Public License as
* published by the Free Software Foundation, either version 3 of the
* License, or (at your option) any later version.
* 
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU Affero General Public License for more details.
* 
* You should have received a copy of the GNU Affero General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

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