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
        $query = $sqlStatementModel->sql;

        //expressions
        $expressions = $sqlStatementModel->expressions;
        $paramsModel = $sqlStatementModel->parameters;
        foreach($expressions as $expression) {
            $exp = "";
            if($expression->operatorType == SqlOperatorType::CONDITION)
            {
                $paramValue = $parameters[$expression->paramName];
                $compareValue = $expression->compareValue;
                $condition = $expression->condition;
                $paramType = "";
                foreach($paramsModel as $paramModel) {
                    if($paramModel->name == $expression->paramName) {
                        $paramType = $paramModel->type;
                        break;
                    }
                }

                $success = false;
                if($paramType == "bool") {
                    $paramValue = (bool)$paramValue;
                    $compareValue = (bool)$compareValue;
                }
                switch($condition) {
                    case SqlOperator::EQUAL:
                        $success  = $paramValue == $compareValue;
                        break;
                    case SqlOperator::NOT_EQUAL:
                        $success  = $paramValue != $compareValue;
                        break;
                }
                if($success) {
                    $exp = strip_tags($expression->xmlString);
                }
            }
            $replacement = $expression->xmlString;
            $query = str_replace($replacement, $exp, $query);
        }

        //where
        $whereParams = $sqlStatementModel->whereParams;
        $paramsModel = $sqlStatementModel->parameters;
        foreach($whereParams as $whereParam) {
            $expressions = $whereParam->expressions;
            $where = self::processExpressions($expressions,$parameters,$paramsModel);
            $where = $where != "" ? "\nWHERE {$where}" : "";
            $query = self::replaceTags($query, $where, "where");
        }
        //group by
        $groupBy = $sqlStatementModel->groupBy;
        if(isset($groupBy) && $groupBy != "") {
            $query .= "\nGROUP BY {$groupBy}";
        }
        //having
        $having = $sqlStatementModel->having;
        if(isset($having) && $having != "") {
            $query .= "\nHAVING {$having}";
        }
        //order by
        if(isset($orderBy) && $orderBy != "") {
            $orderBy = "\nORDER BY {$orderBy}";
        }
        //limit
        if(isset($limit) && $limit != "") {
            $limit = "\nLIMIT {$limit}";
        }
        $sql = $query.$orderBy.$limit;
        $sqlStatementModel->query = $sql;

        //count query
        $sqlStatementModel->countQuery = "SELECT COUNT(*) as record_count FROM ( {$query} ) sub_query";
        return $sqlStatementModel;
    }

    /**
     * Function to replace tags with text
     *
     * @param $source
     * @param $newText
     * @param $tagName
     * @return mixed
     */
    static function replaceTags($source, $newText, $tagName) {
        $startTag = "<{$tagName}>";
        $endTag = "</{$tagName}>";
        $startTagPos = strpos($source, $startTag);
        $endTagPos = strpos($source, $endTag);
        $tagLength = $endTagPos - $startTagPos + strlen($endTag);
        return substr_replace($source, $newText, $startTagPos, $tagLength);
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
     * @param SqlExpressionModel[] $expressions
     * @param $parameters
     * @param SqlParamModel[] $paramsModel
     * @param null $logicOperator
     * @return string
     */
    static private function processExpressions(array $expressions, $parameters, $paramsModel, $logicOperator = NULL) {

        $where = "";
        foreach($expressions as $expression) {
            switch($expression->operatorType) {

                case SqlOperatorType::COMPARISON:
                    $paramName = $expression->paramName;
                    $dbField = $expression->dbField;
                    $paramValue = $expression->paramValue != "" ? $expression->paramValue : $parameters[$paramName];
                    $paramType = "";
                    if(isset($paramValue)) {
                        foreach($paramsModel as $paramModel) {
                            if($paramModel->name == $paramName) {
                                $paramType = $paramModel->type;
                                break;
                            }
                        }
                        $where .= $where != "" ? " {$logicOperator} " : "";
                        $where .= self::buildWhereParameter($dbField,$paramValue,$expression->operator,$paramType);
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
                        case SqlOperator::NOT_EQUAL:
                            $success  = $paramValue != $compareValue;
                            break;
                    }
                    if($success) {
                        $where .= $where != "" ? " {$logicOperator} " : "";
                        $where .= self::processExpressions($expression->expressions,$parameters,$paramsModel) ;
                    }
                    break;

                default:
                    $where .= self::processExpressions($expression->expressions, $parameters, $paramsModel, $expression->operator);
                    $where = $where != "" ? "({$where})" : "";
                    break;

            }
        }
        return $where;
    }

    /**
     * @param $name
     * @param $value
     * @param $operator
     * @param $type
     * @return null|string
     */
    static private function buildWhereParameter($name,$value,$operator,$type) {
        $where = null;
        if($type == "string") {
            $value = "'{$value}'";
        }
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
            case SqlOperator::IN:
                $value = (substr($value, 0, 1) != "(" && substr($value, -1, 1) != ")") ? "(".$value.")" : $value;
                $where = "{$name} $operator {$value}";
                break;
            case SqlOperator::NOT_IN:
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