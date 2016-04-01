<?php
/**
 * Created by PhpStorm.
 * User: atorkelson
 * Date: 3/31/16
 * Time: 4:49 PM
 */

abstract class SqlOperator {

    const EQUAL = '=';
    const NOT_EQUAL = '<>';
    const GREATER_THAN = '>';
    const LESS_THAN = '<';
    const GREATER_THAN_EQ = '>=';
    const LESS_THAN_EQ = '<=';
    const LIKE = 'like';
    const ILIKE = 'ilike';
    const IS = 'IS';
    const IS_NOT = 'IS NOT';
    const _AND_ = 'AND';
    const _OR_ = 'OR';

    /**
     * @param $string
     * @return bool
     */
    static public function isComparisonOperator($string) {
        switch($string) {
            case self::_AND_:
            case self::_OR_:
                return false;
        }
        return true;
    }
}

abstract class SqlOperatorType {

    const COMPARISON = '=';
    const LOGIC = '<>';
}