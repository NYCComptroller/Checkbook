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

namespace Drupal\data_controller_sql\Datasource\Handler;

use Drupal\data_controller\Common\Pattern\AbstractObject;
use Drupal\data_controller\Datasource\DataSourceQueryFactory;
use Drupal\data_controller\Transaction\ResourceTransActionCallback;
use Drupal\data_controller\Transaction\TransActionCallback;

class ConnectionTransactionActionCallback extends AbstractObject implements TransActionCallback, ResourceTransActionCallback {

    private $datasourceName = NULL;

    public function __construct($datasourceName) {
        parent::__construct();
        $this->datasourceName = $datasourceName;
    }

    protected function getDataSourceHandler() {
        $environment_metamodel = data_controller_get_environment_metamodel();

        $datasource = $environment_metamodel->getDataSource($this->datasourceName);

        return DataSourceQueryFactory::getInstance()->getHandler($datasource->type);
    }

    public function start() {
        $this->getDataSourceHandler()->startTransaction($this->datasourceName);
    }

    public function commit() {
        $this->getDataSourceHandler()->commitTransaction($this->datasourceName);
    }

    public function rollback() {
        $this->getDataSourceHandler()->rollbackTransaction($this->datasourceName);
    }
}
