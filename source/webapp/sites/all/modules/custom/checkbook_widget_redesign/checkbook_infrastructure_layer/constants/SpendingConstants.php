<?php
/**
 * Created by PhpStorm.
 * User: pshirodkar
 * Date: 12/7/16
 * Time: 4:54 PM
 */

abstract class SpendingDimension {

    const TOTAL = "total_spending";
    const PAYROLL = "payroll_spending";
    const CAPITAL = "capital_spending";
    const CONTRACT = "contract_spending";
    const TRUSTAGENCY = "trust_and_agency_spending";
    const OTHER = "other_spending";

    static public function getCurrent() {
        $category = NULL;
        $category = RequestUtilities::getRequestParamValue(UrlParameter::SPENDING_CATEGORY);
        switch($category){
            case 1:
                $dimension = self::CONTRACT;
                break;
            case 2:
                $dimension = self::PAYROLL;
                break;
            case 3:
                $dimension = self::CAPITAL;
                break;
            case 4:
                $dimension = self::OTHER;
                break;
            case 5:
                $dimension = self::TRUSTAGENCY;
                break;
            default:
                $dimension = self::TOTAL;
                break;
        }
        return $dimension;
    }
}
