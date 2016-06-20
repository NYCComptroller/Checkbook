<?php
/**
 * Created by PhpStorm.
 * User: atorkelson
 * Date: 6/16/16
 * Time: 4:37 PM
 */

interface ISqlEntityRepository {
    public function getData($parameters, $limit, $order_by, $statementName, $sqlConfigName);
    public function getDataCount($parameters, $statementName, $sqlConfigName);
} 