<?php /**
 * @file
 * Contains \Drupal\checkbook_api\EventSubscriber\InitSubscriber.
 */

namespace Drupal\checkbook_api\EventSubscriber;

use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class InitSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [KernelEvents::REQUEST => ['onEvent', 0]];
  }

  public function onEvent() {
    //require_once(\Drupal::service('extension.list.module')->getPath('checkbook_api') . "/Utilities/revenue.inc");
    //require_once(\Drupal::service('extension.list.module')->getPath('checkbook_api') . "/Utilities/budget.inc");
    //require_once(\Drupal::service('extension.list.module')->getPath('checkbook_api') . "/Utilities/contracts.inc");
    //require_once(\Drupal::service('extension.list.module')->getPath('checkbook_api') . "/Utilities/spending.inc");
    //require_once(\Drupal::service('extension.list.module')->getPath('checkbook_api') . "/Utilities/payroll.inc");
  }

}
