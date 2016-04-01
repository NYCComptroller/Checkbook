<?php
/**
 * Created by PhpStorm.
 * User: atorkelson
 * Date: 3/31/16
 * Time: 6:03 PM
 */

class SqlModelFactory {

    /**
     * @param $statement
     * @param $parameters
     * @param $limit
     * @param $orderBy
     * @param $type
     * @return mixed
     */
    static function getSqlStatement($statement, $parameters, $limit, $orderBy, $type) {
        $sqlStatementModel = self::getSqlStatementModel($statement, $parameters, $limit, $orderBy, $type);
        return $sqlStatementModel->query;
    }

    /**
     * @param $statement
     * @param $parameters
     * @param $limit
     * @param $orderBy
     * @param $type
     * @return SqlStatementModel
     */
    static function getSqlStatementModel($statement, $parameters, $limit, $orderBy, $type) {
        $sqlModel = self::getSqlModel($type);
        $sqlStatementModel = $sqlModel->getStatement($statement);
        $query = self::prepareSqlStatement($sqlStatementModel, $parameters, $limit, $orderBy);
        $sqlStatementModel->query = $query;
        return $sqlStatementModel;
    }

    /**
     * @param $type
     * @return SqlModel
     */
    static private function getSqlModel($type) {
        $xml = self::loadSqlConfigFile($type);
        $sqlModel = SqlModel::loadFromXml($xml);
        return $sqlModel;
    }

    /**
     * @param $type
     * @return SimpleXMLElement
     */
    static private function loadSqlConfigFile($type) {
        $file = realpath(drupal_get_path('module', 'checkbook_domain')).'/'.$type.'/config/sql/'.$type.'.xml';
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
     * @return null|string
     */
    static private function processExpressions(array $expressions, $parameters) {

        $where = "";
        foreach($expressions as $expression) {
            if($expression->operatorType == SqlOperatorType::COMPARISON) {
                $paramName = $expression->paramName;
                $dbField = $expression->dbField;
                $paramValue = $expression->paramValue != "" ? $expression->paramValue : $parameters[$paramName];
                if(isset($paramValue)) {
                    $where .= $where != "" ? " AND " : "";
                    $where .= self::buildWhereParameter($dbField,$paramValue,$expression->operator);
                }
            }
            else {
                $where = "(";
                $where .= self::processExpressions($expression->expressions,$parameters);
                $where .= ")";
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