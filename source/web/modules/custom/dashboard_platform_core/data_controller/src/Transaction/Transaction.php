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

use Drupal\data_controller\Common\NamespaceHelper\NameSpaceHelper;
use Drupal\data_controller\Common\Object\Exception\IllegalArgumentException;
use Drupal\data_controller\Common\Object\Exception\IllegalStateException;

class Transaction {

    private $started = FALSE;

    private $resources = NULL;
    private $actionCallbacks = NULL;

    public function start() {
        $this->checkTransactionNotStarted();

        // process all resource action callbacks and ask them to join the transaction
        if (isset($this->actionCallbacks)) {
            foreach ($this->actionCallbacks as $callback) {
                $callback->start();
            }
        }

        $this->started = TRUE;
    }

    public function assembleResourceName($resourceType, $name) {
        return NameSpaceHelper::addNameSpace($resourceType, $name);
    }

    public function findResource($resourceName) {
        return isset($this->resources[$resourceName]) ? $this->resources[$resourceName] : NULL;
    }

    public function registerResource($resourceName, $resource) {
        if (isset($this->resources[$resourceName])) {
            throw new IllegalArgumentException(t("'@resourceName' resource had been registered already", array('@resourceName' => $resourceName)));
        }

        $this->resources[$resourceName] = $resource;
    }

    public function registerActionCallback(TransActionCallback $callback) {
        if ($callback instanceof ResourceTransActionCallback) {
            if ($this->started) {
                $callback->start();
            }
            else {
                // this action callback can join a transaction in the future when $transaction->start() method is called
            }
        }
        else {
            if ($this->started) {
                // this callback needs to be registred with current transaction
            }
            else {
                return;
            }
        }


        if (!isset($this->actionCallbacks)) {
            $this->actionCallbacks = [];
        }
        // adding the callback to the beginning of the list. When it is time to commit/rollback the callback will be called first
        array_unshift($this->actionCallbacks, $callback);
    }

    public function commit() {
        $this->checkTransactionStarted();

        if (isset($this->actionCallbacks)) {
            foreach ($this->actionCallbacks as $callback) {
                $callback->commit();
            }
            $this->actionCallbacks = NULL;
        }

        $this->started = FALSE;
    }

    public function rollback() {
        // if the transaction had not been started we have nothing to rollback
        if ($this->started) {
            if (isset($this->actionCallbacks)) {
                foreach ($this->actionCallbacks as $callback) {
                    $callback->rollback();
                }
                $this->actionCallbacks = NULL;
            }

            $this->started = FALSE;
        }
    }

    protected function checkTransactionNotStarted() {
        if ($this->started) {
            $this->errorTransactionStarted();
        }
    }

    protected function checkTransactionStarted() {
        if (!$this->started) {
            $this->errorTransactionNotStarted();
        }
    }

    protected function errorTransactionStarted() {
        throw new IllegalStateException(t('Transaction has already been started'));
    }

    protected function errorTransactionNotStarted() {
        throw new IllegalStateException(t('Transaction has not been started'));
    }
}
