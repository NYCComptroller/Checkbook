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
 * Script to send notification emails
 */

_drush_bootstrap_drupal_full();

$recipients = db_query("SELECT DISTINCT recipient FROM checkbook_alerts");

foreach($recipients as $recipient){

  $alerts=db_query(
    _checkbook_alerts_getQuery().
    "WHERE UPPER(recipient)=UPPER(:recipient) AND ".
      "active = 'Y' AND ".
      "date_end>CURRENT_TIMESTAMP AND ".
      "DATE_ADD( date_last_new_results, INTERVAL minimum_days DAY) <= CURRENT_TIMESTAMP",
        array(":recipient"=>$recipient->recipient));

  $alertsToEmail=array();
  foreach($alerts as $alert){
    $_GET['refURL']=$alert->ref_url;

    $count = _checkbook_alerts_get_data_count();
    $newRecords =  $alert->number_of_results > $count ? $alert->number_of_results-$count : $count-$alert->number_of_results;
var_dump($newRecords);
    if($newRecords>$alert->minimum_results){
      $alert->number_of_results=$count;
      $alert->date_last_new_results=date("Y-m-d H:i:s");
      drupal_write_record('checkbook_alerts',$alert,array('checkbook_alerts_sysid'));
      $alert->new_count=$newRecords;      
      if($alert->recipient_type=="EMAIL"){
        $alertsToEmail[]=$alert;
      }else if($alert->recipient_type=="TWITTER"){
        _checkbook_alerts_process_twitter($alert);
      }
    }
  }

  if(count($alertsToEmail)>0){
    _checkbook_alerts_process_email($alertsToEmail,md5($alertsToEmail[0]->recipient));
  }
}
