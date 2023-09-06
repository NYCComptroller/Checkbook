<?php
namespace Drupal\data_controller_sql\Datasource\Handler\Impl\DefaultImpl;

use Drupal\data_controller\Controller\CallContext\DataControllerCallContext;
use Drupal\data_controller\Datasource\DataSourceHandler;
use Drupal\data_controller_sql\Datasource\Handler\__SQLDataSourceHandler__AbstractQueryCallbackProxy;
use Drupal\data_controller_sql\Datasource\Handler\Impl\AbstractExecuteQueryStatementImpl;
use Exception;


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


class PDOExecuteQueryStatementImpl extends AbstractExecuteQueryStatementImpl {

    public function execute(
            DataSourceHandler $handler,
            DataControllerCallContext $callcontext,
            $connection, $sql,
            __SQLDataSourceHandler__AbstractQueryCallbackProxy $callbackInstance) {

        $statement = $connection->query($sql);
        try {
            $result = $callbackInstance->callback($callcontext, $connection, $statement);
        }
        catch (Exception $e) {
            $statement->closeCursor();
            throw $e;
        }
        $statement->closeCursor();

        return $result;
    }
}
