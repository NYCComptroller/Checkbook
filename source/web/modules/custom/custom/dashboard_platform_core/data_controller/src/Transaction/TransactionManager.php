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

namespace Drupal\data_controller\Transaction;

use Drupal\data_controller\Common\Pattern\Singleton\AbstractSingleton;

class TransactionManager extends AbstractSingleton {

    private static $instance = NULL;

    private $transactions = NULL;

    public static function getInstance() {
        if (!isset(self::$instance)) {
            self::$instance = new TransactionManager();
        }

        return self::$instance;
    }

    protected function initiateTransaction($datasourceName) {
        $transaction = new Transaction();
        $this->transactions[$datasourceName] = $transaction;

        return $transaction;
    }

    protected function findTransaction($datasourceName) {
        return $this->transactions[$datasourceName] ?? NULL;
    }

    public function startTransaction($datasourceName) {
        $transaction = $this->findTransaction($datasourceName);
        if (isset($transaction)) {
        }
        else {
            $transaction = $this->initiateTransaction($datasourceName);
        }
        $transaction->start();

        return $transaction;
    }

    public function getTransaction($datasourceName) {
        $transaction = $this->findTransaction($datasourceName);

        return $transaction ?? $this->initiateTransaction($datasourceName);
    }
}
