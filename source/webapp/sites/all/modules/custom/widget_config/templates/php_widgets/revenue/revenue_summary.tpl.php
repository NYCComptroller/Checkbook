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