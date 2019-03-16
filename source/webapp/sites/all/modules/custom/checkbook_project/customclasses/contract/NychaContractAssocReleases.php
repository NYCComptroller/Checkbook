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
 * Class NychaContractAssocReleases
 */
class NychaContractAssocReleases
{

    /**
     * @param $node
     */
    public function getData(&$node)
    {
        $contract_id = RequestUtilities::get('contract');
        $page = RequestUtilities::get('page') ?? 0;

        if (!ctype_alnum($contract_id)) {
            return;
        }

        $node->contractPO = $node->contractBAPA = false;
        $node->page = $page;

        if (stripos(' ' . $contract_id, 'ba') || stripos(' ' . $contract_id, 'pa')) {
//            $this->loadBaPa($node, $contract_id);
            $node->contractBAPA = true;
        }

        if (stripos(' ' . $contract_id, 'po')) {
//            $this->loadPurchaseOrder($node, $contract_id);
            $node->contractPO = true;
        }

//        $node->contract_history_by_years = $this->getAgreementHistory($node->data['contract_id'] ?? '');
//        $this->calcNumberOfContracts($node);

        $node->assocReleases = $this->getAssocReleases($contract_id, $page);
        if ($node->assocReleases && sizeof($node->assocReleases)) {
            $this->loadReleaseHistory($node->assocReleases);
            $this->loadShipmentDistributionDetails($node, $contract_id);
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

    private function getAssocReleases($contract_id, int $page=0)
    {
        $page*=10;
        $releases_sql = <<<SQL
            SELECT DISTINCT release_id,
                            release_number,
                            vendor_id,
                            vendor_name,
                            release_total_amount,
                            release_approved_date,
                            release_original_amount,
                            release_spend_to_date
            FROM all_agreement_transactions
            WHERE contract_id = '{$contract_id}'
            ORDER BY release_number
            LIMIT 10 OFFSET {$page}
SQL;
        return _checkbook_project_execute_sql_by_data_source($releases_sql, 'checkbook_nycha');

    }

    private function loadReleaseHistory(&$releases)
    {
        $release_ids = array_column($releases, 'release_id');
        $release_ids = join("','", $release_ids);

        $rh_sql = <<<SQL
            SELECT DISTINCT
                release_id,
                hgr.revision_number,
                release_year,
                release_year_id,
                release_approved_date,
                hgr.revised_total_amount,
                release_original_amount,
                revision_approved_date,
                transaction_status_name
            FROM
                history_agreement_all_revisions hgr
                left join all_agreement_transactions agt on hgr.purchase_order_number = agt.purchase_order_number
                and hgr.release_number = agt.release_number
            WHERE
                release_id IN('{$release_ids}')
                and hgr.release_number is not null
                and revision_number <> 0
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
                line_number,
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
