<?php
/**
 * Created by PhpStorm.
 * User: atorkelson
 * Date: 4/8/16
 * Time: 3:58 PM
 */

class SpendingWidgetService extends AbstractWidgetService {

    public function implDerivedColumn($column_name,$row) {
        
        $value = null;
        switch($column_name) {
            case "agency_name_link":
                $column = $row['agency_name'];
                $url = SpendingUrlService::agencyUrl($row['agency_id'], $this->getLegacyNodeId());
                $value = "<a href='{$url}'>{$column}</a>";
                break;
            case "payroll_agency_name_link":
                $column = $row['agency_name'];
                $url = SpendingUrlService::payrollagencyUrl($row['agency_id'], $this->getLegacyNodeId());
                $value = "<a href='{$url}'>{$column}</a>";
                break;
            case "agency_ytd_spending_link":
                $column = $row['check_amount_sum'];
                $class = "bottomContainerReload";
                $url = SpendingUrlService::ytdSpendindUrl('agency',$row['agency_id'], $this->getLegacyNodeId());
                $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
                break;
            case "expense_cat_ytd_spending_link":
                $column = $row['check_amount_sum'];
                $class = "bottomContainerReload";
                $url = SpendingUrlService::ytdSpendindUrl('expcategory',$row['expenditure_object_id'], $this->getLegacyNodeId());
                $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
                break;
            case "department_ytd_spending_link":
                $column = $row['check_amount_sum'];
                $class = "bottomContainerReload";
                $url = SpendingUrlService::ytdSpendindUrl('dept',$row['department_id'], $this->getLegacyNodeId());
                $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
                break;
            case "contract_amount_link":
                $column = $row['check_amount_sum'];
                $class = "bottomContainerReload";
                $url = SpendingUrlService::contractAmountUrl($row, $this->getLegacyNodeId());
                $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
                break;
            case "contract_number_link":
                $column = $row['document_id'];
                $class = "new_window";
                $url = SpendingUrlService::contractIdUrl($row);
                $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
                break;
            case "mwbe_category":
                $column = isset($row['minority_type']) ? $row['minority_type'] : $row['prime_minority_type'];
                $value = MappingUtil::getMinorityCategoryById($column);
                break;
            case "mwbe_category_name": // node_id = 763
                $column = $row['minority_type'];
                $mwbe_category_name = MappingUtilities::getMinorityCategoryById($column);
                $value = $mwbe_category_name;
                break;
            case "mwbe_category_link":
                $column = $row['minority_type'];
                $mwbe_category_name = MappingUtilities::getMinorityCategoryById($column);
                $url = SpendingUrlService::mwbeUrl($column);
                $value = (RequestUtilities::isNewWindow() || !MappingUtilities::isMWBECertified(array($column)))  ? $mwbe_category_name  : "<a href= '{$url}'>{$mwbe_category_name}</a>";
                break;
            case "contract_vendor_name_link":
                $column = $row['vendor_name'];
                $class = "bottomContainerReload";
                $url = SpendingVendorUrlService::vendorUrl($row);
                $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
                break;
            case "vendor_name_link":
                $vendor_id = isset($row["prime_vendor_id"]) ? $row["prime_vendor_id"] : $row["vendor_id"];
                $column = $row['vendor_name'];
                if(isset($row['prime_vendor_id'])){
                    $url = SpendingUrlService::primevendorUrl($vendor_id);
                }else{
                    $url = SpendingUrlService::vendorUrl($vendor_id);
                }
                $value = "<a href='{$url}'>{$column}</a>";
                break;
            case "sub_vendor_name_link":
                $column = $row['sub_vendor_name'];
                $url = "";
                $value = "<a href='{$url}'>{$column}</a>";
                break;
            case "prime_fvendor_ytd_spending_link":
                $vendor_id = isset($row["prime_vendor_id"]) ? $row["prime_vendor_id"] : $row["vendor_id"];
                $column = $row['check_amount_sum'];
                $class = "bottomContainerReload";
                $url = SpendingUrlService::ytdSpendindUrl('fvendor',$vendor_id, $this->getLegacyNodeId());
                $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
                break;
            case "prime_vendor_ytd_spending_link":
                $vendor_id = isset($row["prime_vendor_id"]) ? $row["prime_vendor_id"] : $row["vendor_id"];
                $column = $row['check_amount_sum'];
                $class = "bottomContainerReload";
                $url = SpendingUrlService::ytdSpendindUrl('vendor',$vendor_id, $this->getLegacyNodeId());
                $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
                break;
            case "sub_vendor_ytd_spending_link":
                $column = isset($row['check_amount_sum']) ? $row['check_amount_sum'] : $row['ytd_spending_sub_vendors'];
                $class = "bottomContainerReload";
                $url = SpendingUrlService::getSubVendorYtdSpendingUrl($this->getLegacyNodeId(), $row);
                $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
                break;
            case "checks_vendor_link":
                $vendor_id = $row["vendor_id"];
                $column = $row['vendor_name'];
                $citywide_vendor_name_link = SpendingVendorUrlService::getPrimeVendorNameLinkUrl($row);
                $oge_vendor_link = SpendingVendorUrlService::getOGEPrimeVendorNameLinkUrl($vendor_id);
                $url = RequestUtilities::_checkbook_check_isEDCPage() ? $oge_vendor_link : $citywide_vendor_name_link;
                $value = ($row['expense_category'] == 'Payroll Summary') ? $column : "<a href='{$url}'>{$column}</a>";
                break;
            case "sub_vendor_link":
                $vendor_id = $row["vendor_id"];
                $column = $row['sub_vendor_name'];
                $url =  SpendingVendorUrlService::getSubVendorNameLinkUrl($row);
                $value = $vendor_id == null ? $column : "<a href='{$url}'>{$column}</a>";
                break;
            case "prime_vendor_link":
                $vendor_id = $row['prime_vendor_id'];
                $column = $row['prime_vendor_name'];
                $url =  SpendingVendorUrlService::getPrimeVendorNameLinkUrl($row);
                $value = $vendor_id == null ? $column : "<a href='{$url}'>{$column}</a>";
                break;
            case "agencies_ytd_spending_subvendors_link":
                $column = $row['ytd_spending_sub_vendors'];
                $class = "bottomContainerReload";
                $url =  SpendingUrlService::agenciesYtdSpendingSubvendorsUrl($row, $this->getLegacyNodeId());
                $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
                break;
            case "sub_contracts_ytd_spending_link":
                $column = isset($row['check_amount_sum']) ? $row['check_amount_sum'] : $row['ytd_spending_sub_vendors'];
                $class = "bottomContainerReload";
                $url = SpendingUrlService::getSubContractAmountLinkUrl($this->getLegacyNodeId(), $row);
                $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
                break;
            case "industry_name_link":
                $column = $row['industry_type_name'];
                $url = SpendingUrlService::industryUrl($row['industry_type_id'], $this->getLegacyNodeId());
                $value = "<a href='{$url}'>{$column}</a>";
                break;
            case "industry_ytd_spending_link":
                $column = $row['check_amount_sum'];
                $url = SpendingUrlService::ytdSpendindUrl('industry', $row['industry_type_id'], $this->getLegacyNodeId());
                $class = "bottomContainerReload";
                $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
                break;
            case "contract_purpose_formatted":
                $column = $row['description'];
                $value = (strlen($column) > 0) ? _get_tooltip_markup($column, 40) : 'N/A';
                break;
        }

        if(isset($value)) {
            return $value;
        }
        return $value;
    }
    
    public function getWidgetFooterUrl($parameters) {
        return SpendingUrlService::getFooterUrl($parameters,$this->getLegacyNodeId());
    }
    
    public function adjustParameters($parameters, $urlPath) {
        return $parameters;
    }
}
