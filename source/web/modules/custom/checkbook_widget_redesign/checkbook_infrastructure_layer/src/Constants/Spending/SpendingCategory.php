<?php

namespace Drupal\checkbook_infrastructure_layer\Constants\Spending;

abstract class SpendingCategory {

    const TOTAL = "total_spending";
    const PAYROLL = "payroll_spending";
    const CAPITAL = "capital_spending";
    const CONTRACT = "contract_spending";
    const TRUST_AGENCY = "trust_and_agency_spending";
    const OTHER = "other_spending";

    public static function getCurrent() {
        $parameter = SpendingCategoryParameter::getCurrent();

        switch($parameter) {
            case SpendingCategoryParameter::PAYROLL: return self::PAYROLL;
            case SpendingCategoryParameter::CAPITAL: return self::CAPITAL;
            case SpendingCategoryParameter::CONTRACT: return self::CONTRACT;
            case SpendingCategoryParameter::TRUST_AGENCY: return self::TRUST_AGENCY;
            case SpendingCategoryParameter::OTHER: return self::OTHER;
            default: return SpendingCategory::TOTAL;
        }
    }
}
