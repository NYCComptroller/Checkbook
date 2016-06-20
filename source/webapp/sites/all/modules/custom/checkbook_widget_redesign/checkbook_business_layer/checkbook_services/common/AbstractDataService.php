<?php
abstract class AbstractDataService implements IDataService {

    protected $sqlConfigPath;
    protected $repository;

    function __construct($sqlConfigPath) {
        $this->sqlConfigPath = $sqlConfigPath;
        $this->repository = new SqlEntityRepository();
    }

    /**
     * Returns the data after executing the Sql
     * @param $parameters
     * @param $limit
     * @param $orderBy
     * @param $statementName
     * @param null $sqlConfigPath
     * @return mixed
     */
    public function getData($parameters, $limit, $orderBy, $statementName, $sqlConfigPath = null) {
        $sqlConfigPath = $sqlConfigPath || $this->sqlConfigPath;
        $data = $this->repository->getData($parameters, $limit, $orderBy, $sqlConfigPath, $statementName);
        return $data;
    }
} 