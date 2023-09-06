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

namespace Drupal\checkbook_domain\Sql;

use Drupal\checkbook_infrastructure_layer\DataAccess\Factory\SqlModelFactory;
use Drupal\checkbook_infrastructure_layer\DataAccess\SqlUtil;
use Drupal\checkbook_log\LogHelper;
use Exception;

/**
 * Class EntityRepository
 */
class SqlEntityRepository implements ISqlEntityRepository {

    private $statementName;
    private $sqlConfigPath;

    function __construct($sqlConfigPath = null, $statementName = null) {
        $this->sqlConfigPath = $sqlConfigPath;
        $this->statementName = $statementName;
    }

    /**
     * Returns the dataset from executing the query
     * @param $parameters
     * @param $limit
     * @param $orderBy
     * @param $statementName
     * @return array
     * @throws Exception
     */
    public function getByDataset($parameters, $limit, $orderBy, $statementName) {
        $statementName = $statementName ?? $this->statementName;
        $sqlConfigPath = $this->sqlConfigPath;

        try {
            $sqlModel = SqlModelFactory::getSqlStatementModel($parameters, $limit, $orderBy, $sqlConfigPath, $statementName);
            $data = SqlUtil::executeSqlQuery($sqlModel);
            $results = (new SqlDatasetFactory())->create($data);
        }
        catch (Exception $e) {
            LogHelper::log_error("Error in function SqlRepository::getByDataset() \nError getting data from controller: \n" . $e->getMessage());
            throw $e;
        }

        return $results;
    }

    /**
     * Returns total number of records from executing the query
     * @param $parameters
     * @param $statementName
     * @return int
     * @throws Exception
     */
    public function getByDatasetRowCount($parameters, $statementName) {

        $statementName = $statementName ?? $this->statementName;
        $sqlConfigPath = $this->sqlConfigPath;

        try {
            $sqlModel = SqlModelFactory::getSqlStatementModel($parameters, null, null, $sqlConfigPath, $statementName);
            $data = SqlUtil::executeCountSqlQuery($sqlModel);
            $results = (new SqlRecordCountFactory())->create($data);
        }
        catch (Exception $e) {
            LogHelper::log_error("Error in function SqlRepository::getByDatasetRowCount() \nError getting data from controller: \n" . $e->getMessage());
            throw $e;
        }
        return $results;
    }
}
