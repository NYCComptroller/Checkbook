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
            //url needs to be built for below cases
            case "recognized_amount_link":
                $column = $row['revenue_amount_sum'];
                $url = '/revenue/transactions'.RequestUtilities::_getUrlParamString('year');
                  //?
                $value = "<a href='{$url}'>{$column}</a>";
                break;
            case "current_recognized_link":
                $column = $row['current_recognized'];
                $url = '/revenue/transactions'.RequestUtilities::_getUrlParamString('year');
                  //?
                $value = "<a href='{$url}'>{$column}</a>";
                break;
            case "reognized_1_link":
                $column = $row['recognized_1'];
                $url = '/revenue/transactions'.RequestUtilities::_getUrlParamString('year');
                  //?
                $value = "<a href='{$url}'>{$column}</a>";
                break;
            case "recognized_2_link":
                $column = $row['recognized_2'];
                $url = '/revenue/transactions'.RequestUtilities::_getUrlParamString('year');
                  //?
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
