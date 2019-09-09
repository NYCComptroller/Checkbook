<?php

class NychaSpendingWidgetService extends WidgetDataService implements IWidgetService {
    /**
     * Function to allow the client to initialize the data service
     * @return mixed
     */
    public function initializeDataService() {
        return new NychaSpendingDataService();
    }

    public function implementDerivedColumn($column_name,$row) {
        $value = null;
        switch($column_name) {
            case "vendor_link":
                $column = $row['vendor_name'];
                $url = NychaSpendingUrlService::generateLandingPageUrl('vendor',$row['vendor_id']);
                $value = "<a href='{$url}'>{$column}</a>";
                break;
            case "industry_link":
                $column = $row['industry_name'];
                $url = NychaSpendingUrlService::generateLandingPageUrl('industry',$row['industry_id']);
                $value = "<a href='{$url}'>{$column}</a>";
                break;
            case "fundsrc_link":
                $column = $row['funding_source_name'];
                $url = NychaSpendingUrlService::generateLandingPageUrl('fundsrc',$row['funding_source_id']);
                $value = "<a href='{$url}'>{$column}</a>";log_error($value);
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
        return NychaSpendingUrlService::getFooterUrl($parameters);
    }
}
