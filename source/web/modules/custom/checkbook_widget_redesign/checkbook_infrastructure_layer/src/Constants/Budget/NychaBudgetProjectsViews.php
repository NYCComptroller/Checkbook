<?php

namespace Drupal\checkbook_infrastructure_layer\Constants\Budget;

use Drupal\checkbook_infrastructure_layer\Constants\Common\UrlParameter;
use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;

abstract class NychaBudgetProjectsViews {
  const PERCENT_DIFF = "percent_difference_by_projects";
  const PERCENT_DIFF_PREVIOUS_1 = "percent_difference_by_projects_previous_1";
  const PERCENT_DIFF_PREVIOUS_2 = "percent_difference_by_projects_previous_2";

  public static function getCurrent() {
    $base_year = NychaBudgetConstants::getCurrent();;
    $year = RequestUtilities::get(UrlParameter::YEAR);

    if($year == $base_year + 1) {
      return self::PERCENT_DIFF_PREVIOUS_1;
    }
    else if($year == $base_year + 2) {
      return self::PERCENT_DIFF_PREVIOUS_2;
    }
    else if($year > $base_year) {
      return self::PERCENT_DIFF;
    }
  }
}
