<?php
/**
 * Created by PhpStorm.
 * User: atorkelson
 * Date: 10/18/16
 * Time: 5:43 PM
 */

abstract class ContractCategory {

    const EXPENSE = "expense";
    const REVENUE = "revenue";

    static public function getCurrent() {
        $urlPath = $_GET['q'];
        $ajaxPath = $_SERVER['HTTP_REFERER'];
        $category = self::EXPENSE;

        if(preg_match('/revenue/',$urlPath) || preg_match('/revenue/',$ajaxPath)
        || preg_match('/pending_rev/',$urlPath) || preg_match('/pending_rev/',$ajaxPath)) {
            $category = self::REVENUE;
        }

        return $category;
    }
}

abstract class ContractStatus {

    const ACTIVE = "active";
    const REGISTERED = "registered";
    const PENDING = "pending";

    static public function getCurrent() {
        $parameter = ContractStatusParameter::getCurrent();
        switch($parameter) {
            case ContractStatusParameter::ACTIVE: return self::ACTIVE;
            case ContractStatusParameter::REGISTERED: return self::REGISTERED;
            default: return self::PENDING;
        }
    }
}

abstract class ContractStatusParameter {
    const ACTIVE = "A";
    const REGISTERED = "R";

    static public function getCurrent() {
        return RequestUtilities::getRequestParamValue(UrlParameter::CONTRACT_STATUS);
    }
}

abstract class ContractType {

    const ACTIVE_EXPENSE = "active_expense";
    const REGISTERED_EXPENSE = "registered_expense";
    const ACTIVE_REVENUE = "active_revenue";
    const REGISTERED_REVENUE = "registered_revenue";
    const PENDING_EXPENSE = "pending_expense";
    const PENDING_REVENUE = "pending_revenue";

    static public function getCurrent() {

        $status = ContractStatus::getCurrent();
        $category = ContractCategory::getCurrent();
        return "{$status}_{$category}";
    }
}

abstract class noStatusExpenseContracts {

    const EXPENSE = "expense";

    static public function getCurrent() {
        $urlPath = $_GET['q'];
        $ajaxPath = $_SERVER['HTTP_REFERER'];
        $category = NULL;

        if((preg_match('/contracts_landing/',$urlPath) || preg_match('/contracts_landing/',$ajaxPath)) &&
            !(preg_match('/status/',$urlPath) || preg_match('/status/',$ajaxPath))) {
            $category = self::EXPENSE;
        }

        return $category;
    }
}

