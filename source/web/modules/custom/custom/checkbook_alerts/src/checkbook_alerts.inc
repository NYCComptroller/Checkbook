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

function _checkbook_alerts_form(){
  echo theme('checkbook_alerts_default_theme',array());
}

function _checkbook_alerts_advanced_search_form(){
    echo theme('checkbook_alerts_advanced_search_theme',array());
}

function _checkbook_alerts_getQuery(){
$sql=
  "SELECT checkbook_alerts_sysid,".
    "label,recipient,".
    "recipient_type,".
    "ref_url,".
    "user_url,".
    "active,".
    "number_of_results,".
    "minimum_results,".
    "minimum_days,".
    "date_end,".
    "date_last_new_results,".
  "domain,".
  "created_date,".
  "active_date,".
  "un_subscribed_date ".
  "FROM checkbook_alerts ";
  return $sql;
}

function _checkbook_alerts_transactions(){
  //Give default or option to pass this
  if(!isset($_GET['alert_theme_file'])){
      $_GET['alert_theme_file']='checkbook_alerts_subscribe_theme';
  }
  if(!isset($_GET['alert_minimum_results'])){
    $_GET['alert_minimum_results']=1;
  }
  if(!isset($_GET['alert_minimum_days'])){
    $_GET['alert_minimum_days']=1;
  }

  $_GET['q']=urldecode(drupal_get_path_alias($_GET['refURL']));

  $alerts=db_query(
    _checkbook_alerts_getQuery().
    "WHERE recipient=:recipient AND ".
      "recipient_type='EMAIL' AND ".
      "UPPER(ref_url)=UPPER(:ref_url)",
        array(
          ":recipient"=>$_GET['alert_email'],
          ":ref_url"=>$_GET['refURL']));

  $alertEndDate=strtotime($_GET['alert_end']);
  if(!$alertEndDate){
    $alertEndDate = strtotime(date("Y-m-d", mktime()) . " + 365 day");
  }
  $alertEndDate=date("Y-m-d H:i:s",$alertEndDate);
    $user_url = $_GET['userURL'];
    $user_url = str_replace(" ", "%20", $user_url);
    $alert=array(
        'recipient'=>$_GET['alert_email'],
        'label'=>$_GET['alert_label'],
        'recipient_type'=>'EMAIL',
        'ref_url'=>$_GET['refURL'],
        'user_url'=>$user_url,
        'number_of_results'=>_checkbook_alerts_get_data_count(),
        'minimum_results'=>$_GET['alert_minimum_results'],
        'minimum_days'=>$_GET['alert_minimum_days'],
        'date_end'=>$alertEndDate,
        'date_last_new_results'=>date("Y-m-d H:i:s"),
        'domain'=>_checkbook_alerts_get_domain(),
        'created_date'=>date("Y-m-d H:i:s"),
        'un_subscribed_date'=>null
    );

  if(($a=$alerts->fetchObject())!=null) {
      $alert['checkbook_alerts_sysid']=$a->checkbook_alerts_sysid;
      $alert['active']=$a->active;
      $alert['active_date']=$a->active_date;
      $alert['un_subscribed_date']=$a->null;
      drupal_write_record('checkbook_alerts',$alert,array("checkbook_alerts_sysid"));
  }
  else {
      drupal_write_record('checkbook_alerts',$alert);
  }
  $alertFrequency=_checkbook_alerts_alertFrequency($_GET['alert_minimum_days']);

    $link_expire_date = date("Y-m-d H:i:s",strtotime("+7 day", strtotime($alert["created_date"])));
    $subData = array (
        "alertFrequency"=>$alertFrequency,
        "label"=>$_GET['alert_label'],
        "alert2"=>$alert,
        "link_expire_date"=>date("m-d-Y", strtotime($link_expire_date)),
    );

  $module="checkbook_alerts";
  $key=md5(date("Y-m-d H:i:s"));
  $msg=theme('checkbook_alerts_activate_theme',$subData);

  $message=drupal_mail($module,$key,$_GET['alert_email'],language_default(),array(),"",false);

  $message['subject']="Checkbook NYC Alert Activation";
  $message['body'] = [$msg];

  $system=drupal_mail_system($module,$key);
  $message=$system->format($message);

  $system->mail($message);

  $res=array(
    "success"=>true,
    "html"=>theme($_GET['alert_theme_file'],$subData)
  );
  echo json_encode($res);
}

/**
 * Based on the current user url, derives to current domain used for reporting.
 * @return string
 */
/*function _checkbook_alerts_get_domain() {

    $user_url = $_GET['userURL'];
    $domain = "";

    if(preg_match('/budget/',$user_url)) {
        $domain = 'budget';
    }
    else if(preg_match('/revenue/',$user_url)) {
        $domain = 'revenue';
    }
    else if(preg_match('/spending/',$user_url)) {
        $domain = 'spending';
    }
    else if(preg_match('/contract/',$user_url)) {
        $domain = 'contracts';
    }
    else if(preg_match('/payroll/',$user_url)) {
        $domain = 'payroll';
    }
    return $domain;
}*/

function _checkbook_alerts_alertFrequency($days){
  switch($days){
  case 1:
    $alertFrequency="Daily";
    break;
  case 7:
    $alertFrequency="Weekly";
    break;
  case 30:
    $alertFrequency="Monthly";
    break;
  case 90:
    $alertFrequency="Quarterly";
    break;
  default:
    $alertFrequency="{$_GET['alert_minimum_days']} days";
  }
  return $alertFrequency;
}

function _checkbook_alerts_process_email($alerts){

    $module = "checkbook_alerts";
    $key = md5(date("Y-m-d H:i:s"));
    $msg = theme('checkbook_alerts_email_theme',array('alerts'=>$alerts));
    $message = drupal_mail($module,$key,$alerts[0]->recipient,language_default(),array(),"",false);

    $message['subject'] = "Checkbook NYC Alert";
    $message['body'] = [$msg];

    $system = drupal_mail_system($module,$key);
    $message = $system->format($message);

    $system->mail($message);
}

function _checkbook_alerts_process_twitter($alert){
  global $conf;

  if(!isset($conf['check_book']['alerts']['twitter'][$alert->recipient])){
    return;
  }

  $settings = $conf['check_book']['alerts']['twitter'][$alert->recipient];

  $message=str_replace(":count",$alert->new_count,$alert->label);
  $url = 'https://api.twitter.com/1.1/statuses/update.json';

  $postfields = array(
    'status'=>$message,
    'wrap_links'=>'true'
  );
  $requestMethod = 'POST';
  $twitter = new TwitterAPIExchange($settings);
  echo $twitter->buildOauth($url,$requestMethod)
               ->setPostfields($postfields)
               ->performRequest();
}

function _checkbook_alerts_twitter_callback(){

}

//////////////////////////////////////////////////////////
/*
function _checkbook_alerts_get_data_count(){
  $node = _checkbook_alerts_get_node_config();

  $node->widgetConfig->getTotalDataCount = true;
  $node->widgetConfig->getData = false;

  widget_data($node);



  return $node->totalDataCount;
}

function _checkbook_alerts_get_node_config(){

  $_GET['q']=urldecode(check_plain($_GET['refURL']));

  $nodeId = RequestUtilities::get('node');

  $node = node_load($nodeId);

  widget_config($node);

  widget_prepare($node);

  widget_invoke($node, 'widget_prepare');

  return $node;
}*/
