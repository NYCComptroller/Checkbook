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

        if(preg_match('/contracts_revenue/',$urlPath) || preg_match('/contracts_revenue/',$ajaxPath)
            || preg_match('/contracts_revenue_landing/',$urlPath) || preg_match('/contracts_revenue_landing/',$ajaxPath)
            || preg_match('/contracts_pending_rev/',$urlPath) || preg_match('/contracts_pending_rev/',$ajaxPath)) {
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
            default:
                //Todo: Fix Sub Vendor 3rd Nav to be Active
                return Dashboard::isSubDashboard() ? self::ACTIVE : self::PENDING;
        }
    }
}

abstract class ContractStatusParameter {
    const ACTIVE = "A";
    const REGISTERED = "R";
    const PENDING = "P";

    static public function getCurrent() {
<<<<<<< HEAD
=======
        $database = _getRequestParamValue('datasource');
>>>>>>> release/checkbook_4.13.0
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

<<<<<<< HEAD
abstract class subVendorContractsByPrimeVendor {
=======
abstract class noStatusExpenseContracts {
>>>>>>> release/checkbook_4.13.0

    const EXPENSE = "expense";

    static public function getCurrent() {
        $urlPath = $_GET['q'];
        $ajaxPath = $_SERVER['HTTP_REFERER'];
        $category = NULL;

        if((preg_match('/contracts_landing/',$urlPath) || preg_match('/contracts_landing/',$ajaxPath)) &&
<<<<<<< HEAD
            (preg_match('/bottom_slider/',$urlPath) || preg_match('/bottom_slider/',$ajaxPath))) {
=======
            !(preg_match('/status/',$urlPath) || preg_match('/status/',$ajaxPath))) {
>>>>>>> release/checkbook_4.13.0
            $category = self::EXPENSE;
        }

        return $category;
    }
}

abstract class DocumentType {

    const CT1 = "General Contract";
    const CTA1 = "Multiple Award Contract";
    const CTA2 = "Consortium Contract";
    const DO1 = "Delivery Order";
    const MA1 = "Master agreement";
    const MMA1 = "Multiple Award Master Agreement";
    const RCT1 = "Revenue Contract";
    const CTR = "Pending General Contract";
}

abstract class DocumentCode {

    const CT1 = "CT1";
    const CTA1 = "CTA1";
    const CTA2 = "CTA2";
    const DO1 = "DO1";
    const MA1 = "MA1";
    const MMA1 = "MMA1";
    const RCT1 = "RCT1";
    const CTR = "CTR";

    static public function isMasterAgreement($documentCode) {
        return $documentCode == self::MA1 || $documentCode == self::MMA1;
    }
}

abstract class DocumentCodeId {

    const CT1 = 1;
    const CTA1 = 2;
    const CTA2 = 3;
    const DO1 = 4;
    const MA1 = 5;
    const MMA1 = 6;
    const RCT1 = 7;
    const CTR = 20;

    static public function isMasterAgreement($documentCodeId) {
        return $documentCodeId == self::MA1 || $documentCodeId == self::MMA1;
    }
}
