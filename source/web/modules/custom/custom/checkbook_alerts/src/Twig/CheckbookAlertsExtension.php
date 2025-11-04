<?php

namespace Drupal\checkbook_alerts\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CheckbookAlertsExtension extends AbstractExtension
{
  /**
   * Generates a list of all Twig functions that this extension defines.
   *
   * @return array
   *   A key/value array that defines custom Twig functions. The key denotes the
   *   function name used in the tag, e.g.:
   * @code
   *   {{ testfunc() }}
   * @endcode
   *
   *   The value is a standard PHP callback that defines what the function does.
   */
  public function getFunctions() {
    return [
      'checkbook_alerts_get_activate_alert_link' => new TwigFunction('checkbook_alerts_get_activate_alert_link', [
        $this,
        'checkbook_alerts_get_activate_alert_link',
      ]),
      'checkbook_alerts_get_unsubscribe_link' => new TwigFunction('checkbook_alerts_get_unsubscribe_link', [
        $this,
        'checkbook_alerts_get_unsubscribe_link',
      ]),
      'checkbook_alerts_get_unsubscribe_all_link' => new TwigFunction('checkbook_alerts_get_unsubscribe_all_link', [
        $this,
        'checkbook_alerts_get_unsubscribe_all_link',
      ])
    ];
  }

  public function checkbook_alerts_get_activate_alert_link($alert2): string {
    $site_url = \Drupal::config('check_book')->get('data_feeds')['site_url'];
    return $site_url.(substr($site_url,-1)=="/"?'':"/").'alert/activate/'.$alert2['checkbook_alerts_sysid'].md5($alert2['checkbook_alerts_sysid'].$alert2['label'].$alert2['recipient']);

  }

  public function checkbook_alerts_get_unsubscribe_link($alert): string {
    $site_url = \Drupal::config('check_book')->get('data_feeds')['site_url'];
    return $site_url . (substr($site_url,-1)=="/"?'':"/") . "alert/unsubscribe/" . $alert->checkbook_alerts_sysid . md5($alert->checkbook_alerts_sysid . $alert->label.$alert->recipient);
  }

  public function checkbook_alerts_get_unsubscribe_all_link($alert): string {
    $site_url = \Drupal::config('check_book')->get('data_feeds')['site_url'];
    return $site_url.'/alert/unsubscribe/'.md5($alert->recipient);
  }
}
