<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/
?>

<h3 class="grid_title"><?= htmlentities($title); ?></h3>
<h3 class="grid_chart_title"><?php print $node->widgetConfig->chartTitle;?></h3>

<?php
$refURL =$_GET['refURL'];
if(!(RequestUtil::isPendingExpenseContractPath($refURL) || RequestUtil::isPendingRevenueContractPath($refURL))){
   echo '<h3 class="grid_year_title">' .  (isset($domain) ? ($domain .' '._getFullYearString()) : _getFullYearString()) . '</h3>';
}else{
    //For UI alignment purpose
    echo '<h3 class="grid_year_title">&nbsp;</h3>';
}
