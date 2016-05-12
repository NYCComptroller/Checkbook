<?php
/**
 * Created by PhpStorm.
 * User: atorkelson
 * Date: 4/20/16
 * Time: 4:05 PM
 */

class ContractsWidgetService extends AbstractWidgetService {

    public function implDerivedColumn($column_name,$row) {
        $value = null;
        switch($column_name) {
            case "contract_id_link":
                $column = $row['contract_number'];
                $class = "bottomContainerReload";
                $url = ContractsUrlService::contractIdUrl($row['original_agreement_id'],$row['document_code']);
                $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
                break;

            case "agency_name_link":
                $column = $row['agency_name'];
                $url = ContractsUrlService::agencyUrl($row['agency_id'], $row['original_agreement_id']);
                $value = "<a href='{$url}'>{$column}</a>";
                break;
            
            case "agency_landing_link":
                $column = $row['agency_name'];
                $url = ContractsUrlService::agencyUrl($row['agency_id']);
                $value = "<a href='{$url}'>{$column}</a>";
                break;

            case "vendor_name_link":
                $column = $row['vendor_name'];
                $url = ContractsUrlService::vendorUrl($row['vendor_id'], $row['minority_type_id'], $row['is_prime_or_sub']);
                $value = "<a href='{$url}'>{$column}</a>";
                break;

            case "spent_to_date_link":
                $column = $row['spending_amount_sum'];
                $class = "new_window";
                $url = ContractsUrlService::spentToDateUrl($row['original_agreement_id'],$row['vendor_id'],$row['contract_number']);
                $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
                break;
        }

        if(isset($value)) {
            return $value;
        }
        return $value;
    }

}