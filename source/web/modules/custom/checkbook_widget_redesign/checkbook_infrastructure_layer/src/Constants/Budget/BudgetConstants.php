<?php

namespace Drupal\checkbook_infrastructure_layer\Constants\Budget;

abstract class BudgetConstants{
    const BASE_YEAR_ID = 112;
     public static function getCurrent() {
        return self::BASE_YEAR_ID;
     }
}
