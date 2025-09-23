<?php

namespace Drupal\checkbook_infrastructure_layer\Constants\Contract;

use Drupal\checkbook_infrastructure_layer\Constants\Common\UrlParameter;
use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;

abstract class ContractStatusParameter {
    const ACTIVE = "A";
    const REGISTERED = "R";
    const PENDING = "P";

    public static function getCurrent() {
        return RequestUtilities::get(UrlParameter::CONTRACT_STATUS);
    }
}
