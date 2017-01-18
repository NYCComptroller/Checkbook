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
                $url = '/revenue'.RequestUtilities::_getUrlParamString('year')
                  .RequestUtilities::_getUrlParamString('yeartype')
                  .'/agency/'.$row['agency_id'];
                $value = "<a href='{$url}'>{$column}</a>";
                break;
            case "agency_revenue_amount_sum":
                $column = $row['revenue_amount_sum'];
                $url = RevenueUrlService::getRecognizedAmountUrl('agency', $row['agency_id'], $this->getLegacyNodeId());
                $class = "bottomContainerReload";
                $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
                break;
            case "revcat_recognized_amount_link":
                $column = $row['recognized_amount'];
                $url = RevenueUrlService::getRecognizedAmountUrl('revcat', $row['revenue_category_id'], $this->getLegacyNodeId());
                $class = "bottomContainerReload";
                $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
                break;
            case "agency_recognized_amount_link":
                $column = $row['recognized_amount'];
                $url = RevenueUrlService::getRecognizedAmountUrl('agency', $row['agency_id'], $this->getLegacyNodeId());
                $class = "bottomContainerReload";
                $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
                break;
            case "cross_agency_recognized_amount_link":
                $column = $row['current_recognized'];
                $url = RevenueUrlService::getRecognizedAmountUrl('agency', $row['agency_id'], $this->getLegacyNodeId(), 0);
                $class = "bottomContainerReload";
                $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
                break;
            case "cross_agency_recognized_amount_link_1":
                $column = $row['recognized_1'];
                $url = RevenueUrlService::getRecognizedAmountUrl('agency', $row['agency_id'], $this->getLegacyNodeId(), 1);
                $class = "bottomContainerReload";
                $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
                break;
            case "cross_agency_recognized_amount_link_2":
                $column = $row['recognized_2'];
                $url = RevenueUrlService::getRecognizedAmountUrl('agency', $row['agency_id'], $this->getLegacyNodeId(), 2);
                $class = "bottomContainerReload";
                $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
                break;
            case "cross_rev_cat_recognized_amount_link":
                $column = $row['current_recognized'];
                $url = RevenueUrlService::getRecognizedAmountUrl('revcat', $row['revenue_category_id'], $this->getLegacyNodeId(), 0);
                $class = "bottomContainerReload";
                $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
                break;
            case "cross_rev_cat_recognized_amount_link_1":
                $column = $row['recognized_1'];
                $url = RevenueUrlService::getRecognizedAmountUrl('revcat', $row['revenue_category_id'], $this->getLegacyNodeId(), 1);
                $class = "bottomContainerReload";
                $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
                break;
            case "cross_rev_cat_recognized_amount_link_2":
                $column = $row['recognized_2'];
                $url = RevenueUrlService::getRecognizedAmountUrl('revcat', $row['revenue_category_id'], $this->getLegacyNodeId(), 2);
                $class = "bottomContainerReload";
                $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
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
