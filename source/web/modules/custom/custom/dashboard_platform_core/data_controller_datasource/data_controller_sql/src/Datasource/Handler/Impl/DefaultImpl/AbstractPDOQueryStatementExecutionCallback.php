<?php
namespace Drupal\data_controller_sql\Datasource\Handler\Impl\DefaultImpl;

use Drupal\data_controller\MetaModel\MetaData\ColumnMetaData;
use Drupal\data_controller_sql\Datasource\Handler\Impl\AbstractQueryStatementExecutionCallback;
use PDO;

abstract class AbstractPDOQueryStatementExecutionCallback extends AbstractQueryStatementExecutionCallback {

    public function fetchNextRecord($connection, $statement) {
        return $statement->fetch(PDO::FETCH_NUM);
    }

    public function getColumnCount($connection, $statement) {
        return $statement->columnCount();
    }

    /**
     * @param $statement
     * @param $columnIndex
     * @return ColumnMetaData
     */
    public function getColumnMetaData($connection, $statement, $columnIndex) {
        $statementColumnMetaData = $statement->getColumnMeta($columnIndex);
        if ($statementColumnMetaData === FALSE) {
            return FALSE;
        }

        $column = new ColumnMetaData();
        $column->name = $statementColumnMetaData['name'];
        $column->type->databaseType = $statementColumnMetaData['native_type'];

        // TODO add support for $column->type->length, ...->precision, ...->scale

        return $column;
    }
}
