<?php

/**
 * Interface ISqlEntityRepository
 */
interface ISqlEntityRepository {

    /**
     * Returns the dataset from executing the query
     * @param $parameters
     * @param $limit
     * @param $orderBy
     * @param $statementName
     * @return array
     * @throws Exception
     */
    public function getByDataset($parameters, $limit, $orderBy, $statementName);

    /**
     * Returns total number of records from executing the query
     * @param $parameters
     * @param $statementName
     * @return int
     * @throws Exception
     */
    public function getByDatasetRowCount($parameters, $statementName);

} 