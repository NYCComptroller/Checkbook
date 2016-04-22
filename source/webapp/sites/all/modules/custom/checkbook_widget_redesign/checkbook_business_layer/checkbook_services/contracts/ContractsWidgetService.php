<?php
/**
 * Created by PhpStorm.
 * User: atorkelson
 * Date: 4/20/16
 * Time: 4:05 PM
 */

class ContractsWidgetService extends AbstractWidgetService {

    public function implDerivedColumn($column_name,$row) {
        $value = null;
        switch($column_name) {
            case "contract_id_link":

                $column = $row['contract_number'];
                $class = "bottomContainerReload";
                $url = "/panel_html/contract_transactions/contract_details"
                    . _checkbook_project_get_url_param_string('status')
                    ."/magid/".$row['original_agreement_id']
                    ."/doctype/".$row['document_code']
                    ._checkbook_append_url_params();

                $value = "<a class='{$class}' href='{$url}'>{$column}</a>";

                break;
            case "agency_name_link":

                $column = $row['agency_name'];
                $url = "/contracts_landing"
                    ."/magid/".$row['original_agreement_id']
                    . _checkbook_append_url_params()
                    ._checkbook_project_get_url_param_string('vendor')
                    ._checkbook_project_get_url_param_string('cindustry')
                    ._checkbook_project_get_url_param_string('csize')
                    ._checkbook_project_get_url_param_string('awdmethod')
                    ._checkbook_project_get_url_param_string('status')
                    ._checkbook_project_get_year_url_param_string()
                    ."/agency/".$row['agency_id']
                    ."?expandBottomCont=true";

                $value = "<a href='{$url}'>{$column}</a>";

                break;
            case "vendor_name_link":

                $column = $row['vendor_name'];
                $url = "/contracts_landing"
//                    .ContractUtil::get_contracts_vendor_link_by_mwbe_category($row)
                    ."?expandBottomCont=true";

                $value = "<a href='{$url}'>{$column}</a>";

                break;
            case "spent_to_date_link":

                $nid = "";
                $column = $row['spending_amount_sum'];
                $class = "new_window";
                $url = "/spending/transactions"
                    ."/magid/".$row['original_agreement_id']
                    ._checkbook_append_url_params()
                    .(new ContractURLHelper())->_checkbook_project_spending_get_year_url_param_string()
                    .ContractUtil::getSpentToDateParams()
                    ."/smnid/".$nid
                    ."/newwindow";

                $value = "<a class='{$class}' href='{$url}'>{$column}</a>";

                break;
            case "award_method_name_link":

                $column = $row['award_method_name'];
                $url = "/contracts_landing"
                    ._checkbook_project_get_url_param_string('agency')
                    . _checkbook_append_url_params()
                    ._checkbook_project_get_url_param_string('vendor')
                    ._checkbook_project_get_url_param_string('status')
                    ._checkbook_project_get_url_param_string('cindustry')
                    ._checkbook_project_get_url_param_string('csize')
                    ."/awdmethod/".$row['award_method_id']
                    ._checkbook_project_get_year_url_param_string()
                    ."?expandBottomCont=true";

                $value = "<a href='{$url}'>{$column}</a>";

                break;
            case "award_size_name_link":

                $column = $row['award_size_name'];
                $url = "/contracts_landing"
                    ._checkbook_project_get_url_param_string('agency')
                    . _checkbook_append_url_params()
                    ._checkbook_project_get_url_param_string('vendor')
                    ._checkbook_project_get_url_param_string('awdmethod')
                    ._checkbook_project_get_url_param_string('status')
                    ._checkbook_project_get_url_param_string('cindustry')
                    ._checkbook_project_get_url_param_string('csize')
                    ."/csize/".$row['award_size_id']
                    ._checkbook_project_get_year_url_param_string()
                    ."?expandBottomCont=true";

                $value = "<a href='{$url}'>{$column}</a>";
                break;
            case "industry_type_name_link":

                $column = $row['industry_type_name'];
                $url = "/contracts_landing"
                    ._checkbook_project_get_url_param_string('agency')
                    . _checkbook_append_url_params()
                    ._checkbook_project_get_url_param_string('vendor')
                    ._checkbook_project_get_url_param_string('status')
                    ._checkbook_project_get_url_param_string('csize')
                    ._checkbook_project_get_url_param_string('awdmethod')
                    ."/cindustry/".$row['industry_type_id']
                    ._checkbook_project_get_year_url_param_string()
                    ."?expandBottomCont=true";

                $value = "<a href='{$url}'>{$column}</a>";

                break;
        }

        if(isset($value)) {
            return $value;
        }
        return $value;
    }

}