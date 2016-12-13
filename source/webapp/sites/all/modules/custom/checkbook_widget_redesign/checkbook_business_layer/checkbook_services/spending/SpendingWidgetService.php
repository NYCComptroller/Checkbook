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
            case "vendor_name_link":
                $datasource = _getRequestParamValue("datasource");
                $dashboard = _getRequestParamValue("dashboard");
                $column = $row['vendor_name'];
                if(!isset($datasource) && !isset($dashboard)) return $column;
                $url = "";
                $value = "<a href='{$url}'>{$column}</a>";
                break;
            case "sub_vendor_name_link":
                $column = $row['sub_vendor_name'];
                $url = "";
                $value = "<a href='{$url}'>{$column}</a>";
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
