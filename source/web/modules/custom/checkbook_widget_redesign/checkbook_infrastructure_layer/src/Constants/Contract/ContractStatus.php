<?php

namespace Drupal\checkbook_infrastructure_layer\Constants\Contract;

use Drupal\checkbook_infrastructure_layer\Constants\Common\Dashboard;

abstract class ContractStatus {

    const ACTIVE = "active";
    const REGISTERED = "registered";
    const PENDING = "pending";

  /**
   * @return string
   */
    public static function getCurrent(): string
    {
      $parameter = ContractStatusParameter::getCurrent();
      return match ($parameter) {
        ContractStatusParameter::ACTIVE => self::ACTIVE,
        ContractStatusParameter::REGISTERED => self::REGISTERED,
        default => Dashboard::isSubDashboard() ? self::ACTIVE : self::PENDING,
      };
    }
}
