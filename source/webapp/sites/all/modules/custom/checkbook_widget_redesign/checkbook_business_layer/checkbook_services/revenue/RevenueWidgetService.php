<?php
/**
 * Created by PhpStorm.
 * User: sgade
 * Date: 01/10/16
 * Time: 2:05 PM
 */

class RevenueWidgetService extends WidgetSqlService implements IWidgetService {

    public function implementDerivedColumn($column_name,$row) {
        $value = null;
        $legacy_node_id = $this->getLegacyNodeId();
        switch($column_name) {
            case "agency_name_link":
                $column = $row['agency_name'];
                $url = RevenueUrlService::getAgencyUrl($row['agency_id'], $this->getLegacyNodeId());
                $value = "<a href='{$url}'>{$column}</a>";
                break;
            case "agency_recognized_amount_link":
                $column = $row['revenue_amount_sum'];
                $url = RevenueUrlService::getRecognizedAmountUrl('agency', $row['agency_id'], $this->getLegacyNodeId());
                $class = "bottomContainerReload";
                $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
                break;
            case "funding_recognized_amount_link":
                $column = $row['recognized_amount'];
                $url = RevenueUrlService::getRecognizedAmountUrl('fundsrccode', $row['funding_class_code'], $this->getLegacyNodeId());
                $class = "bottomContainerReload";
                $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
                break;
            case "revcat_recognized_amount_link":
                $column = $row['recognized_amount'];
                $url = RevenueUrlService::getRecognizedAmountUrl('revcat', $row['revenue_category_id'], $this->getLegacyNodeId());
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
            case "cross_fund_recognized_amount_link":
                $column = $row['current_recognized'];
                $url = RevenueUrlService::getRecognizedAmountUrl('fundsrccode', $row['funding_class_code'], $this->getLegacyNodeId(), 0);
                $class = "bottomContainerReload";
                $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
                break;
            case "cross_fund_recognized_amount_link_1":
                $column = $row['recognized_1'];
                $url = RevenueUrlService::getRecognizedAmountUrl('fundsrccode', $row['funding_class_code'], $this->getLegacyNodeId(), 1);
                $class = "bottomContainerReload";
                $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
                break;
            case "cross_fund_recognized_amount_link_2":
                $column = $row['recognized_2'];
                $url = RevenueUrlService::getRecognizedAmountUrl('fundsrccode', $row['funding_class_code'], $this->getLegacyNodeId(), 2);
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
