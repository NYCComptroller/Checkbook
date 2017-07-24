<?php


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
        $statementName = isset($statementName) ? $statementName : $this->statementName;
        $sqlConfigPath = $this->sqlConfigPath;

        try {
            $sqlModel = SqlModelFactory::getSqlStatementModel($parameters, $limit, $orderBy, $sqlConfigPath, $statementName);
            $data = SqlUtil::executeSqlQuery($sqlModel);
            $results = (new SqlDatasetFactory())->create($data);
        }
        catch (Exception $e) {
            log_error("Error in function SqlRepository::getByDataset() \nError getting data from controller: \n" . $e->getMessage());
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

        $statementName = isset($statementName) ? $statementName : $this->statementName;
        $sqlConfigPath = $this->sqlConfigPath;

        try {
            $sqlModel = SqlModelFactory::getSqlStatementModel($parameters, null, null, $sqlConfigPath, $statementName);
            $data = SqlUtil::executeCountSqlQuery($sqlModel);
            $results = (new SqlRecordCountFactory())->create($data);
        }
        catch (Exception $e) {
            log_error("Error in function SqlRepository::getByDatasetRowCount() \nError getting data from controller: \n" . $e->getMessage());
            throw $e;
        }
        return $results;
    }
}