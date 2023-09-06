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

namespace Drupal\checkbook_alerts\Services;

use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;
use Drupal\checkbook_log\LogHelper;
use Drupal\data_controller\Common\Object\Converter\Handler\Json2PHPArray;
use Exception;

/**
 * Class CheckbookAlertsHelper
 * @package Drupal\mymodule\Services
 */
class CheckbookAlertsHelper {
  /**
   * CheckbookAlertsHelper constructor.
   */
  public function __construct() {
  }

  /**
   * @param $step
   * @return mixed
   */
  public function checkbook_alerts_create_alert_instructions($step) {
    $create_alert_config =  $this->checkbook_alerts_create_alert_config();
    $instructions = $create_alert_config[$step]['instructions'];
    return $instructions;
  }

  /**
   * Get create alert wizard configuration.
   *
   * @return mixed
   *   configuration
   */
  public function checkbook_alerts_create_alert_config(): mixed {
    $checkbook_advanced_search_path = \Drupal::service('extension.list.module')->getPath('checkbook_advanced_search');
    $config_str = file_get_contents(realpath($checkbook_advanced_search_path) . "/src/config/checkbook_advanced_search_create_alert_config.json");
    $converter = new Json2PHPArray();
    $configuration = $converter->convert($config_str);
    return $configuration;
  }

  /**
   * @param $create_alert_config
   * @param $current_step
   * @return string
   */
  public function checkbook_alerts_create_alert_header($create_alert_config, $current_step)
  {
    $header_html = '';

    foreach($create_alert_config as $step) {
      if(!isset($step['title'])) continue;
      $header_html .= $header_html != '' ? "<span class='inactive'>&nbsp;|&nbsp;</span>" : "";
      $header_html .= $current_step == $step['name'] ? "<span class='active'>" : "<span class='inactive'>";
      $header_html .= $step['title'];
      $header_html .= "</span>";
    }

    $header_html = "<span class='create-alert-header'>".$header_html."</span>";
    return $header_html;
  }

  public function checkbook_alerts_form_header($alert_form_state) {
    $search_criteria = $alert_form_state == 'select_criteria' ? 'active' : 'inactive';
    $customize_results = $alert_form_state == 'customize_results' ? 'active' : 'inactive';
    $schedule_alert = $alert_form_state == 'schedule_alert' ? 'active' : 'inactive';
    $return = '<span class="ui-dialog-title" id="ui-dialog-title-block-checkbook-advanced-search-checkbook-advanced-search-form">';
    $return .= '<span class="create-alert-header">';
    $return .= '<span class="'.$search_criteria.'">1. Select Criteria</span>';
    $return .= '<span class="inactive">&nbsp;|&nbsp;</span>';
    $return .= '<span class="'.$customize_results.'">2. Customize Results</span>';
    $return .= '<span class="inactive">&nbsp;|&nbsp;</span>';
    $return .= '<span class="'.$schedule_alert.'">3. Schedule Alert</span>';
    $return .= '</span>';
    $return .= '</span>';
    return $return;
  }

  public function checkbook_alerts_get_redirect_iframe($redirect_url) {
    $results_html = '<div class="create-alert-customize-results">';
    $results_html .= '<iframe src="'.$redirect_url.'" id="checkbook_advanced_search_result_iframe" frameBorder="0" height="600px" width="100%" style="overflow-x:hidden; overflow-y:scroll;"></iframe>';
    $results_html .= '</div>';
    return $results_html;
  }

  public function checkbook_alerts_create_button($alert_form_state, $button_type, $visibility=null, $callback=null) {
    $create_alert_config =  $this->checkbook_alerts_create_alert_config();
    $button_info = $create_alert_config[$alert_form_state]['buttons'][$button_type];
    $style = '';
    if ($visibility == 'hidden') {
      $form[$button_info['id']] = array(
        '#type' => 'button',
        '#value' => $button_info['value'],
        '#name' => $button_info['name'],
        '#attributes' => array('style'=>'display: none;'),
      );
      if (!empty($button_info['ajax_callback'])) {
        $form[$button_info['id']]['#ajax'] = array(
          'callback' => $callback,
          'event' => 'click',
          'progress' => array('type' => 'none')
        );
      }
    } else {
      $form[$button_info['id']] = array(
        '#type' => 'submit',
        '#value' => $button_info['value'],
        '#name' => $button_info['name'],
        '#submit' => array($button_info['ajax_callback']),
        '#attributes' => array('style'=>$style)
      );
    }

    return $form;
  }

  public function checkbook_alerts_get_create_alert_form() {
    $form['checkbook_create_alert_pref'] = array(
      '#type' => 'markup',
      '#markup' => "<div id='dialog'>
                        <div id='errorMessages'></div>
                        <div id='alert_instructions'>Checkbook alerts will notify you by email when new results matching your current search criteria are available.  Emails will be sent based on the frequency selected and only after the minimum number of additional results entered has been reached since the last alert.</div>
                        <table>
                        <tr>
                          <th>
                            <span class='bold'>Alert Settings</span>
                          </th>
                        </tr>",
      '#allowed_tags' => ['div', 'button', 'h1', 'h2', 'h3', 'span', 'table', 'tr', 'td', 'th'],
    );
    $form['alert_label'] = array(
      '#type' => 'textfield',
      '#name' => 'alert_label',
      '#size' => 25,
      '#prefix' => '<tr><td><span class="alert-required-field" style="color:red;">*</span>Description</td><td>',
      '#suffix' => '<div class="description">This is how the alert will be described in the email text.</div></td></tr>',
      '#required' => true,
    );
    $form['alert_email'] = array(
      '#type' => 'textfield',
      '#name' => 'alert_email',
      '#size' => 50,
      '#prefix' => '<tr><td><span class="alert-required-field" style="color:red;">*</span>Email</td><td>',
      '#suffix' => '</td></tr>',
      '#required' => true,
    );
    $form['alert_minimum_results'] = array(
      '#type' => 'textfield',
      '#size' => 5,
      '#name' => 'alert_minimum_results',
      '#maxlength' => 5,
      '#prefix' => '<tr><td>Minimum Additional Results</td><td>',
      '#suffix' => '<div class="description">Checkbook will not notify you until this many new results are returned.</div></td></tr>',
      '#default_value' => '10',
      '#value' => '10'
    );
    $form['alert_minimum_days'] = array(
      '#type' => 'select',
      '#name' => 'alert_minimum_days',
      '#default_value' => t('Daily'),
      '#prefix' => '<tr><td>Alert Frequency</td><td>',
      '#suffix' => '</td></tr>',
      '#options' => ['1'=>'Daily','7'=>'Weekly','30'=>'Monthy','92'=>'Quarterly']
    );
    $form['alert_end'] = array(
      '#type' => 'date',
      '#date_format' => 'Y-m-d',
      '#name' => 'alert_end',
      '#size' => 30,
      '#maxlength' => 30,
      '#date_year_range' => "'-" . (date("Y") - 1900) . ":+" . (2500 - date("Y")) . "'",
      '#prefix' => '<tr><td>Expiration Date</td><td>',
      '#suffix' => '<div class="description">This is the date the alert will expire.  The default is one year.</div></td></tr>'
    );
    $form['checkbook_create_alert_sufix'] = array(
      '#type' => 'markup',
      '#markup' => "</table></div>",
      '#allowed_tags' => ['div', 'button', 'h1', 'h2', 'h3'],
    );
    return $form;
   // return [  '#theme' => 'checkbook_alerts'];;
  }

  public function checkbook_alerts_get_customize_results_step_form($form, &$form_state) {
    $form['alert_form_header'] = array(
      '#type' => 'markup',
      '#markup' => $this->checkbook_alerts_form_header('customize_results'),
    );
    //if ($isAlert) {
    $form['alert_instruction'] = array(
      '#type' => 'markup',
      '#markup' => $this->checkbook_alerts_create_alert_instructions('customize_results'),
    );
    $form['alert_submit_output'] = array(
      '#type' => 'markup',
      '#markup' => $this->checkbook_alerts_get_redirect_iframe($form_state->get('alert_form_redirect_url')),
      '#allowed_tags' => ['div', 'button', 'h1', 'h2', 'h3', 'iframe'],
    );//checkbook_alerts_create_button
    $form[] = $this->checkbook_alerts_create_button('customize_results','back');
    $form[] = $this->checkbook_alerts_create_button('customize_results','next');
    return $form;
  }

  public function checkbook_alerts_get_schedule_alert_step_form($form, &$form_state) {
    $form['alert_form_header'] = array(
      '#type' => 'markup',
      '#markup' => $this->checkbook_alerts_form_header('schedule_alert'),
    );
    //checkbook_alerts_get_create_alert_form
    $form[] = $this->checkbook_alerts_get_create_alert_form();
    $form[] = $this->checkbook_alerts_create_button('schedule_alert','back');
    $form[] = $this->checkbook_alerts_create_button('schedule_alert','next');
    return $form;
  }

  public function checkbook_alerts_getQuery(){
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

  public function checkbook_alerts_alertFrequency($days){
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

  function checkbook_alerts_get_data_count(){
    $node = $this->checkbook_alerts_get_node_config();

    $node->widgetConfig->getTotalDataCount = true;
    $node->widgetConfig->getData = false;

    widget_data($node);

    return $node->totalDataCount;
  }

  function checkbook_alerts_get_node_config(){

    //$_GET['q']=urldecode(check_plain($_GET['refURL']));
    //\Drupal::request()->query->set('q', urldecode(RequestUtilities::getRefUrl()));
    \Drupal::request()->query->set('q', urldecode(\Drupal::request()->query->get('refURL')));


    $nodeId = RequestUtilities::get('node');

    $node = _widget_node_load_file($nodeId);

    widget_config($node);

    widget_prepare($node);

    widget_invoke($node, 'widget_prepare');

    return $node;
  }
  /**
   * Based on the current user url, derives to current domain used for reporting.
   * @return string
   */
  function checkbook_alerts_get_domain() {

    $user_url = \Drupal::request()->query->get('userURL');
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
  }

  function checkbook_alerts_process_email($alerts){

    $module = "checkbook_alerts";
    $key = md5(date("Y-m-d H:i:s"));

    $renderable = [
      '#theme' => 'checkbook_alerts_email_theme',
      '#alerts' => $alerts,
    ];
    $msg = \Drupal::service('renderer')->renderPlain($renderable);

    ///$message = drupal_mail($module,$key,$alerts[0]->recipient,language_default(),array(),"",false);

    $params['message'] = $msg;
    $params['title'] = "Checkbook NYC Alert";
    $result = \Drupal::service('plugin.manager.mail')->mail($module, 'checkbook_alerts_email', $alerts[0]->recipient, null, $params,);
    if(!$result['result']) {
      $msg = "Error sending email in class CheckBookAPI, function checkbook_alerts_process_email()";
      LogHelper::log_error($msg);
      throw new Exception($msg);
    }
  }
}
