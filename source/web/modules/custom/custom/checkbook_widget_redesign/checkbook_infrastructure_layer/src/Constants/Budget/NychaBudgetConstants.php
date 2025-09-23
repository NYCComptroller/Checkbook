<?php

namespace Drupal\checkbook_infrastructure_layer\Constants\Budget;

abstract class NychaBudgetConstants{
  const BASE_YEAR_ID = 119;
  public static function getCurrent() {
    return self::BASE_YEAR_ID;
  }
}
