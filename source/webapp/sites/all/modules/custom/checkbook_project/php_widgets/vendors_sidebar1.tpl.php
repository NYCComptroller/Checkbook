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