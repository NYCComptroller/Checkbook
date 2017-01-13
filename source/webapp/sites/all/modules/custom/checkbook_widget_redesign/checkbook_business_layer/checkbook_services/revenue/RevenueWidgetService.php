<?php
/**
 * Created by PhpStorm.
 * User: sgade
 * Date: 01/10/16
 * Time: 2:05 PM
 */

class RevenueWidgetService extends AbstractWidgetService {

    public function implDerivedColumn($column_name,$row) {
        $value = null;
        $legacy_node_id = $this->getLegacyNodeId();
        switch($column_name) {
           case "agency_name_link":
                $column = $row['agency_name'];
                $url = '';
                $value = "<a href='{$url}'>{$column}</a>";
                break;
            case "recognized_amount_link":
                $column = $row['recognized_amount'];
                $url = RevenueUrlService::getRecognizedAmountUrl('revcat', $row['revenue_category_id'], $this->getLegacyNodeId());
                $value = "<a href='{$url}'>{$column}</a>";
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
        return RevenueUrlService::getFooterUrl($parameters,$this->getLegacyNodeId());
    }
}