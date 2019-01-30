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
            case "vendor_link":
                $column = $row['vendor_name'];
                $url = NychaContractsUrlService::vendorUrl($row['vendor_id']);
                $value = "<a href='{$url}'>{$column}</a>";
                break;
            case "contract_id_link":
                $contract_id = isset($row['contract_id']) && $row['contract_id'] ? $row['contract_id']: $row['purchase_order_number'];
                $url = NychaContractsUrlService::contractDetailsUrl($contract_id);
                $value = "<a href='{$url}'>{$contract_id}</a>";
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
        return NychaContractsUrlService::getFooterUrl($parameters);
    }
}
