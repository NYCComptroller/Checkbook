<?php

namespace Drupal\checkbook_infrastructure_layer\Constants\Common;

use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;

abstract class DashboardParameter {

    const MWBE = "mp";
    const SUB_VENDORS = "ss";
    const SUB_VENDORS_MWBE = "sp";
    const MWBE_SUB_VENDORS = "ms";

  /**
   * @return array|string|null
   */
     public static function getCurrent() {
       $currentURL = \Drupal::request()->getRequestUri();
       $dashboard = RequestUtilities::get(UrlParameter::DASHBOARD,['q' => $currentURL]);
        return $dashboard;
    }
}
