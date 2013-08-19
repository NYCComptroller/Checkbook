<?php

function _checkbook_alerts_form(){
  echo theme('checkbook_alerts_default_theme',array());
}

function _checkbook_alerts_transactions(){
  $nodeId=_getRequestParamValue('node',false);
  $node=node_load($nodeId);
  if(!isset($_GET['alert_email'])){
    $errors[]="No email address was sent.";
  }
  if(!isset($_GET['refURL'])){
    $errors[]="No search was selected.";
  }

  if(!isset($_GET['alert_label'])){
    $errors[]="No alert label.";
  }
  if(isset($errors)){
    echo json_encode($errors);
    return;
  }

  if(!isset($_GET['alert_minimum_results'])){
    $_GET['alert_minimum_results']=1;
  }
  if(!isset($_GET['alert_minimum_days'])){
    $_GET['alert_minimum_days']=1;
  }
  if(!isset($_GET['alert_end']['date'])){
    $_GET['alert_end']['date']=date('Y-m-d H:i:s',strtotime(date("Y-m-d", mktime()) . " + 365 day"));
  }

  $_GET['q']=urldecode(drupal_get_path_alias($_GET['refURL']));

  $alert=array(
    'recipient'=>$_GET['alert_email'],
    'label'=>$_GET['alert_label'],
    'recipient_type'=>'EMAIL',
    'ref_url'=>$_GET['refURL'],
    'number_of_results'=>_checkbook_alerts_get_data_count(),
    'minimum_results'=>$_GET['alert_minimum_results'],
    'minimum_days'=>$_GET['alert_minimum_days'],
    'date_end'=>$_GET['alert_end']['date'],
    'date_last_new_results'=>date("Y-m-d H:i:s")
  );
  drupal_write_record('checkbook_alerts',$alert);
}

function _checkbook_alerts_unsubscribe(){
  $alert=arg(2);

  db_query('DELETE FROM checkbook_alerts WHERE CONCAT(checkbook_alerts_sysid,MD5(CONCAT(checkbook_alerts_sysid,label,recipient)))=:alert OR MD5(recipient)=:alert',array(':alert'=>$alert));

  echo "Unsubscribed";
}

//////////////////////////////////////////////////////////

function _checkbook_alerts_get_data_count(){
  $node = _checkbook_alerts_get_node_config();

  $node->widgetConfig->getTotalDataCount = true;
  $node->widgetConfig->getData = false;

  widget_data($node);

  return $node->totalDataCount;
}

function _checkbook_alerts_get_node_config(){
  global $conf;

  $_GET['q']=urldecode(check_plain($_GET['refURL']));

  $nodeId = _getRequestParamValue('node');

  $node = node_load($nodeId);

  widget_config($node);

  widget_prepare($node);

  widget_invoke($node, 'widget_prepare');

  return $node;
}
