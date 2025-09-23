<?php

/**
 * This file is part of the Checkbook NYC financial transparency software.
 *
 * Copyright (c) 2012 – 2023 New York City
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Drupal\checkbook_alerts\Commands;

use Drupal\checkbook_log\LogHelper;
use Drush\Commands\DrushCommands;

/**
 * A Drush commandfile.
 *
 * In addition to this file, you need a drush.services.yml
 * in root of your module, and a composer.json file that provides the name
 * of the services file to use.
 *
 * See these files for an example of injecting Drupal services:
 *   - http://cgit.drupalcode.org/devel/tree/src/Commands/DevelCommands.php
 *   - http://cgit.drupalcode.org/devel/tree/drush.services.yml
 */
class CheckbookAlertsCommands extends DrushCommands {
  /**
   * Send alerts for jobs that are active for Checkbook Alerts
   *
   * @usage checkbook_alerts:sendAlerts
   *   Processes job that are available for Checkbook alerts
   *
   * @command checkbook_alerts:sendAlerts
   * @aliases sendCheckbookAlerts
   */
  public function sendAlerts() {
    $log_id = date('mdYHis');
    LogHelper::log_notice("$log_id: invoke sendAlerts command.");
    $connection = \Drupal::service('database');
    $recipients = $connection->query("SELECT DISTINCT recipient FROM checkbook_alerts WHERE un_subscribed_date IS NULL");
    $records = $recipients->fetchAll();
    LogHelper::log_notice("$log_id: Found " . count($records) . " records in checkbook-alerts");
    foreach($records as $recipient){
      LogHelper::log_notice("$log_id: Processing " . $recipient->recipient . " records");
      $alerts = $connection->query(\Drupal::service('checkbook-alerts.helper')->checkbook_alerts_getQuery().
        "WHERE UPPER(recipient)=UPPER(:recipient) AND ".
        "active = 'Y' AND ".
        "date_end>CURRENT_TIMESTAMP AND ".
        "DATE_ADD( date_last_new_results, INTERVAL minimum_days DAY) <= CURRENT_TIMESTAMP AND ".
        "un_subscribed_date IS NULL",
        array(":recipient"=>$recipient->recipient));
      $alerts_records = $alerts->fetchAll();
      LogHelper::log_notice("$log_id: Found " . count($alerts_records) .  " records that need alerts sent for " . $recipient->recipient);
      $alertsToEmail=array();

      foreach($alerts_records as $alert){
        \Drupal::request()->query->set('refURL',$alert->ref_url);
        $count = \Drupal::service('checkbook-alerts.helper')->checkbook_alerts_get_data_count();
        LogHelper::log_notice("$log_id: Count for refURL '" . $alert->ref_url .  "' is $count");
        $newRecords =  $alert->number_of_results > $count ? $alert->number_of_results-$count : $count-$alert->number_of_results;

        if($newRecords>=$alert->minimum_results){
          LogHelper::log_notice("$log_id: New record count more than previous");
          $alert->number_of_results=$count;
          $alert->date_last_new_results=date("Y-m-d H:i:s");
          $queryStatus = \Drupal::database()->merge('checkbook_alerts')
                ->key('checkbook_alerts_sysid', $alert->checkbook_alerts_sysid)
                ->fields([
                  'number_of_results' => $count,
                  'date_last_new_results' => date("Y-m-d H:i:s"),
                ])
                ->execute();
          $alert->new_count=$newRecords;
          if($alert->recipient_type=="EMAIL"){
            LogHelper::log_notice("$log_id: Update checkbook_alerts_sent table for sysid: $alert->checkbook_alerts_sysid");
            $alertsToEmail[]=$alert;
            $inserted_id = $connection->insert('checkbook_alerts_sent')
              ->fields(array('checkbook_alerts_sysid' => $alert->checkbook_alerts_sysid,'sent_date' => date("Y-m-d H:i:s")))
              ->execute();
          } else if($alert->recipient_type=="TWITTER"){
            _checkbook_alerts_process_twitter($alert);
          }
        }
      }
      if(is_countable($alertsToEmail) && count($alertsToEmail)>0){
        LogHelper::log_notice("process email for $recipient->recipient");
        \Drupal::service('checkbook-alerts.helper')->checkbook_alerts_process_email($alertsToEmail,md5($alertsToEmail[0]->recipient));
      }
    }
  }
}
