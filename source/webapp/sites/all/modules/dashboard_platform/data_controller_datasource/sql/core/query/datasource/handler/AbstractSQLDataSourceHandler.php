<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


abstract class AbstractSQLDataSourceHandler extends AbstractDataSourceHandler implements SQLDataSourceHandler {

    const STATEMENT_EXECUTION_MODE__PROCEED = 'proceed';
    // this mode is used then it is necessary to generate but not execute the generated statement
    const STATEMENT_EXECUTION_MODE__IGNORE = 'ignore';

    public static $STATEMENT_EXECUTION_MODE = self::STATEMENT_EXECUTION_MODE__PROCEED;

    public function getDataSourceOwner($datasourceName) {
        $environment_metamodel = data_controller_get_environment_metamodel();

        $datasource = $environment_metamodel->getDataSource($datasourceName);

        return $this->getExtension('getDataSourceOwner')->prepare($this, $datasource);
    }

    public function formatStringValue($value) {
        // replacing ' with '' and \ with \\ then surround the value with quotes
        return '\'' . str_replace(array('\'', '\\'), array('\'\'', '\\\\'), $value) . '\'';
    }

    public function formatOperatorValue(DataControllerCallContext $callcontext, AbstractRequest $request, $datasetName, $columnName, OperatorHandler $value) {
        $handler = SQLOperatorFactory::getInstance()->getHandler($this, $value);
        return $handler->format($callcontext, $request, $datasetName, $columnName);
    }

    public function startTransaction($datasourceName) {
        $environment_metamodel = data_controller_get_environment_metamodel();

        $datasource = $environment_metamodel->getDataSource($datasourceName);

        $sql = $this->getExtension('startTransaction')->generate($this, $datasource);
        LogHelper::log_info(new StatementLogMessage('transaction.begin', $sql));
        $this->executeStatement($datasource, $sql);
    }

    public function commitTransaction($datasourceName) {
        $environment_metamodel = data_controller_get_environment_metamodel();

        $datasource = $environment_metamodel->getDataSource($datasourceName);

        $sql = $this->getExtension('commitTransaction')->generate($this, $datasource);
        LogHelper::log_info(new StatementLogMessage('transaction.commit', $sql));
        $this->executeStatement($datasource, $sql);
    }

    public function rollbackTransaction($datasourceName) {
        $environment_metamodel = data_controller_get_environment_metamodel();

        $datasource = $environment_metamodel->getDataSource($datasourceName);

        $sql = $this->getExtension('rollbackTransaction')->generate($this, $datasource);
        LogHelper::log_info(new StatementLogMessage('transaction.rollback', $sql));
        $this->executeStatement($datasource, $sql);
    }

    protected function getConnection(DataSourceMetaData $datasource) {
        $transaction = TransactionManager::getInstance()->getTransaction($datasource->name);
        $connectionName = $transaction->assembleResourceName(get_class($datasource), $datasource->name);

        $connection = $transaction->findResource($connectionName);
        if (!isset($connection)) {
            $connection = $this->getExtension('initializeConnection')->initialize($this, $datasource);

            if (!$datasource->temporary) {
                $transaction->registerResource($connectionName, $connection);
                $transaction->registerActionCallback(new ConnectionTransactionActionCallback($datasource->name));
            }
        }

        return $connection;
    }

    protected function executeStatement(DataSourceMetaData $datasource, $sql) {
        $timeStart = microtime(TRUE);

        $connection = $this->getConnection($datasource);

        $affectedRecordCount = 0;
        if (self::$STATEMENT_EXECUTION_MODE == self::STATEMENT_EXECUTION_MODE__PROCEED) {
            $affectedRecordCount = $this->getExtension('executeStatement')->execute($this, $connection, $sql);
        }

        LogHelper::log_info(t(
            'Database execution time for @statementCount statement(s): !executionTime',
            array(
                '@statementCount' => count($sql),
                '!executionTime' => ExecutionPerformanceHelper::formatExecutionTime($timeStart))));

        return $affectedRecordCount;
    }
}


class ConnectionTransactionActionCallback extends AbstractObject implements TransactionActionCallback, ResourceTransactionActionCallback {

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
