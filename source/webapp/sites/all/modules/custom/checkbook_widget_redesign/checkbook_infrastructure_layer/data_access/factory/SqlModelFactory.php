<?php
/**
 * Created by PhpStorm.
 * User: atorkelson
 * Date: 3/31/16
 * Time: 6:03 PM
 */

class SqlModelFactory {

    /**
     * @param $parameters
     * @param $limit
     * @param $orderBy
     * @param $sqlConfigName
     * @param $statementName
     * @return mixed
     */
    static function getSqlStatement($parameters, $limit, $orderBy, $sqlConfigName, $statementName) {
        $sqlStatementModel = self::getSqlStatementModel($parameters, $limit, $orderBy, $sqlConfigName, $statementName);
        return $sqlStatementModel->query;
    }

    /**
     * @param $parameters
     * @param $limit
     * @param $orderBy
     * @param $sqlConfigName
     * @param $statementName
     * @return SqlStatementModel
     */
    static function getSqlStatementModel($parameters, $limit, $orderBy, $sqlConfigName, $statementName) {
        $sqlModel = self::getSqlModel($sqlConfigName);
        $sqlStatementModel = $sqlModel->getStatement($statementName);
        $query = self::prepareSqlStatement($sqlStatementModel, $parameters, $limit, $orderBy);
        $sqlStatementModel->query = $query;
        return $sqlStatementModel;
    }

    /**
     * @param $sqlConfigName
     * @return SqlModel
     */
    static private function getSqlModel($sqlConfigName) {
        $xml = self::loadSqlConfigFile($sqlConfigName);
        $sqlModel = SqlModel::loadFromXml($xml);
        return $sqlModel;
    }

    /**
     * @param $sqlConfigName
     * @return SimpleXMLElement
     */
    static private function loadSqlConfigFile($sqlConfigName) {
        $file = realpath(drupal_get_path('module', 'checkbook_domain')).'/config/sql/'.$sqlConfigName.'.xml';
        $xml = simplexml_load_file($file);
        return $xml;
    }

    /**
     * @param SqlStatementModel $statementModel
     * @param $parameters
     * @param $limit
     * @param $orderBy
     * @return string
     */
    static private function prepareSqlStatement(SqlStatementModel $statementModel, $parameters, $limit, $orderBy) {
        $where = "";
        $select = $statementModel->select;
        $whereParams = $statementModel->whereParams;
        foreach($whereParams as $whereParam) {
            $where .= $where != "" ? " AND " : "\nWHERE ";
            $where .= self::processExpressions($whereParam->expressions,$parameters);
        }
        if(isset($orderBy) && $orderBy != "") {
            $orderBy = "\nORDER BY {$orderBy}";
        }
        if(isset($limit) && $limit != "") {
            $limit = "\nLIMIT {$limit}";
        }

        $sql = $select.$where.$orderBy.$limit;
        return $sql;
    }
    /**
     * @param SqlExpressionModel[] $expressions
     * @param $parameters
     * @param null $logicOperator
     * @return string
     */
    static private function processExpressions(array $expressions, $parameters, $logicOperator = NULL) {

        $where = "";
        foreach($expressions as $expression) {
            switch($expression->operatorType) {

                case SqlOperatorType::COMPARISON:
                    $paramName = $expression->paramName;
                    $dbField = $expression->dbField;
                    $paramValue = $expression->paramValue != "" ? $expression->paramValue : $parameters[$paramName];
                    if(isset($paramValue)) {
                        $where .= $where != "" ? " {$logicOperator} " : "";
                        $where .= self::buildWhereParameter($dbField,$paramValue,$expression->operator);
                    }
                    break;

                case SqlOperatorType::CONDITION:
                    $paramValue = $parameters[$expression->paramName];
                    $compareValue = $expression->compareValue;
                    $condition = $expression->condition;

                    $success = false;
                    switch($condition) {
                        case SqlOperator::EQUAL:
                            $success  = $paramValue == $compareValue;
                            break;
                    }
                    if($success) {
                        $where .= $where != "" ? " {$logicOperator} " : "";
                        $where .= self::processExpressions($expression->expressions,$parameters) ;
                    }
                    break;

                default:
                    $where = "(";
                    $where .= self::processExpressions($expression->expressions, $parameters, $expression->operator);
                    $where .= ")";
                    break;

            }
        }
        return $where;
    }

    /**
     * @param $name
     * @param $value
     * @param $operator
     * @return null|string
     */
    static private function buildWhereParameter($name,$value,$operator) {
        $where = null;
        switch($operator) {
            case SqlOperator::EQUAL:
                $where = "{$name} $operator {$value}";
                break;
            case SqlOperator::NOT_EQUAL:
                $where = "{$name} $operator {$value}";
                break;
            case SqlOperator::GREATER_THAN:
                $where = "{$name} $operator {$value}";
                break;
            case SqlOperator::LESS_THAN:
                $where = "{$name} $operator {$value}";
                break;
            case SqlOperator::GREATER_THAN_EQ:
                $where = "{$name} $operator {$value}";
                break;
            case SqlOperator::LESS_THAN_EQ:
                $where = "{$name} $operator {$value}";
                break;
            case SqlOperator::LIKE:
                $where = "{$name} $operator {$value}";
                break;
            case SqlOperator::ILIKE:
                $where = "{$name} $operator {$value}";
                break;
            case SqlOperator::IS:
                $where = "{$name} $operator {$value}";
                break;
            case SqlOperator::IS_NOT:
                $where = "{$name} $operator {$value}";
                break;
        }
        return $where;
    }
} 