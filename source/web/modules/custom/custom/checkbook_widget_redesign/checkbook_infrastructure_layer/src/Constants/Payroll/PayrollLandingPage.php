<?php

namespace Drupal\checkbook_infrastructure_layer\Constants\Payroll;

use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;

abstract class PayrollLandingPage {

    const NYC_LEVEL = "nyc_landing";
    const AGENCY_LEVEL = "agency_landing";
    const TITLE_LEVEL = "title_landing";

    public static function getCurrent() {
      $urlPath = RequestUtilities::getCurrentPageUrl();
      $ajaxPath = RequestUtilities::getAjaxPath();
      $refURL = RequestUtilities::getRefUrl();
      $page = null;

      if(str_contains($urlPath, 'payroll') || str_contains($ajaxPath, 'payroll') || str_contains($refURL, 'payroll')) {
        if(str_contains($urlPath, 'agency_landing') || str_contains($ajaxPath, 'agency_landing') || str_contains($refURL, 'agency_landing')) {
          $page = self::AGENCY_LEVEL;
        }
        else if(str_contains($urlPath, 'title_landing') || str_contains($ajaxPath, 'title_landing') || str_contains($refURL, 'title_landing')) {
          $page = self::TITLE_LEVEL;
        }
        else {
          $page = self::NYC_LEVEL;
        }
      }
      return $page;
    }
}
