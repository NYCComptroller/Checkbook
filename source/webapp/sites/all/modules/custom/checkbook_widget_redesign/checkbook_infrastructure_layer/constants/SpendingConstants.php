<?php

abstract class SpendingCategory {

    const TOTAL = "total_spending";
    const PAYROLL = "payroll_spending";
    const CAPITAL = "capital_spending";
    const CONTRACT = "contract_spending";
    const TRUST_AGENCY = "trust_and_agency_spending";
    const OTHER = "other_spending";

    static public function getCurrent() {
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

abstract class SpendingCategoryParameter {

    const CONTRACT = 1;
    const PAYROLL = 2;
    const CAPITAL = 3;
    const OTHER = 4;
    const TRUST_AGENCY = 5;

    static public function getCurrent() {
        return RequestUtilities::getRequestParamValue(UrlParameter::SPENDING_CATEGORY);
    }
}
