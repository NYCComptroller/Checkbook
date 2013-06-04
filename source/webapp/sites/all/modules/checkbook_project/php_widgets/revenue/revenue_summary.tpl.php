<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/
?>
<?php
$output = '';
$urlPath = drupal_get_path_alias($_GET['q']);
$pathParams = explode('/', $urlPath);
$yrIndex = array_search("year",$pathParams);
$revcatIndex = array_search("revcat",$pathParams);
$fundsrcIndex = array_search("fundsrccode",$pathParams);
$agencyIndex = array_search("agency",$pathParams);

if(!$revcatIndex && !$fundsrcIndex && !$agencyIndex){
   $output .= '<h2>'. _getYearValueFromID($pathParams[$yrIndex+1]) .' NYC Revenue</h2>';
}

foreach($node->data as $key=>$value){
   $output .= '<div class="field-label">Adopted</div><div class="field-items">' . custom_number_formatter_format($value['adopted_budget'],2,'$') .'</div>';
   $output .= '<div class="field-label">Modified</div><div class="field-items">' . custom_number_formatter_format($value['current_modified_budget'],2,'$') .'</div>';
   $output .= '<div class="field-label">Revenue Collected</div><div class="field-items">' . custom_number_formatter_format($value['revenue_amount_sum'],2,'$') .'</div>';
}

print $output;