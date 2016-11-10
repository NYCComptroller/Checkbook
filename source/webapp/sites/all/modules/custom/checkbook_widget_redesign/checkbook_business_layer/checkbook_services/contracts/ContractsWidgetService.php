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
        $legacy_node_id = $this->getLegacyNodeId();
        switch($column_name) {
            case "contract_id_link":
                $column = $row['contract_number'];
                $class = "bottomContainerReload";
                $url = ContractsUrlService::contractIdUrl($row['original_agreement_id'],$row['document_code']);
                $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
                break;

            case "master_contract_id_link":
                $column = $row['contract_number'];
                $class = "bottomContainerReload";
                $url = ContractsUrlService::masterContractIdUrl($row['original_agreement_id'],$row['document_code']);
                $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
                break;

            case "pending_master_contract_id_link":
                $column = $row['contract_number'];
                $class = "bottomContainerReload";
                $url = ContractsUrlService::pendingMasterContractIdUrl($row['original_agreement_id'],$row['document_code'],$row['fms_contract_number'],$row['contract_number'],$row['document_version']);
                $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
                break;

            case "pending_contract_id_link":
                $column = $row['contract_number'];
                $class = "bottomContainerReload";
                $url = ContractsUrlService::pendingContractIdLink($row['original_agreement_id'],$row['document_code'],$row['fms_contract_number'],$row['contract_number'],$row['document_version']);
                $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
                break;

            case "agency_name_link":
                $column = $row['agency_name'];
                $url = ContractsUrlService::agencyUrl($row['agency_id'], $row['original_agreement_id']);
                $value = "<a href='{$url}'>{$column}</a>";
                break;

            case "award_method_name_link":
                $column = $row['award_method_name'];
                $url = ContractsUrlService::awardmethodUrl($row['award_method_id']);
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
                $url = ContractsVendorUrlService::vendorUrl($row['vendor_id'], $row['agency_id'], $year_id, $year_type, $row['minority_type_id'], $row['is_prime_or_sub']);
                $value = "<a href='{$url}'>{$column}</a>";
                break;

            case "sub_vendor_name_link":
                $column = $row['sub_vendor_name'];
                $url = ContractsVendorUrlService::getSubContractsVendorLink($row['sub_vendor_id']);
                $value = "<a href='{$url}'>{$column}</a>";
                break;

            case "master_agreements_agency_landing_link":
                $datasource = _getRequestParamValue('datasource');
                $column = $row['agency_name'];
                if($datasource == 'checkbook_oge'){
                    return $column;
                }
                $url = ContractsUrlService::agencyUrl($row['agency_id']);
                $value = "<a href='{$url}'>{$column}</a>";
                break;

            case "master_agreements_vendor_name_link":
                $datasource = _getRequestParamValue('datasource');
                $column = $row['vendor_name'];
                if($datasource == 'checkbook_oge'){
                    return $column;
                }
                $year_id = _getRequestParamValue("year");
                $year_type = _getRequestParamValue("yeartype");
                $url = ContractsVendorUrlService::vendorUrl($row['vendor_id'], $row['agency_id'], $year_id, $year_type, $row['minority_type_id'], $row['is_prime_or_sub']);
                $value = "<a href='{$url}'>{$column}</a>";
                break;

            case "industry_name_link":
                $column = $row['industry_type_name'];
                $url = ContractsUrlService::industryUrl($row['industry_type_id']);
                $value = "<a href='{$url}'>{$column}</a>";
                break;

            case "contract_size_name_link":
                $column = $row['award_size_name'];
                $url = ContractsUrlService::contractSizeUrl($row['award_size_id']);
                $value = "<a href='{$url}'>{$column}</a>";
                break;
            
            case "minority_type_name_link":
                $minority_type_id = $row['minority_type_id'];
                $column = MinorityTypeURLService::$minority_type_category_map[$minority_type_id];
                $url = ContractsUrlService::minorityTypeUrl($minority_type_id);
                $value = (isset($url))?"<a href='{$url}'>{$column}</a>" : $column;
                break;
            
            case "minority_type_name":
                $minority_type_id = isset($row['prime_minority_type_id']) ? $row['prime_minority_type_id'] : $row ['minority_type_id'] ;
                $value = MinorityTypeURLService::$minority_type_category_map[$minority_type_id];
                break;

            case "sub_minority_type_name_link":
                $column = $row['sub_minority_type_name'];
                $url = MinorityTypeURLService::getSubMinorityTypeUrl($row['sub_minority_type_id']);
                $value = (isset($url))?"<a href='{$url}'>{$column}</a>" : $column;
                break;

            // Spent to Date Links
            case "contracts_spent_to_date_link":
                $column = $row['spending_amount_sum'];
                $class = "new_window";

                $spend_type_parameter = _checkbook_check_isEDCPage()
                    ? "/agid/".$row['original_agreement_id']
                    : "/contnum/".$row['contract_number'];
                $url = ContractsUrlService::spentToDateUrl($spend_type_parameter,$legacy_node_id);
                $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
                break;

            case "prime_vendor_spent_to_date_link":
                $column = $row['spending_amount_sum'];
                $class = "new_window";

                $spend_type_parameter = "/cvendor/".$row['vendor_id'];
                $url = ContractsUrlService::spentToDateUrl($spend_type_parameter,$legacy_node_id);
                $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
                break;

            case "sub_vendor_spent_to_date_link":
                $column = $row['spending_amount_sum'];
                $class = "new_window";

                $spend_type_parameter = "/csubvendor/".$row['sub_vendor_id'];
                $url = ContractsUrlService::spentToDateUrl($spend_type_parameter,$legacy_node_id);
                $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
                break;

            case "award_method_spent_to_date_link":
                $column = $row['spending_amount_sum'];
                $class = "new_window";

                $spend_type_parameter = "/awdmethod/".$row['award_method_id'];
                $url = ContractsUrlService::spentToDateUrl($spend_type_parameter,$legacy_node_id);
                $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
                break;

            case "master_agreement_spent_to_date_link":
                $column = $row['spending_amount_sum'];
                $class = "new_window";

                $spend_type_parameter = "/magid/".$row['original_agreement_id'];
                $url = ContractsUrlService::masterAgreementSpentToDateUrl($spend_type_parameter,$legacy_node_id);
                $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
                break;

            case "industry_spent_to_date_link":
                $column = $row['spending_amount_sum'];
                $class = "new_window";

                $spend_type_parameter = "/cindustry/".$row['industry_type_id'];
                $url = ContractsUrlService::spentToDateUrl($spend_type_parameter,$legacy_node_id);
                $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
                break;

            case "contact_size_spent_to_date_link":
                $column = $row['spending_amount_sum'];
                $class = "new_window";

                $spend_type_parameter = "/csize/".$row['award_size_id'];
                $url = ContractsUrlService::spentToDateUrl($spend_type_parameter,$legacy_node_id);
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
                $doc_type = "('MMA1','MA1','MAR','CT1','CTA1','CTR')";
            }
            else if(preg_match('/pending_rev/',$urlPath)){
                $doc_type = "('RCT1')";
            }
            else {
                $doc_type = "('MA1','CTA1','CT1')";
            }
            $parameters['doctype'] = $doc_type;
        }
        return $parameters;
    }

    public function getWidgetFooterUrl($parameters) {
        return ContractsUrlService::getFooterUrl($parameters,$this->getLegacyNodeId());
    }

}
