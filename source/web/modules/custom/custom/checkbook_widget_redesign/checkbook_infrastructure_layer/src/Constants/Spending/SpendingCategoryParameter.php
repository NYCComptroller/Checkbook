<?php

namespace Drupal\checkbook_infrastructure_layer\Constants\Spending;

use Drupal\checkbook_infrastructure_layer\Constants\Common\UrlParameter;
use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;

abstract class SpendingCategoryParameter {

    const CONTRACT = 1;
    const PAYROLL = 2;
    const CAPITAL = 3;
    const OTHER = 4;
    const TRUST_AGENCY = 5;

    public static function getCurrent() {
        return RequestUtilities::get(UrlParameter::SPENDING_CATEGORY);
    }
}
