<?php

interface IDataService {

    function configure($dataFunction, $sqlConfigPath, $parameters, $limit = null, $orderBy = null);
    function setSqlConfigPath($sqlConfigPath);
    function setDataFunction($fnData);
    function setParameters($parameters);
    function setLimit($limit);
    function setOrderBy($orderBy);
    function getByDataset($parameters = null, $limit = null, $orderBy = null);
    function getByDatasetRowCount($parameters = null);
}
