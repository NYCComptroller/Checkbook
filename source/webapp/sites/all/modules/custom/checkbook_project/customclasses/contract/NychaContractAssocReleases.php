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

        $node->contractBAPA = true;
        $node->page = $page;

        $node->assocReleases = $this->getBapaAssocReleases($contract_id, $page);
        if ($node->assocReleases && sizeof($node->assocReleases)) {
            $this->loadBapaReleaseHistory($node->assocReleases);
            NychaContractDetails::loadShipmentDistributionDetails($node, $contract_id);
            $node->spendingByRelease = $this->getSpendingByRelease($contract_id);
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

    private function getBapaAssocReleases($contract_id, int $page=0)
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
        return _checkbook_project_execute_sql_by_data_source($releases_sql, Datasource::NYCHA);

    }

    private function loadBapaReleaseHistory(&$releases)
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
        $release_history = _checkbook_project_execute_sql_by_data_source($rh_sql, Datasource::NYCHA);

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

    /**** Returns Spending by release data for the given contract ID
     * @param $contract_id
     *******/
    private function getSpendingByRelease($contract_id){
      $sql = <<<SQL
                	SELECT issue_date_year, release_number, issue_date, document_id , check_amount, invoice_net_amount, expenditure_type_description
                  FROM all_disbursement_transactions where contract_id = '{$contract_id}'
                  GROUP BY release_number, issue_date_year, issue_date, document_id , check_amount, invoice_net_amount, expenditure_type_description
                  ORDER BY release_number, issue_date_year, issue_date DESC
            
SQL;
      $results = _checkbook_project_execute_sql_by_data_source($sql, Datasource::NYCHA);
      $releaseSpendingData = [];
      $releases = [];
      $years = [];
      foreach($results as $result){
        $releases[] = $result['release_number'];
        $years[$result['release_number']][] = $result['issue_date_year'];
        $releaseSpendingData[$result['release_number']][$result['issue_date_year']][] = array('issue_date'=>$result['issue_date'], 'document_id'=>$result['document_id'],
          'check_amount' => $result['check_amount'], 'amount_spent'=>$result['invoice_net_amount'], 'expense_category'=>$result['expenditure_type_description']);
      }
      sort($releases);
      $releases = array_unique($releases);
      foreach($releases as $key => $release_number){
          $data = [];
          $yearList = array_unique($years[$release_number]);
          arsort($yearList);
          foreach($yearList as $key=>$year){
            $data[$year] = $releaseSpendingData[$release_number][$year];
          }
          $spendingByRelease[$release_number]['year_list'] = $yearList;
          $spendingByRelease[$release_number]['spending_by_release'] = $data;
      }
      return $spendingByRelease;
    }

}
