<?php

class NychaSpendingWidgetService extends WidgetDataService implements IWidgetService {
    /**
     * Function to allow the client to initialize the data service
     * @return mixed
     */
    public function initializeDataService() {
        return new NychaSpendingDataService();
    }

    static $widget_titles = array('checks' => 'Checks', 'vendors' => 'Vendors', 'contracts' => 'Contracts',
                                  'expense_categories' => 'Expense Categories', 'industries' => 'Industries',
                                  'funding_source' => 'Funding Sources', 'departments' => 'Departments');

    static public function getTransactionsTitle(){
      $categories = array(3 => 'Contract', 2 => 'Payroll', 1 => 'Section 8', 4 => 'Others', null => 'Total');
      $widget = RequestUtilities::get('widget');
      $widget_titles = self::$widget_titles;
      $title = $widget_titles[$widget].' '.$categories[ RequestUtilities::get('category')]. " Spending Transactions";
      return $title ;
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
        return NychaSpendingUrlService::getFooterUrl($parameters);
    }
}
