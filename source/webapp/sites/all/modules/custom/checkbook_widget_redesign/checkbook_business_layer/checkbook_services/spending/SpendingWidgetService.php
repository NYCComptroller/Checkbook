<?php

class SpendingWidgetService extends WidgetDataService implements IWidgetService
{

    /**
     * Function to allow the client to initialize the data service
     * @return mixed
     */
    public function initializeDataService()
    {
        return new SpendingDataService();
    }

    public function implementDerivedColumn($column_name, $row)
    {
        $value = null;
        switch ($column_name) {

            case "contract_number_link":
                // using 'original_agreement_id' if it's a sub contract
                $agreement_id = isset($row['original_agreement_id']) ? $row['original_agreement_id'] : $row['agreement_id'];
                $column = $row['document_id'];
                $class = "new_window";

                $url = SpendingUrlService::contractIdUrl($agreement_id, $row['document_code']);
                $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
                break;

            case "contract_purpose_formatted":
                $column = $row['contract_purpose'];
                $value = (strlen($column) > 0) ? _get_tooltip_markup($column, 40) : '';
                break;

            /* Name Links */
            case "agency_name_link":
                $column = $row['agency_name'];
                $url = SpendingUrlService::agencyUrl($row['agency_id']);
                $value = "<a href='{$url}'>{$column}</a>";
                break;

            case "payroll_agency_name_link":
                $column = $row['agency_name'];
                $url = SpendingUrlService::payrollAgencyUrl($row['agency_id']);
                $value = "<a href='{$url}'>{$column}</a>";
                break;

            case "industry_name_link":
                $column = $row['industry_type_name'];
                $url = SpendingUrlService::industryUrl($row['industry_type_id']);
                $value = "<a href='{$url}'>{$column}</a>";
                break;

            /* Prime/Sub Vendor Link */
            case "prime_vendor_link":
                $column = $row['prime_vendor_name'];
                $url = SpendingUrlService::primeVendorUrl($row['prime_vendor_id']);
                $value = isset($url) ? "<a href='{$url}'>{$column}</a>" : $column;
                break;

            case "sub_vendor_link":
                $column = $row['sub_vendor_name'];
                $url = SpendingUrlService::subVendorUrl($row['sub_vendor_id']);
                $value = "<a href='{$url}'>{$column}</a>";
                break;

            /* M/WBE Category Links */
            case "prime_mwbe_category_link":
                $minority_type_id = $row['prime_minority_type_id'];
                $mwbe_category_name = MinorityTypeService::getMinorityCategoryById($minority_type_id);
                $is_pop_up = RequestUtilities::isNewWindow();

                // We do not add links to popup windows
                if (!$is_pop_up) {
                    $url = SpendingUrlService::PrimeMwbeCategoryUrl($minority_type_id);
                }
                $value = isset($url)
                    ? "<a href='{$url}'>{$mwbe_category_name}</a>"
                    : $mwbe_category_name;
                break;

            case "sub_mwbe_category_link":
                $minority_type_id = $row['sub_minority_type_id'];
                $mwbe_category_name = MinorityTypeService::getMinorityCategoryById($minority_type_id);
                $is_pop_up = RequestUtilities::isNewWindow();

                // We do not add links to popup windows
                if (!$is_pop_up) {
                    $url = SpendingUrlService::SubMwbeCategoryUrl($minority_type_id);
                }
                $value = isset($url)
                    ? "<a href='{$url}'>{$mwbe_category_name}</a>"
                    : $mwbe_category_name;
                break;

            /* YTD Spending links */
            case "agency_ytd_spending_link":
                $column = $row['check_amount_sum'];
                $class = "bottomContainerReload";
                $dynamic_parameter = "/agency/" . $row["agency_id"];
                $url = SpendingUrlService::ytdSpendingUrl($dynamic_parameter, $this->getLegacyNodeId());
                $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
                break;

            case "expense_cat_ytd_spending_link":
                $column = $row['check_amount_sum'];
                $class = "bottomContainerReload";
                if($row["expenditure_object_id"] == 4) {
                    $dynamic_parameter = "/expcategorynm/" . strtoupper($row["expenditure_object_name"]);
                }
                else {
                    $dynamic_parameter = "/expcategorycode/" . $row["expenditure_object_code"];
                }
                $url = SpendingUrlService::ytdSpendingUrl($dynamic_parameter, $this->getLegacyNodeId());
                $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
                break;

            case "department_ytd_spending_link":
                $column = $row['check_amount_sum'];
                $class = "bottomContainerReload";
                $dynamic_parameter = "/dept/" . $row["department_id"];
                $url = SpendingUrlService::ytdSpendingUrl($dynamic_parameter, $this->getLegacyNodeId());
                $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
                break;

            case "prime_vendor_ytd_spending_link":
                $column = $row['check_amount_sum'];
                $class = "bottomContainerReload";
                $dynamic_parameter = "/vendor/" . $row["prime_vendor_id"];
                $dynamic_parameter .= "/fvendor/" . $row["prime_vendor_id"];
                $url = SpendingUrlService::ytdSpendingUrl($dynamic_parameter, $this->getLegacyNodeId());
                $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
                break;

            case "sub_vendor_ytd_spending_link":
                $column = isset($row['check_amount_sum']) ? $row['check_amount_sum'] : $row['ytd_spending_sub_vendors'];
                $class = "bottomContainerReload";
                $dynamic_parameter = "/subvendor/" . $row["sub_vendor_id"];
                $dynamic_parameter .= "/fvendor/" . $row["sub_vendor_id"];
                $dynamic_parameter .= isset($row["prime_vendor_id"]) ? "/vendor/" . $row["prime_vendor_id"] : "";
                $url = SpendingUrlService::ytdSpendingUrl($dynamic_parameter, $this->getLegacyNodeId());
                $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
                break;

            case "payroll_agency_ytd_spending_link":
                $column = $row['check_amount_sum'];
                $class = "bottomContainerReload";
                $dynamic_parameter = "/agency/" . $row["agency_id"] . "/category/2";
                $url = SpendingUrlService::ytdSpendingUrl($dynamic_parameter, $this->getLegacyNodeId());
                $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
                break;

            case "industry_ytd_spending_link":
                $column = $row['check_amount_sum'];
                $dynamic_parameter = "/industry/" . $row["industry_type_id"];
                $url = SpendingUrlService::ytdSpendingUrl($dynamic_parameter, $this->getLegacyNodeId());
                $class = "bottomContainerReload";
                $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
                break;

            case "contracts_ytd_spending_link":
            case "sub_contracts_ytd_spending_link":
                $column = $row['check_amount_sum'];
                $class = "bottomContainerReload";
                $dynamic_parameter = DocumentCode::isMasterAgreement($row['document_code'])
                    ? '/magid/' . $row['agreement_id'] . '/doctype/' . $row['document_code']
                    : '/agid/' . $row['agreement_id'] . '/doctype/' . $row['document_code'];
                $url = SpendingUrlService::ytdSpendingUrl($dynamic_parameter, $this->getLegacyNodeId());
                $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
                break;

        }

        if (isset($value)) {
            return $value;
        }
        return $value;
    }

    public function getWidgetFooterUrl($parameters)
    {
        return SpendingUrlService::getFooterUrl($parameters, $this->getLegacyNodeId());
    }

    public function adjustParameters($parameters, $urlPath)
    {

        $category = SpendingCategory::getCurrent();
        if ($category == SpendingCategory::TOTAL) {
            $parameters['is_all_categories'] = 'Y';
        }
        return $parameters;
    }
}