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
?>

<h3 class="grid_title"><?= $title; ?></h3>
<h3 class="grid_chart_title"><?php print $node->widgetConfig->chartTitle;?></h3>

<?php
$refURL =$_GET['refURL'];
if(!(RequestUtil::isPendingExpenseContractPath($refURL) || RequestUtil::isPendingRevenueContractPath($refURL))){
   echo '<h3 class="grid_year_title">' .  (isset($domain) ? ($domain .' '._getFullYearString()) : _getFullYearString()) . '</h3>';
}else{
    //For UI alignment purpose
    echo '<h3 class="grid_year_title">&nbsp;</h3>';
}
