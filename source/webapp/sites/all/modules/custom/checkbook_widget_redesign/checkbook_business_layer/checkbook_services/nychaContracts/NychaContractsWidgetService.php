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
                $url = NychaContractsUrlService::generateLandingPageUrl('vendor',$row['vendor_id']);
                $value = "<a href='{$url}'>{$column}</a>";
                break;
            case "size_link":
                $column = $row['award_size_name'];
                $url = NychaContractsUrlService::generateLandingPageUrl('size',$row['award_size_id']);
                $value = "<a href='{$url}'>{$column}</a>";
                break;
            case "industry_link":
                $column = $row['industry_type_name'];
                $url = NychaContractsUrlService::generateLandingPageUrl('industry',$row['industry_type_id']);
                $value = "<a href='{$url}'>{$column}</a>";
                break;
            case "award_method_link":
                $column = $row['award_method_name'];
                $url = NychaContractsUrlService::generateLandingPageUrl('awdmethod',$row['award_method_id']);
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
