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
        $url_param = drupal_get_path_alias($_GET['q']);
        $category_id  = RequestUtil::getRequestKeyValueFromURL('category', $url_param);
        $value = null;
        switch($column_name) {
            case "vendor_link":
                $column = $row['vendor_name'];
                $url = NychaSpendingUrlService::generateLandingPageUrl('vendor',$row['vendor_id']);
                $value = "<a href='{$url}'>{$column}</a>";
                break;
          case "contract_link":
                $contract_id = isset($row['contract_id']) && $row['contract_id'] ? $row['contract_id']: $row['purchase_order_number'];
                $value = NychaSpendingUrlService::generateContractIdLink($contract_id);
                break;
            case "industry_link":
                $value = $row['industry_name'];
                break;
            case "fundsrc_link":
                $value = $row['funding_source_name'];
                break;
            /* YTD Spending links */
            case "dept_ytd_spending_link":
                $column = $row['check_amount_sum'];
                $class = "bottomContainerReload";
                $dynamic_parameter = "/dept_inv/" . $row["department_id"];
                $url = NYCHASpendingUrlService::ytdSpendingUrl($dynamic_parameter, 'ytd_department');
                $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
                break;
            case "exp_cat_ytd_spending_link":
                $column = $row['check_amount_sum'];
                $class = "bottomContainerReload";
                $dynamic_parameter = "/exp_cat_inv/" . $row["expenditure_type_id"];
                $url = NYCHASpendingUrlService::ytdSpendingUrl($dynamic_parameter, 'ytd_expense_category');
                $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
                break;
            case "fundsrc_ytd_spending_link":
                $column = $row['check_amount_sum'];
                $class = "bottomContainerReload";
                $dynamic_parameter = "/fundsrc_inv/" . $row["funding_source_id"];
                $url = NYCHASpendingUrlService::ytdSpendingUrl($dynamic_parameter, 'ytd_funding_source');
                $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
                break;
            case "industry_ytd_spending_link":
                $column = $row['check_amount_sum'];
                $class = "bottomContainerReload";
                $dynamic_parameter = "/industry_inv/" . $row["industry_id"];
                $url = NYCHASpendingUrlService::ytdSpendingUrl($dynamic_parameter, 'ytd_industry');
                $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
                break;
            case "vendor_ytd_spending_link":
                $column = $row['check_amount_sum'];
                $class = "bottomContainerReload";
                $dynamic_parameter = "/vendor_inv/" . $row["vendor_id"];
                $url = NYCHASpendingUrlService::ytdSpendingUrl($dynamic_parameter, 'ytd_vendor');
                $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
                break;
            case "contract_ytd_spending":
                 $column = $row['check_amount_sum'];
                 $class = "bottomContainerReload";
                 $dynamic_parameter = "/po_num/" . $row["contract_id"];
                 $url = NYCHASpendingUrlService::ytdSpendingUrl($dynamic_parameter, 'ytd_contract');
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
        return NychaSpendingUrlService::getFooterUrl($parameters);
    }
}
