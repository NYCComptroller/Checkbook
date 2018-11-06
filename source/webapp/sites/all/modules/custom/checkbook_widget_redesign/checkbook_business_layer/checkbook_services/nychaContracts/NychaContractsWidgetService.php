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
            case "Vendor_link":
                $column = $row['vendor_name'];
                $class = "bottomContainerReload";
                $url = "";

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
        return NychaContractsUrlService::getFooterUrl($parameters,$this->getLegacyNodeId());
    }
}
