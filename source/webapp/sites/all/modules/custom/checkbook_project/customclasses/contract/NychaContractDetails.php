<?php
/**
 * This file is part of the Checkbook NYC financial transparency software.
 *
 * Copyright (C) 2012, 2013 New York City
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once(realpath(drupal_get_path('module', 'checkbook_project')) . '/customclasses/contract/ContractUtil.php');

/**
 * Class NychaContractDetails
 */
class NychaContractDetails
{

    /**
     * @param $node
     */
    public function getData(&$node)
    {

        $contract_id = RequestUtilities::getRequestParamValue('contract');

        if (!ctype_alnum($contract_id)) {
            return;
        }

        $node->contractPO = $node->contractBAPA = false;

        if (stripos(' ' . $contract_id, 'ba') || stripos(' ' . $contract_id, 'pa')) {
            $node->contractBAPA = true;
            $this->loadBlankedOrPlannedAgreement($node, $contract_id);
            $this->calcBapaAssocReleases($node, $contract_id);
        }

        if (stripos(' ' . $contract_id, 'po')) {
            $node->contractPO = true;
            $this->loadPurchaseOrder($node, $contract_id);
            NychaContractDetails::loadShipmentDistributionDetails($node, $contract_id);
        }

        $node->contract_history_by_years = $this->getContractHistory($node->data['contract_id'] ?? '');
        $this->calcNumberOfContracts($node);
    }

    /**
     * @param $node
     * @param $contract_id
     */
    private function loadPurchaseOrder(&$node, $contract_id)
    {
        $po_query = <<<SQL
            SELECT DISTINCT
                contract_id,
                agreement_type_code,
                agreement_type_name,
                transaction_category_name,
                transaction_status_name,
                purchase_order_number as id,
                release_approved_date,
                release_approved_year,
                release_approved_year_id,
                release_total_amount current_amount,
                release_original_amount original_amount,
                release_spend_to_date spend_to_date,
                release_line_amount_difference,
                release_revision_count,
                purpose,
                agency_name,
                contract_type_descr,
                vendor_id,
                vendor_number,
                vendor_name,
                address_id,
                address_line1,
                address_line2,
                city,
                state,
                zip,
                industry_type_name,
                department_name,
                award_method_name,
                latest_flag
            FROM
                all_agreement_transactions
            WHERE contract_id='{$contract_id}' AND agreement_type_id = 3
            ORDER BY release_approved_date DESC
SQL;

        $contracts = _checkbook_project_execute_sql_by_data_source($po_query, 'checkbook_nycha');
        if ($contracts && sizeof($contracts)) {
            $node->data = $contracts[0];
        }
    }

    /**
     * @param $node
     * @param $contract_id
     */
    private function loadBlankedOrPlannedAgreement(&$node, $contract_id)
    {
        $bapa_query = <<<SQL
            SELECT
                agreement_id,
                contract_id,
                agreement_type_id,
                agreement_type_code,
                agreement_type_name,
                transaction_category_id,
                transaction_category_name,
                transaction_status_id,
                transaction_status_name,
                purchase_order_number as id,
                start_date,
                start_year,
                start_year_id,
                end_date,
                end_year,
                end_year_id,
                approved_date,
                approved_year,
                approved_year_id,
                cancel_date,
                reject_date,
                total_amount current_amount,
                original_amount,
                spend_to_date,
                amount_difference,
                percent_difference,
                award_size_id,
                award_size_name,
                revision_count,
                purpose,
                agency_id,
                agency_code,
                agency_name,
                contract_type_id,
                contract_type_code,
                contract_type_descr,
                vendor_id,
                vendor_number,
                vendor_name,
                address_id,
                address_line1,
                address_line2,
                city,
                state,
                zip,
                location_id,
                location_code,
                location_description,
                borough_id,
                borough_code,
                borough_name,
                industry_type_id,
                industry_type_code,
                industry_type_name,
                department_id,
                department_code,
                department_name,
                award_method_id,
                award_method_code,
                award_method_name,
                contracting_agency,
                budget_approval_fiscal_year,
                commodity_category_id,
                commodity_category_code,
                commodity_category_descr category_descr,
                po_header_id,
                number_of_solicitations,
                response_to_solicitation,
                latest_flag
            FROM
              all_agreements
            WHERE
              (purchase_order_number = '{$contract_id}' OR contract_id='{$contract_id}')
            ORDER BY agreement_id DESC
            LIMIT 1;
SQL;

        $contracts = _checkbook_project_execute_sql_by_data_source($bapa_query, 'checkbook_nycha');
        if ($contracts && sizeof($contracts)) {
            $node->data = $contracts[0];
        }

        $agreement_transactions_query =
            "SELECT
              contract_id, count(DISTINCT release_number) as associated_releases
            from all_agreement_transactions
            where (contract_id = '{$contract_id}' )
            group by contract_id";

        $assoc_releases_data = _checkbook_project_execute_sql_by_data_source($agreement_transactions_query, 'checkbook_nycha');
        $total_associated_releases = 0;
        foreach ($assoc_releases_data as $row) {
            $total_associated_releases += $row["associated_releases"];
        }
        $node->total_associated_releases = $total_associated_releases;
    }

    /**
     * @param $node
     */
    private function calcNumberOfContracts(&$node)
    {
        $vendor_id = $node->data['vendor_id'];
        $node->total_number_of_contracts = 0;
        if ($vendor_id) {
            $total_number_contracts_query = <<<EOQ2
            SELECT SUM(count)
            FROM (SELECT COUNT(DISTINCT contract_id) 
                  FROM all_agreements
                  WHERE vendor_id = '{$vendor_id}'
                  UNION ALL
                  SELECT COUNT(DISTINCT contract_id)
                  FROM all_agreement_transactions
                  WHERE agreement_type_code = 'PO' AND vendor_id = '{$vendor_id}') a
EOQ2;

            $total_number_of_contracts = _checkbook_project_execute_sql_by_data_source($total_number_contracts_query, 'checkbook_nycha');
            if ($total_number_of_contracts) {
                $node->total_number_of_contracts = $total_number_of_contracts[0];
            }
        }
    }

    /**
     * @param $history
     * @return array
     */
    private function splitHistoryByYears($history)
    {
        $return = [];
        if (!$history or !is_array($history) or !sizeof($history)) {
            return [];
        }
        foreach ($history as $line) {
            list($year,) = explode('-', $line['revision_approved_date']);
            if ($year < 1900 || $year > (date('Y') + 10)) {
                continue;
            }
            $return[$year][$line['revision_number']] = $line;
        }
        return $return;
    }

    /**
     * @param $node
     * @param $contract_id
     * @return array|bool|mixed
     */
    private function calcBapaAssocReleases(&$node, $contract_id)
    {
        $releases_sql = <<<SQL
            SELECT COUNT(DISTINCT release_id)
            FROM all_agreement_transactions
            WHERE contract_id = '{$contract_id}'
SQL;
        $total = _checkbook_project_execute_sql_by_data_source($releases_sql, 'checkbook_nycha');
        $node->assoc_releases_count = $total[0]['count'];
        $node->assoc_releases_pages = ceil($total[0]['count']/10.0);
        return;
    }


    /**
     * @param string $contract_id
     * @return array|void
     */
    private function getContractHistory(string $contract_id)
    {
        if (!$contract_id || !preg_match('/[A-Z]{2}[0-9]*/', $contract_id)) {
            return;
        }

        $sql = <<<SQL
            SELECT
                distinct contract_id,
                hgr.revision_number,
                agreement_start_date,
                agreement_end_date,
                hgr.revised_total_amount current_amount,
                hgr.revision_approved_date,
                agreement_original_amount
            FROM
              history_agreement_all_revisions hgr
              join all_agreement_transactions agt on hgr.purchase_order_number = agt.purchase_order_number
            WHERE
              hgr.release_number is null
              AND revision_number <> 0
              AND contract_id='{$contract_id}'
            ORDER BY revision_number DESC;
SQL;

        $history = _checkbook_project_execute_sql_by_data_source($sql, 'checkbook_nycha');
        return $this->splitHistoryByYears($history);

    }


    public static function loadShipmentDistributionDetails(&$node, $contract_id)
    {
        $sd_sql = <<<SQL
            SELECT DISTINCT 
                release_id,
                shipment_number,
                line_number,
                distribution_number,
                release_line_total_amount,
                release_line_original_amount,
                release_line_spend_to_date,
                responsibility_center_descr,
                transaction_status_name
            FROM all_agreement_transactions a
            WHERE contract_id = '{$contract_id}'
            ORDER BY shipment_number, distribution_number, line_number;
SQL;

        $shipments = _checkbook_project_execute_sql_by_data_source($sd_sql, 'checkbook_nycha');
        if ($node->contractPO) {
            $node->shipments = $shipments;
            return;
        }

        $return = [];
        foreach ($shipments as $shipment) {
            if (!isset($return[$shipment['release_id']])) {
                $return[$shipment['release_id']] = [];
            }
            $return[$shipment['release_id']][] = $shipment;
        }

        foreach ($node->assocReleases as &$release) {
            $release['shipments'] = $return[$release['release_id']];
        }
    }
}
