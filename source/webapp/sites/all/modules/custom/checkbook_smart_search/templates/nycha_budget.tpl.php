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
$budget_parameter_mapping = CheckbookSolr::getSearchFields($solr_datasource, 'budget');
$amount_fields = array("adopted_budget", "modified_budget", "committed","actual_amount","funds_available","encumbrance_amount");

$count = 1;
$row = array();
$rows = array();
foreach ($budget_parameter_mapping as $key=>$title){
  $value = $budget_results[$key];

  if($searchTerm){
    $temp = substr($value, strpos(strtoupper($value), strtoupper($searchTerm)),strlen($searchTerm));
    $value = str_ireplace($searchTerm,'<em>'. $temp . '</em>', $value);
  }

  $value = _checkbook_smart_search_str_html_entities($value);

  if(in_array($key, $amount_fields)){
    $value = custom_number_formatter_format($value, 2 , '$');
  }
  if ($count % 2 == 0){
    if($title)
      $row[] = '<div class="field-label">'.$title.':</div><div class="field-content">'.$value.'</div>';
    $rows[] = $row;
    $row = array();
  } else {
    if($title)
      $row[] = '<div class="field-label">'.$title.':</div><div class="field-content">'.$value.'</div>';
  }
  $count++;

}
print theme('table',array('rows'=>$rows,'attributes'=>array('class'=>array('search-result-fields'))));



