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


namespace Drupal\checkbook_alerts\Controller;

use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;
use Drupal\checkbook_log\LogHelper;
use Drupal\Core\Controller\ControllerBase;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

require_once(dirname(__FILE__) . "/../../../checkbook_advanced_search/src/Results/checkbook_advanced_search.inc");

class CheckbookAlertsController extends ControllerBase {
  public function checkbook_alerts_form_display() {
    $page['advanced_search_form'] = \Drupal::formBuilder()->getForm('Drupal\checkbook_advanced_search\Form\CheckbookAdvancedSearchForm', true);
    return $page;
  }

  public function checkbook_alerts_transactions_form() {
    $build['#theme'] = 'checkbook_alerts_default_theme';
    return $build;
  }

  public function checkbook_alerts_transactions_advanced_search_form() {
    $form = \Drupal::service('checkbook-alerts.helper')->checkbook_alerts_get_create_alert_form();
    return new Response(\Drupal::service('renderer')->render($form));
  }

  /**
   * Function will activate the alert alert if the following conditions are met:
   *
   * 1. Alert is not already active
   * 2. Alert was created in the past 7 days
   * 3. Alert has not been un-subscribed from
   * 4. Alert end date is not past
   *
   * @return string
   * @throws Exception
   */
  public function checkbook_alerts_activate($token) {
    $arg = $token;

    //Un-subscribed
    $message = 'Your alert activation link is no longer active. You have unsubscribed from this alert.';

    $database = \Drupal::database();
    $alert = $database->select('checkbook_alerts')
      ->fields('checkbook_alerts', ['checkbook_alerts_sysid','created_date','date_end','active'])
      ->where('CONCAT(checkbook_alerts_sysid,MD5(CONCAT(checkbook_alerts_sysid,label,recipient))) = :arg',array(':arg' => $arg))
      ->isNull('un_subscribed_date')
      ->execute()
      ->fetchAssoc();

    if($alert) {
      //End date is past
      if(time() > strtotime($alert['date_end'])) {
        $message = 'Your alert has expired. Please schedule a new alert using the alerts functionality.';
      }
      //Already Active
      else if($alert['active'] == 'Y') {
        $message = 'The alert is already active.';
      }
      //Expired
      else if(time() > strtotime('+7 day', strtotime($alert['created_date']))) {
        $message = 'Your alert activation link has expired. Please schedule a new alert using the alerts functionality.';
      }
      //Activate
      else {
        $message = 'Your alert has been successfully activated.';
        $database->update('checkbook_alerts')
          ->fields(array('active' => 'Y','active_date' => date("Y-m-d H:i:s")))
          ->where('CONCAT(checkbook_alerts_sysid,MD5(CONCAT(checkbook_alerts_sysid,label,recipient))) = :arg', array(':arg' => $arg))
          ->execute();
      }
    }

    $build = [
      '#theme' => 'checkbook_alerts_activated_theme',
      '#message' => $message
    ];
    return $build;
    //return new Response(\Drupal::service('renderer')->render($build));
  }

  /**
   * Function will check if the value provided is for a single alert or all alerts
   * and populate the un subscribe date for those records.
   *
   * Single alert -> value is hash of checkbook_alerts_sysid|label|recipient appended to the sys_id:
   *
   * All Alerts for a user ->value is hash of this recipient
   *
   * @return string
   * @throws Exception
   */
  public function checkbook_alerts_unsubscribe($token) {
    $alert = $token;
    $num_alerts = 0;

    $database = \Drupal::database();
    $result = $database->select('checkbook_alerts')
      ->fields('checkbook_alerts', array('checkbook_alerts_sysid','recipient'))
      ->isNull('un_subscribed_date')
      ->where('CONCAT(checkbook_alerts_sysid,MD5(CONCAT(checkbook_alerts_sysid,label,recipient))) = :alert', array(':alert' => $alert))
      ->execute()
      ->fetchAssoc();

    if($result) {
      $num_alerts = 1;
      $database->update('checkbook_alerts')
        ->fields(array('un_subscribed_date' => date("Y-m-d H:i:s")))
        ->where('checkbook_alerts_sysid = :checkbook_alerts_sysid', array(':checkbook_alerts_sysid' => $result['checkbook_alerts_sysid']))
        ->execute();
    }
    else {
      $query = $database->select('checkbook_alerts')
        ->where('MD5(recipient) = :alert', array(':alert' => $alert))
        ->isNull('un_subscribed_date');
      $query
        ->groupBy($query->addField('checkbook_alerts', 'recipient'))
        ->addExpression('COUNT(checkbook_alerts_sysid)', 'num_alerts');
      $result = $query->execute()->fetchAssoc();

      if(is_countable($result) && count($result) > 0) {
        $num_alerts = $result['num_alerts'];
        $database->update('checkbook_alerts')
          ->fields(array('un_subscribed_date' => date("Y-m-d H:i:s")))
          ->where('recipient = :recipient', array(':recipient' => $result['recipient']))
          ->execute();
      }
    }

    $build = [
      '#theme' => 'checkbook_alerts_unsubscribe_theme',
      '#numAlerts' => $num_alerts
    ];
    return $build;
    //return new Response(\Drupal::service('renderer')->render($build));
  }

  /**
   * @throws \Exception
   */
  public function checkbook_alerts_transactions() {
    //Give default or option to pass this
    if(\Drupal::request()->query->get('alert_theme_file') === NULL){
      \Drupal::request()->query->set('alert_theme_file', 'checkbook_alerts_subscribe_theme');
    }
    if(\Drupal::request()->query->get('alert_minimum_results') === NULL){
      \Drupal::request()->query->set('alert_minimum_results', 1);
    }
    if(\Drupal::request()->query->get('alert_minimum_days') === NULL){
      \Drupal::request()->query->set('alert_minimum_days', 1);
    }
    \Drupal::request()->query->set('q', urldecode(RequestUtilities::getCurrentPageUrl()));

    $alert_email = \Drupal::request()->query->get('alert_email');
    $refURL = \Drupal::request()->query->get('refURL');

    $db = \Drupal::database();
    $alerts = $db->query(\Drupal::service('checkbook-alerts.helper')->checkbook_alerts_getQuery().
      " WHERE recipient=:recipient AND ".
      "recipient_type='EMAIL' AND ".
      "UPPER(ref_url)=UPPER(:ref_url)",
      array(
        ":recipient"=>$alert_email,
        ":ref_url"=>$refURL
      ));
    $alertEndDate=strtotime(\Drupal::request()->query->get('alert_end'));
    if(!$alertEndDate){
      $alertEndDate = strtotime(date("Y-m-d", time()) . " + 365 day");
    }
    $alertEndDate=date("Y-m-d H:i:s",$alertEndDate);
    $user_url = \Drupal::request()->query->get('userURL');
    $user_url = str_replace(" ", "%20", $user_url);
    $alert=array(
      'recipient'=>\Drupal::request()->query->get('alert_email'),
      'label'=>\Drupal::request()->query->get('alert_label'),
      'recipient_type'=>'EMAIL',
      'ref_url'=>$refURL,
      'user_url'=>$user_url,
      'number_of_results'=>\Drupal::service('checkbook-alerts.helper')->checkbook_alerts_get_data_count(),
      'minimum_results'=>\Drupal::request()->query->get('alert_minimum_results'),
      'minimum_days'=>\Drupal::request()->query->get('alert_minimum_days'),
      'date_end'=>$alertEndDate,
      'date_last_new_results'=>date("Y-m-d H:i:s"),
      'domain'=>\Drupal::service('checkbook-alerts.helper')->checkbook_alerts_get_domain(),
      'created_date'=>date("Y-m-d H:i:s"),
      'un_subscribed_date'=>null
    );

    $connection = \Drupal::service('database');

    //check if checkbook alerts already has the same record
    $alerts = $connection->query(\Drupal::service('checkbook-alerts.helper')->checkbook_alerts_getQuery().
      " WHERE recipient=:recipient AND ".
      "recipient_type='EMAIL' AND ".
      "UPPER(ref_url)=UPPER(:ref_url)",
      array(
        ":recipient"=>$alert_email,
        ":ref_url"=>RequestUtilities::getCurrentPageUrl()
      ));
    $records = $alerts->fetchAll();

    //if record exists
    if(($a=$records[0])!=null) {
      $alert['checkbook_alerts_sysid']=$a->checkbook_alerts_sysid;
      $alert['active']=$a->active;
      $alert['active_date']=$a->active_date;
      $alert['un_subscribed_date']=$a->null;
      $updated_id = $connection->update('checkbook_alerts')
        ->fields($alert)
        ->where('checkbook_alerts_sysid = ' . $a->checkbook_alerts_sysid)
        ->execute();
    } else {
      //insert the data into checkbook_alerts table
      $inserted_id = $connection->insert('checkbook_alerts')
        ->fields($alert)
        ->execute();
      if ($inserted_id) {
        $alert['checkbook_alerts_sysid']=$inserted_id;
      }
    }

    $alertFrequency=\Drupal::service('checkbook-alerts.helper')->checkbook_alerts_alertFrequency(\Drupal::request()->query->get('alert_minimum_days'));

    $link_expire_date = date("Y-m-d H:i:s",strtotime("+7 day", strtotime(date("Y-m-d H:i:s"))));
    $module="checkbook_alerts";
    $to = $alert_email;

    $renderable = [
      '#theme' => 'checkbook_alerts_activate_theme',
      '#alertFrequency' => $alertFrequency,
      '#label' => \Drupal::request()->query->get('alert_label'),
      '#alert2' => $alert,
      '#link_expire_date' => date("m-d-Y", strtotime($link_expire_date)),
    ];
    $msg = \Drupal::service('renderer')->renderInIsolation($renderable);

    $params['message'] = $msg;
    $params['title'] = "Checkbook NYC Alert Activation";

    $langcode = \Drupal::currentUser()->getPreferredLangcode();
    $send = true;

    $result = \Drupal::service('plugin.manager.mail')->mail($module, 'checkbook_alerts_activate', $to, null, $params,);

    if(!$result['result']) {
      $msg = "Error sending email in class CheckBookAPI, function sendConfirmationEmail()";
      LogHelper::log_error($msg);
      throw new Exception($msg);
    }

    $renderable_theme = [
      '#theme' => \Drupal::request()->query->get('alert_theme_file'),
    ];
    $html = \Drupal::service('renderer')->renderInIsolation($renderable_theme);


    $res=array(
      "success"=>true,
      "html"=>$html,
      "error" => ""
    );


    return new JsonResponse([ 'data' => $res, 'status'=> 200]);

  }
}
