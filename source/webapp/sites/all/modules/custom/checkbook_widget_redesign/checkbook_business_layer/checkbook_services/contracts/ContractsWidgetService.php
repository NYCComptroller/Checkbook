<?php

class ContractsWidgetService extends WidgetDataService implements IWidgetService {

    /**
     * Function to allow the client to initialize the data service
     * @return mixed
     */
    public function initializeDataService() {
        return new ContractsDataService();
    }

    public function implementDerivedColumn($column_name,$row) {
        $value = null;
        $legacy_node_id = $this->getLegacyNodeId();
        $data_source = RequestUtilities::get('datasource');

        switch($column_name) {
            case "contract_id_link":
                $column = $row['contract_number'];
                $class = "bottomContainerReload";
                if(ContractStatus::getCurrent() == ContractStatus::PENDING)
                    $url = ContractsUrlService::pendingContractIdLink($row['original_agreement_id'],$row['document_code'],$row['pending_contract_number'],$row['contract_number'],$row['document_version']);
                else
                    $url = ContractsUrlService::contractIdUrl($row['original_agreement_id'],$row['document_code']);

                $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
                break;

            case "master_contract_id_link":
                $column = $row['contract_number'];
                $class = "bottomContainerReload";
                if(ContractStatus::getCurrent() == ContractStatus::PENDING)
                    $url = ContractsUrlService::pendingMasterContractIdUrl($row['original_agreement_id'],$row['document_code'],$row['pending_contract_number'],$row['contract_number'],$row['document_version']);
                else
                    $url = ContractsUrlService::masterContractIdUrl($row['original_agreement_id'],$row['document_code']);
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

            case "prime_vendor_link":
                $column = $row['vendor_name'];
                $url = ContractsUrlService::primeVendorUrl($row['vendor_id']);
                $value = "<a href='{$url}'>{$column}</a>";
                break;

            case "sub_vendor_link":
                $column = $row['sub_vendor_name'];
                $url = ContractsUrlService::subVendorUrl($row['sub_vendor_id']);
                $value = "<a href='{$url}'>{$column}</a>";
                break;

            case "master_agreements_agency_landing_link":
                $column = $row['agency_name'];
                if($data_source == 'checkbook_oge'){
                    return $column;
                }
                $url = ContractsUrlService::agencyUrl($row['agency_id']);
                $value = "<a href='{$url}'>{$column}</a>";
                break;

            case "master_agreements_vendor_name_link":
                $column = $row['vendor_name'];
                if($data_source == 'checkbook_oge'){
                    return $column;
                }
                $url = ContractsUrlService::primeVendorUrl($row['vendor_id'], null, true, $row);
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

            case "prime_mwbe_category_link":
                $minority_type_id = isset($row['prime_minority_type_id']) ? $row['prime_minority_type_id'] : $row ['minority_type_id'] ;
                $column = MinorityTypeService::$minority_type_category_map[$minority_type_id];
                $url = ContractsUrlService::primeMinorityTypeUrl($minority_type_id);
                $value = (isset($url))?"<a href='{$url}'>{$column}</a>" : $column;
                break;

            case "minority_type_name":
                $minority_type_id = isset($row['prime_minority_type_id']) ? $row['prime_minority_type_id'] : $row ['minority_type_id'] ;
                $value = MinorityTypeService::$minority_type_category_map[$minority_type_id];
                break;

            case "sub_mwbe_category_link":
                $column = MinorityTypeService::$minority_type_category_map[$row['sub_minority_type_id']];
                $url = ContractsUrlService::subMinorityTypeUrl($row['sub_minority_type_id']);
                $value = (isset($url))?"<a href='{$url}'>{$column}</a>" : $column;
                break;

            // Spent to Date Links
            case "contracts_spent_to_date_link":
                $column = $row['spending_amount_sum'];
                $class = "new_window";

                $spend_type_parameter = _checkbook_check_isEDCPage()
                    ? "/agid/".$row['original_agreement_id']."/cvendor/".$row['vendor_id']
                    : "/contnum/".$row['contract_number'];
                $url = ContractsUrlService::spentToDateUrl($spend_type_parameter,$legacy_node_id);
                $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
                break;

            case "sub_contracts_spent_to_date_link":
                $column = $row['spending_amount_sum'];
                $class = "new_window";

                $spend_type_parameter = "/agid/".$row['sub_contract_original_agreement_id'];
                $url = ContractsUrlService::spentToDateUrl($spend_type_parameter,$legacy_node_id);
                $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
                break;

            case "agency_spent_to_date_link":
                $column = $row['spending_amount_sum'];
                $class = "new_window";

                $spend_type_parameter = "/cagency/".$row['agency_id'];
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

        $status = ContractStatus::getCurrent();
        if($status == ContractStatus::ACTIVE) {
            $parameters['effective_year'] = $parameters['year'];
        }
        else if($status == ContractStatus::REGISTERED) {
            $parameters['registered_year'] = $parameters['year'];
        }

        $doc_type = $parameters['doctype'];
        if(!isset($doc_type)) {
            $contractType = $parameters['contract_type'];
            $category = ContractCategory::getCurrent();
            $parameters['contract_status'] = $status;
            $parameters['doctype']  = $this->deriveDocumentCode($category, $status, $contractType);
        }
        return $parameters;
    }

    public function getWidgetFooterUrl($parameters) {
        return ContractsUrlService::getFooterUrl($parameters,$this->getLegacyNodeId());
    }

    private function deriveDocumentCode($category, $status, $contractType = "all") {

        $docCode = null;

        switch($contractType) {
            case "master_agreement":
                $docCode =
                    $category == ContractCategory::REVENUE
                        ? "('RCT1')"
                        : ($status == ContractStatus::PENDING
                            ? "('MA1','MMA1','MAR')"
                            : "('MMA1','MA1')");
                break;

            case "child_contract":
                $docCode =
                    $category == ContractCategory::REVENUE
                        ? "('RCT1')"
                        : ($status == ContractStatus::PENDING
                            ? "('CT1','CTA1','CTR')"
                            : "('CTA1','CT1')");
                break;

            default:
                $docCode =
                    $category == ContractCategory::REVENUE
                        ? "('RCT1')"
                        : ($status == ContractStatus::PENDING
                            ? "('MMA1','MA1','CTA1','CT1','MAR','CTR')"
                            : "('MA1','CTA1','CT1')");
                break;

        }
        return $docCode;
    }
}
