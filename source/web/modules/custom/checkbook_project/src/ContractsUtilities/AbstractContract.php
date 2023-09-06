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

Abstract class AbstractContract{

        private $dataSource;
        private $agreementId;
        private $originalAmount;
        private $currentAmount;
        private $spentAmount;

      /**
       * AbstractContract constructor.
       * @param $data_source
       * @param $agreement_id
       */
        public function __construct($data_source, $agreement_id) {
            $this->dataSource = $data_source;
            $this->agreementId = $agreement_id;

            $this->initializeAmounts();
        }

        public function getOriginalAmount() { return $this->originalAmount; }
        public function setOriginalAmount($originalAmount) { $this->originalAmount = $originalAmount; }

        public function getCurrentAmount() { return $this->currentAmount; }
        public function setCurrentAmount($currentAmount) { $this->currentAmount = $currentAmount; }

        public function getSpentAmount() { return $this->spentAmount; }
        public function setSpentAmount($spentAmount) { $this->spentAmount = $spentAmount; }

        public function getDataSource() { return $this->dataSource; }
        public function setDataSource($dataSource) { $this->dataSource = $dataSource; }

        public function getAgreementId() { return $this->agreementId; }
        public function setAgreementId($agreementId) { $this->agreementId = $agreementId; }
    }
