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
                $url = SpendingUrlService::agencyUrl($row['agency_id']);
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
            case "mwbe_category":
                $column = $row['minority_type'];
                $value = MappingUtil::getMinorityCategoryById($column);
                break;
            case "mwme_category_link":
                $column = $row['minority_type'];
                $mwbe_category_name = MappingUtil::getMinorityCategoryById($column);
                $url = SpendingUrlService::mwbeUrl($column);
                $value = RequestUtil::isNewWindow() || !MappingUtil::isMWBECertified(array($column)) ? $mwbe_category_name  : "<a href= '{$url}'>{$mwbe_category_name}</a>";
                break;
            case "vendor_name_link":
                $vendor_id = isset($row["prime_vendor_id"]) ? $row["prime_vendor_id"] : $row["vendor"];
                $column = $row['vendor_name'];
                if(isset($row['prime_vendor_id'])){
                    $url = '/spending_landing'
                        .RequestUtilities::_getUrlParamString('category')
                        ._checkbook_project_get_year_url_param_string()
                        . '/vendor/'. $vendor_id;
                }else{
                    $url = '/spending_landing'
                        .RequestUtilities::_getUrlParamString('category')
                        .RequestUtilities::_appendMWBESubVendorDatasourceUrlParams()
                        ._checkbook_project_get_year_url_param_string()
                        . '/vendor/'. $vendor_id;
                }
                $value = "<a href='{$url}'>{$column}</a>";
                break;
            case "sub_vendor_name_link":
                $column = $row['sub_vendor_name'];
                $url = "";
                $value = "<a href='{$url}'>{$column}</a>";
                break;
            case "prime_vendor_ytd_spending_link":
                $vendor_id = isset($row["prime_vendor_id"]) ? $row["prime_vendor_id"] : $row["vendor"];
                $column = $row['check_amount_sum'];
                $class = "bottomContainerReload";
                $url = SpendingUrlService::ytdSpendindUrl('vendor',$vendor_id, $this->getLegacyNodeId());
                $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
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
