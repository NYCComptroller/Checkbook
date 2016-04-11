<?php
/**
 * Created by PhpStorm.
 * User: atorkelson
 * Date: 4/8/16
 * Time: 3:58 PM
 */

class ChecksWidgetService extends AbstractWidgetService {

    public function implDerivedColumns($column_name,$row) {
        $value = null;
//        switch($column_name) {
//            case "agency_name":
//                $value = "test";
////                            '<a href="/spending_landing'
////                            .  _checkbook_project_get_url_param_string('vendor')
////                            . _checkbook_project_get_url_param_string('category')
////                            . _checkbook_project_get_year_url_param_string()
////                            . _checkbook_append_url_params()
////                            . '/agency/' . $row['agency_id'] . '">' . _get_tooltip_markup($row['agency_name']) . '</a>';
//                break;
//            case "vendor_name":
//                $value = "test";
////                            $row['expenditure_object_name'] == 'Payroll Summary'
////                                ? _get_tooltip_markup($row['vendor_name'],36)
////                                : '<a href=\"' . SpendingUtil::getPrimeVendorNameLinkUrl(null,$row) . '\">' . $row['vendor_name_formatted'] . '</a>';
//                break;
//            case "check_amount":
//                $value = "custom_number_formatter_format($row[check_amount],2,'$')";
//                break;
//        }

        if(isset($value)) {
            return $value; //do something
        }
        return $value;
    }

}