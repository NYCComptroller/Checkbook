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
                $url = ContractsUrlService::contractIdUrl($row['original_agreement_id'],$row['document_code']);
                $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
                break;

            case "agency_name_link":
                $column = $row['agency_name'];
                $url = ContractsUrlService::agencyUrl($row['agency_id'], $row['original_agreement_id']);
                $value = "<a href='{$url}'>{$column}</a>";
                break;
            
            case "agency_landing_link":
                $column = $row['agency_name'];
                $url = ContractsUrlService::agencyUrl($row['agency_id']);
                $value = "<a href='{$url}'>{$column}</a>";
                break;

            case "vendor_name_link":
                $column = $row['vendor_name'];
                $year_id = _getRequestParamValue("year");
                $year_type = _getRequestParamValue("yeartype");
                $url = ContractsUrlService::vendorUrl($row['vendor_id'], $row['agency_id'], $year_id, $year_type, $row['minority_type_id'], $row['is_prime_or_sub']);
                $value = "<a href='{$url}'>{$column}</a>";
                break;

            case "industry_name_link":
                $column = $row['industry_type_name'];
                $url = ContractsUrlService::industryUrl($row['industry_type_id']);
                $value = "<a href='{$url}'>{$column}</a>";
                break;

            // Spent to Date Links
            case "contracts_spent_to_date_link":
                $column = $row['spending_amount_sum'];
                $class = "new_window";

                $spend_type_parameter = _checkbook_check_isEDCPage()
                    ? "/agid/".$row['original_agreement_id']
                    : "/contnum/".$row['contract_number'];
                $url = ContractsUrlService::spentToDateUrl($spend_type_parameter);
                $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
                break;

            case "prime_vendor_spent_to_date_link":
                $column = $row['spending_amount_sum'];
                $class = "new_window";

                $spend_type_parameter = "/cvendor/".$row['vendor_id'];
                $url = ContractsUrlService::spentToDateUrl($spend_type_parameter);
                $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
                break;

            case "award_method_spent_to_date_link":
                $column = $row['spending_amount_sum'];
                $class = "new_window";

                $spend_type_parameter = "/awdmethod/".$row['award_method_id'];
                $url = ContractsUrlService::spentToDateUrl($spend_type_parameter);
                $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
                break;

            case "industry_spent_to_date_link":
                $column = $row['spending_amount_sum'];
                $class = "new_window";

                $spend_type_parameter = "/cindustry/".$row['industry_type_id'];
                $url = ContractsUrlService::spentToDateUrl($spend_type_parameter);
                $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
                break;

            case "contact_size_spent_to_date_link":
                $column = $row['spending_amount_sum'];
                $class = "new_window";

                $spend_type_parameter = "/csize/".$row['award_size_id'];
                $url = ContractsUrlService::spentToDateUrl($spend_type_parameter);
                $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
                break;
        }

        if(isset($value)) {
            return $value;
        }
        return $value;
    }

    public function adjustParameters($parameters, $urlPath) {

        //contract category or doc type is derived from the page path
        $doc_type = $parameters['doctype'];
        if(!isset($doc_type)) {
            if(preg_match('/revenue/',$urlPath)){
                $doc_type =  "('RCT1')";
            }
            else if(preg_match('/pending_exp/',$urlPath)){
                $doc_type = "('MMA1', 'MA1', 'MAR', 'CT1', 'CTA1', 'CTR')";
            }
            else if(preg_match('/pending_rev/',$urlPath)){
                $doc_type = "('RCT1')";
            }
            else {
                $doc_type = "('MA1', 'CTA1', 'CT1')";
            }
            $parameters['doctype'] = $doc_type;
        }
        return $parameters;
    }

}