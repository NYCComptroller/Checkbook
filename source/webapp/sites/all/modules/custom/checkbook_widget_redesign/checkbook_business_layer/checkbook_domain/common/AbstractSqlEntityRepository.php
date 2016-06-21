<?php

abstract class AbstractSqlEntityRepository implements ISqlEntityRepository {

    private $sqlModel;

    /**
     * Returns the data from executing the query
     * @param $parameters
     * @param $limit
     * @param $order_by
     * @param $statementName
     * @param $sqlConfigPath
     * @return mixed
     */
    public function getData($parameters, $limit, $order_by, $statementName, $sqlConfigPath)
    {
        $data = $this->_getData($parameters, $limit, $order_by, $statementName, $sqlConfigPath);
        $result = $data->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    /**
     * Returns total number of records from executing the query
     * @param $parameters
     * @param $statementName
     * @param $sqlConfigPath
     * @return mixed
     */
    public function getDataCount($parameters, $statementName, $sqlConfigPath)
    {
        $data = $this->_getDataCount($parameters, $statementName, $sqlConfigPath);
        $results = $data->fetchAll();
        return $results[0]->record_count;
    }

    /**
     * Returns records from executing SQL specified in $statementName
     * @param $parameters
     * @param $statementName
     * @param $sqlConfigPath
     * @param $limit
     * @param $order_by
     * @return DatabaseStatementInterface|null
     * @throws Exception
     */
    private function _getData($parameters, $limit, $order_by, $statementName, $sqlConfigPath)
    {
        try {
            $this->sqlModel = SqlModelFactory::getSqlStatementModel($parameters, $limit, $order_by, $sqlConfigPath, $statementName);
            $results = SqlUtil::executeSqlQuery($this->sqlModel);
        }
        catch (Exception $e) {
            log_error("Error in function SqlRepository::getData() \nError getting data from controller: \n" . $e->getMessage());
            throw $e;
        }
        return $results;
    }

    /**
     * Returns the count of records returned from the SQL in $statementName
     * @param $parameters
     * @param $statementName
     * @param $sqlConfigPath
     * @return DatabaseStatementInterface|null
     * @throws Exception
     */
    private function _getDataCount($parameters, $statementName, $sqlConfigPath)
    {
        try {
            $test = new SqlUtil();
            $this->sqlModel = SqlModelFactory::getSqlStatementModel($parameters, null, null, $sqlConfigPath, $statementName);
            $results = SqlUtil::executeCountSqlQuery($this->sqlModel);
        }
        catch (Exception $e) {
            log_error("Error in function SqlRepository::_getDataCount() \nError getting data from controller: \n" . $e->getMessage());
            throw $e;
        }
        return $results;
    }
}