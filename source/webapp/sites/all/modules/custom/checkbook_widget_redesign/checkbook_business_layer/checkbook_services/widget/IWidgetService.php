<?php
/**
 * Created by PhpStorm.
 * User: atorkelson
 * Date: 4/8/16
 * Time: 3:07 PM
 */

interface IWidgetService {
    public function getWidgetData($parameters, $limit, $orderBy, $sqlConfigPath = null, $statementName = null);
    public function getWidgetDataCount($parameters, $sqlConfigPath = null, $statementName = null);
    public function getWidgetHeaderCount($parameters);
    public function implDerivedColumn($column_name,$row);
} 