<?php
$prod_process_errors = FALSE;
if(isset($prod_stats)) {
  foreach ($prod_stats as $prod_stat) {
    if ($prod_stat['Last Run Success?'] == 'Fail') {
      if ($prod_stat['All Files Processed?'] == 'N' && $prod_stat['Shards Refreshed?'] == 'Y' && $prod_stat['Solr Refreshed?'] == 'Y') {
        $prod_process_errors = TRUE;
      }
    }
  }
}
$uat_process_errors = FALSE;
if(isset($uat_stats)) {
  foreach ($uat_stats as $uat_stat) {
    if ($uat_stat['Last Run Success?'] == 'Fail') {
      if ($uat_stat['All Files Processed?'] == 'N' && $uat_stat['Shards Refreshed?'] == 'Y' && $uat_stat['Solr Refreshed?'] == 'Y') {
        $uat_process_errors = TRUE;
      }
    }
  }
}
