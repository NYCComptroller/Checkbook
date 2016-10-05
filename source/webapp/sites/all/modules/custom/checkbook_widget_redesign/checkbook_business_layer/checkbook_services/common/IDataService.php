<?php
/**
 * Created by PhpStorm.
 * User: atorkelson
 * Date: 6/15/16
 * Time: 3:42 PM
 */

interface IDataService {
    public function getData($parameters, $limit, $orderBy, $statementName, $sqlConfigPath = null);
} 