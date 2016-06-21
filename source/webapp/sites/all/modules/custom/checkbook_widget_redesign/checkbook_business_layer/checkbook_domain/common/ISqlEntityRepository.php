<?php
/**
 * Created by PhpStorm.
 * User: atorkelson
 * Date: 6/21/16
 * Time: 2:32 PM
 */

interface ISqlEntityRepository {
    public function getData($parameters, $limit, $order_by, $statementName, $sqlConfigName);
    public function getDataCount($parameters, $statementName, $sqlConfigName);
} 