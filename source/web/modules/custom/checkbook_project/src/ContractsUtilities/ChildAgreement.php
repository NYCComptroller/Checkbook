<?php
/**
 * This file is part of the Checkbook NYC financial transparency software.
 *
 * Copyright (c) 2012 – 2023 New York City
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Drupal\checkbook_project\ContractsUtilities;

class ChildAgreement extends AbstractContract{
        public function initializeAmounts() {
            $agreement_id = $this->getAgreementId();
            $query = "SELECT";

            switch($this->getDataSource()) {
                case "checkbook_oge":
                    $query .=
                        " sum(original_amount) original_amount
                        , sum(current_amount) current_amount
                        , sum(check_amount) as spent_amount
                        FROM {oge_contract_vendor_level} a
                        JOIN
                        (
                            SELECT DISTINCT contract_number
                            FROM {history_agreement}
                            WHERE agreement_id = " . $agreement_id . "
                        ) b ON a.fms_contract_number = b.contract_number
                        LEFT JOIN
                        (
                        SELECT sum(check_amount) as check_amount
                        , contract_number
                        , vendor_id
                        FROM {disbursement_line_item_details}
                        GROUP BY 2,3
                        ) c
                        ON b.contract_number = c.contract_number AND a.vendor_id = c.vendor_id limit 1";
                    break;

                default:
                    $query .=
                        " l1.contract_number
                        , l1.maximum_contract_amount as current_amount
                        , l1.original_contract_amount as original_amount
                        , l1.rfed_amount as spent_amount
                        FROM history_agreement AS l1
                        WHERE l1.original_agreement_id = " . $agreement_id . " AND l1.latest_flag = 'Y'";
                    break;
            }

            $results = _checkbook_project_execute_sql_by_data_source($query,$this->getDataSource());

            foreach ($results as $row) {
                $this->setOriginalAmount($row['original_amount']);
                $this->setCurrentAmount($row['current_amount']);
                $this->setSpentAmount($row['spent_amount']);
            }
        }

    }
