<?php

class NychaContractsWidgetService extends WidgetDataService implements IWidgetService {
    /**
     * Function to allow the client to initialize the data service
     * @return mixed
     */
    public function initializeDataService() {
        return new NychaContractsDataService();
    }

    public function implementDerivedColumn($column_name,$row) {
        $value = null;

        switch($column_name) {
            case "vendor_link":
                $column = $row['vendor_name'];
                $url = NychaContractsUrlService::generateLandingPageUrl('vendor',$row['vendor_id']);
                $value = "<a href='{$url}'>{$column}</a>";
                break;
            case "size_link":
                $column = $row['award_size_name'];
                $url = NychaContractsUrlService::generateLandingPageUrl('csize',$row['award_size_id']);
                $value = "<a href='{$url}'>{$column}</a>";
                break;
            case "industry_link":
                $column = $row['industry_type_name'];
                $url = NychaContractsUrlService::generateLandingPageUrl('industry',$row['industry_type_id']);
                $value = "<a href='{$url}'>{$column}</a>";
                break;
            case "award_method_link":
                $column = $row['award_method_name'];
                $url = NychaContractsUrlService::generateLandingPageUrl('awdmethod',$row['award_method_id']);
                $value = "<a href='{$url}'>{$column}</a>";
                break;
            case "contract_id_link":
                $contract_id = isset($row['contract_id']) && $row['contract_id'] ? $row['contract_id']: $row['purchase_order_number'];
                $url = NychaContractsUrlService::contractDetailsUrl($contract_id);
                $value = "<a href='{$url}'>{$contract_id}</a>";
                break;

            ## NYCHA Contracts Invoiced Amount Links - linkc to nycha spending transactions
            case "ba_inv_link":
              $ba_inv_amount = $row['spend_to_date'];
              $agreement_type='/agreement_type/BA';
              $tcode = '/tcode/BA';
              $dynamic_parameter = "/po_num_exact/" . $row["contract_id"];
              //$class = "bottomContainerReload";
              $class = "new_window";
              $url = NYCHASpendingUrlService::invContractSpendingUrl($dynamic_parameter, 'inv_contract',$agreement_type,$tcode);
              $value = "<a class='{$class}' href='{$url}'>{$ba_inv_amount}</a>";
              break;
            case "bam_inv_link":
              $bam_inv_amount = $row['spend_to_date'];
              $agreement_type='/agreement_type/BA';
              $tcode = '/tcode/BAM';
              $dynamic_parameter = "/po_num_exact/" . $row["contract_id"];
              $class = "bottomContainerReload";
              $class = "new_window";
              $url = NYCHASpendingUrlService::invContractSpendingUrl($dynamic_parameter, 'inv_contract',$agreement_type,$tcode);
              $value = "<a class='{$class}' href='{$url}'>{$bam_inv_amount}</a>";
              break;
            case "pa_inv_link":
              $pa_inv_amount = $row['spend_to_date'];
              $agreement_type='/agreement_type/PA';
              $tcode = '/tcode/PA';
              $dynamic_parameter = "/po_num_exact/" . $row["contract_id"];
              $class = "bottomContainerReload";
              $class = "new_window";
              $url = NYCHASpendingUrlService::invContractSpendingUrl($dynamic_parameter, 'inv_contract',$agreement_type,$tcode);
              $value = "<a class='{$class}' href='{$url}'>{$pa_inv_amount}</a>";
              break;
            case "pam_inv_link":
              $pam_inv_amount = $row['spend_to_date'];
              $agreement_type='/agreement_type/PA';
              $tcode = '/tcode/PAM';
              $dynamic_parameter = "/po_num_exact/" . $row["contract_id"];
              $class = "bottomContainerReload";
              $class = "new_window";
              $url = NYCHASpendingUrlService::invContractSpendingUrl($dynamic_parameter, 'inv_contract',$agreement_type,$tcode);
              $value = "<a class='{$class}' href='{$url}'>{$pam_inv_amount}</a>";
              break;
            case "po_inv_link":
              $po_inv_amount = $row['spend_to_date'];
              $agreement_type='/agreement_type/PO';
              $tcode = '/tcode/PO';
              $dynamic_parameter = "/po_num_exact/" . $row["contract_id"];
              $class = "bottomContainerReload";
              $class = "new_window";
              $url = NYCHASpendingUrlService::invContractSpendingUrl($dynamic_parameter, 'inv_contract',$agreement_type,$tcode);
              $value = "<a class='{$class}' href='{$url}'>{$po_inv_amount}</a>";
              break;
            case "vendor_inv_link":
              $vendor_inv_amount = $row['spend_to_date'];
              $agreement_type="";
              $tcode = '/tcode/VO';
              $dynamic_parameter = "/vendor/" . $row['vendor_id'];
              $class = "bottomContainerReload";
              $class = "new_window";
              $url = NYCHASpendingUrlService::invContractSpendingUrl($dynamic_parameter, 'inv_contract',$agreement_type,$tcode);
              $value = "<a class='{$class}' href='{$url}'>{$vendor_inv_amount}</a>";
              break;
            case "awd_inv_link":
              $awd_inv_amount = $row['spend_to_date'];
              $agreement_type="";
              $tcode = '/tcode/AWD';
              $dynamic_parameter = "/awdmethod/" . $row['award_method_id'];
              $class = "bottomContainerReload";
              $class = "new_window";
              $url = NYCHASpendingUrlService::invContractSpendingUrl($dynamic_parameter, 'inv_contract',$agreement_type,$tcode);
              $value = "<a class='{$class}' href='{$url}'>{$awd_inv_amount}</a>";
              break;
            case "dpt_inv_link":
              $dpt_inv_amount = $row['spend_to_date'];
              $agreement_type="";
              $tcode = '/tcode/DEP';
              $dynamic_parameter = "/department/" . $row['department_id'];
              $class = "bottomContainerReload";
              $class = "new_window";
              $url = NYCHASpendingUrlService::invContractSpendingUrl($dynamic_parameter, 'inv_contract',$agreement_type,$tcode);
              $value = "<a class='{$class}' href='{$url}'>{$dpt_inv_amount}</a>";
              break;
            case "rsp_inv_link":
              $rsp_inv_amount = $row['spend_to_date'];
              $agreement_type="";
              $tcode = '/tcode/RESC';
              $dynamic_parameter = "/respcenter/" . $row['responsibility_center_id'];
              $class = "bottomContainerReload";
              $class = "new_window";
              $url = NYCHASpendingUrlService::invContractSpendingUrl($dynamic_parameter, 'inv_contract',$agreement_type,$tcode);
              $value = "<a class='{$class}' href='{$url}'>{$rsp_inv_amount}</a>";
              break;
            case "ind_inv_link":
              $ind_inv_amount = $row['spend_to_date'];
              $agreement_type="";
              $tcode = '/tcode/IND';
              $dynamic_parameter = "/industry/" . $row['industry_type_id'];
              $class = "bottomContainerReload";
              $class = "new_window";
              $url = NYCHASpendingUrlService::invContractSpendingUrl($dynamic_parameter, 'inv_contract',$agreement_type,$tcode);
              $value = "<a class='{$class}' href='{$url}'>{$ind_inv_amount}</a>";
              break;
            case "award_size_inv_link":
              $award_size_inv_amount = $row['spend_to_date'];
              $agreement_type="";
              $tcode = '/tcode/SZ';
              $dynamic_parameter = "/csize/" . $row['award_size_id'];
              $class = "bottomContainerReload";
              $class = "new_window";
              $url = NYCHASpendingUrlService::invContractSpendingUrl($dynamic_parameter, 'inv_contract',$agreement_type,$tcode);
              $value = "<a class='{$class}' href='{$url}'>{$award_size_inv_amount}</a>";
              break;
        }

        if(isset($value)) {
            return $value;
        }
        return $value;
    }

    public function adjustParameters($parameters, $urlPath) {
        return $parameters;
    }

    public function getWidgetFooterUrl($parameters) {
        return NychaContractsUrlService::getFooterUrl($parameters);
    }
}
