<?php
/***
 * @param $sql_query
 * @return string
 */
function checkbook_api_adjustNYCHARevenueSql(&$sql_query)
{
  $sql_parts = explode("WHERE", $sql_query);
  $select_part = $sql_parts[0];
  $where_part = $sql_parts[1];
  $select_part = str_replace("modified_amount", "adopted_amount AS modified_amount", $select_part);
  $sql_query = (count($sql_parts) > 1) ? implode(' WHERE ', array($select_part, $where_part)) : $select_part;
}
