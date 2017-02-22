<?php
/**
 * Created by PhpStorm.
 * User: sgade
 * Date: 02/22/17
 * Time: 2:05 PM
 */

class BudgetWidgetService extends WidgetSqlService implements IWidgetService {

    public function implementDerivedColumn($column_name,$row) {
        $value = null;
        $legacy_node_id = $this->getLegacyNodeId();
        switch($column_name) {
           case "agency_name_link":
                $column = $row['agency_name'];
                $url = "";
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
        return BudgetUrlService::getFooterUrl($parameters,$this->getLegacyNodeId());
    }
}
