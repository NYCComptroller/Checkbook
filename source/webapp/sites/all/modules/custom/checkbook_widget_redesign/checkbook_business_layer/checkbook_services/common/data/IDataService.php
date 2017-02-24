<?php

interface IDataService {

    function configure($dataFunction, $parameters, $limit = null, $orderBy = null, $sqlConfigPath);
    function setSqlConfigPath($sqlConfigPath);
    function setDataFunction($fnData);
    function setParameters($parameters);
    function setLimit($limit);
    function setOrderBy($orderBy);
    function getByDataset($parameters = null, $limit = null, $orderBy = null);
    function getByDatasetRowCount($parameters = null);
}