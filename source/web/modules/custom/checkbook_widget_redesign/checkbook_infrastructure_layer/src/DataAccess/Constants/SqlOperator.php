<?php

namespace Drupal\checkbook_infrastructure_layer\DataAccess\Constants;

abstract class SqlOperator {

  const EQUAL = '=';

  const NOT_EQUAL = '<>';

  const GREATER_THAN = '>';

  const LESS_THAN = '<';

  const GREATER_THAN_EQ = '>=';

  const LESS_THAN_EQ = '<=';

  const IN = 'IN';

  const BETWEEN = 'BETWEEN';

  const NOT_IN = 'NOT IN';

  const LIKE = 'like';

  const ILIKE = 'ilike';

  const IS = 'IS';

  const IS_NOT = 'IS NOT';

  const _AND_ = 'AND';

  const _OR_ = 'OR';

  const _IF_ = 'IF';

  /**
   * @param $string
   *
   * @return bool
   */
  public static function isComparisonOperator($string) {
    switch ($string) {
      case self::_AND_:
      case self::_OR_:
      case self::_IF_:
        return FALSE;
    }
    return TRUE;
  }

  /**
   * @param $string
   *
   * @return bool
   */
  public static function isLogicOperator($string) {
    switch ($string) {
      case self::_AND_:
      case self::_OR_:
        return TRUE;
    }
    return FALSE;
  }

  /**
   * @param $string
   *
   * @return bool
   */
  public static function isConditionOperator($string) {
    switch ($string) {
      case self::_IF_:
        return TRUE;
    }
    return FALSE;
  }

}
