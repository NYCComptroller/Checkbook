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
        
    }
    
    public function adjustParameters($parameters, $urlPath) {

    }

}
