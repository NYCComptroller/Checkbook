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
            $this->loadBaPa($node, $contract_id);
            $node->contractBAPA = true;
        }

        if (stripos(' ' . $contract_id, 'po')) {
            $this->loadPurchaceOrder($node, $contract_id);
            $node->contractPO = true;
        }

        $node->contract_history_by_years = $this->splitHistoryByYears($node->contract_history);
        $this->calcNumberOfContracts($node);

        $node->assocReleases = $this->getAssocReleases($contract_id);
        if ($node->assocReleases && sizeof($node->assocReleases)) {
            $this->loadReleaseHistory($node->assocReleases, $contract_id);
            $this->loadShipmentDistributionDetails($node, $contract_id);
        }
    }

    private function loadPurchaceOrder(&$node, $contract_id)
    {
        $po_query = <<<SQL
            SELECT DISTINCT
                contract_id,
                agreement_type_code,
                agreement_type_name,
                release_total_amount total_amount,
                release_original_amount original_amount,
                release_spend_to_date spend_to_date,
                transaction_category_name,
                transaction_status_name,
                purchase_order_number,
                release_approved_date,
                release_approved_year,
                release_approved_year_id,
                release_line_total_amount,
                release_line_original_amount,
                release_line_spend_to_date,
                release_line_amount_difference,
                release_revision_count,
                'fixme' revision_number,
                'fixme' revision_total_amount,
                'fixme' revision_approved_date,
                shipment_number,
                distribution_number,
                purpose,
                agency_name,
                contract_type_descr,
                vendor_id,
                vendor_number,
                vendor_name,
                address_line1,
                address_line2,
                city,
                state,
                zip,
                industry_type_name,
                department_name,
                award_method_name,
                commodity_category_descr category_descr,
                latest_flag
            FROM
                all_agreement_transactions
            WHERE contract_id='{$contract_id}'
            ORDER BY revision_approved_date DESC
SQL;

        $contracts = _checkbook_project_execute_sql_by_data_source($po_query, 'checkbook_nycha');
        if ($contracts && sizeof($contracts)) {
            $node->data = $contracts[0];
        }

        $node->contract_history = $contracts;

    }

    private function loadBaPa(&$node, $contract_id)
    {
        $bapa_query = <<<SQL
            SELECT
              contract_id,
              agency_name,
              start_date,
              end_date,
              approved_date,
              approved_year,
              total_amount,
              original_amount,
              spend_to_date,
              amount_difference,
              award_size_id,
              award_size_name,
              revision_count,
              purpose,
              'fixme' revision_number,
              'fixme' revision_total_amount,
              'fixme' revision_approved_date,
              agency_id,
              agency_code,
              agency_name,
              contract_type_id,
              contract_type_code,
              contract_type_descr,
              vendor_id,
              vendor_number,
              vendor_name,
              address_line1,
              address_line2,
              city,
              STATE,
              zip,
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
              commodity_category_id,
              commodity_category_code,
              commodity_category_descr category_descr,
              number_of_solicitations,
              response_to_solicitation
            FROM
              all_agreements
            WHERE
              (purchase_order_number = '{$contract_id}' OR contract_id='{$contract_id}')
            ORDER BY revision_number DESC
SQL;

        $contracts = _checkbook_project_execute_sql_by_data_source($bapa_query, 'checkbook_nycha');
        if ($contracts && sizeof($contracts)) {
            $node->data = $contracts[0];
        }

        $node->contract_history = $contracts;

        $agreement_transactions_query =
            "SELECT
            contract_id, count(DISTINCT release_number) as associated_releases from all_agreement_transactions  where (contract_id = '{$contract_id}' ) group by contract_id";

        $assoc_releases_data = _checkbook_project_execute_sql_by_data_source($agreement_transactions_query, 'checkbook_nycha');
        $total_associated_releases = 0;
        foreach ($assoc_releases_data as $row) {
            $total_associated_releases += $row["associated_releases"];
        }
        $node->total_associated_releases = $total_associated_releases;
    }

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

    private function getAssocReleases($contract_id)
    {
        $releases_sql = <<<SQL
            SELECT DISTINCT release_id,
                            release_number,
                            vendor_id,
                            vendor_name,
                            release_total_amount,
                            release_original_amount,
                            release_spend_to_date
            FROM all_agreement_transactions
            WHERE contract_id = '{$contract_id}'
            ORDER BY release_number
            LIMIT 50
SQL;
        return _checkbook_project_execute_sql_by_data_source($releases_sql, 'checkbook_nycha');

    }

    private function loadReleaseHistory(&$releases, $contract_id)
    {
        $rh_sql = <<<SQL
            SELECT DISTINCT 
                release_id,
                'fixme' revision_number,
                release_year,
                release_year_id,
                release_approved_date,
                'fixme' revision_total_amount,
                release_original_amount,
                'fixme' revision_approved_date,
                transaction_status_name
            FROM all_agreement_transactions a
            WHERE contract_id = '{$contract_id}'
            ORDER BY revision_number DESC;
SQL;
        $release_history = _checkbook_project_execute_sql_by_data_source($rh_sql, 'checkbook_nycha');

        if (!$release_history) {
            return;
        }
        $history = [];
        foreach ($release_history as $row) {
            if (!isset($history[$row['release_id']])) {
                $history[$row['release_id']] = [];
            }
            $history[$row['release_id']][] = $row;
        }

        foreach ($releases as &$release) {
            $release['history'] = $this->splitHistoryByYears($history[$release['release_id']]);
        }
    }

    private function loadShipmentDistributionDetails(&$node, $contract_id)
    {
        $sd_sql = <<<SQL
            SELECT DISTINCT 
                release_id,
                shipment_number,
                distribution_number,
                release_line_total_amount,
                release_line_original_amount,
                release_line_spend_to_date,
                responsibility_center_descr,
                transaction_status_name
            FROM all_agreement_transactions a
            WHERE contract_id = '{$contract_id}'
            ORDER BY shipment_number, distribution_number;
SQL;

        $shipments = _checkbook_project_execute_sql_by_data_source($sd_sql, 'checkbook_nycha');
        if ($node->contractPO) {
            $node->shipments = $shipments;
            return;
        }

        $return = [];
        foreach($shipments as $shipment) {
            if(!isset($return[$shipment['release_id']])){
                $return[$shipment['release_id']] = [];
            }
            $return[$shipment['release_id']][] = $shipment;
        }

        foreach($node->assocReleases as &$release) {
            $release['shipments'] = $return[$release['release_id']];
        }
    }
}
