<?php

namespace Drupal\checkbook_infrastructure_layer\DataAccess\Models;

use SimpleXMLElement;

abstract class AbstractSqlModel implements ISqlModel {

  /**
   * @param SimpleXMLElement $xml
   *
   * @return ISqlModel[]
   */
  public static function loadChildXElements(SimpleXMLElement $xml) {
    $childModels = [];
    if (isset(static::$childElements)) {
      foreach (static::$childElements as $childElement) {
        $childModels[] = self::loadChildElements($xml, $childElement['xpath'], $childElement['class']);
      }
    }
    return $childModels;
  }

  /**
   * @param SimpleXMLElement $xml
   * @param $path
   * @param $modelClass
   *
   * @return ISqlModel[]
   */
  private static function loadChildElements(SimpleXMLElement $xml, $path, $modelClass) {
    $childModels = [];
    foreach ($xml->xpath($path) as $childXml) {
      switch ($modelClass) {
        case 'SqlStatementModel':
          $childModel = SqlStatementModel::loadFromXml($childXml);
          break;
        case 'SqlParamModel':
          $childModel = SqlParamModel::loadFromXml($childXml);
          break;
        case 'SqlWhereParamModel':
          $childModel = SqlWhereParamModel::loadFromXml($childXml);
          break;
        case 'SqlExpressionModel':
          $childModel = SqlExpressionModel::loadFromXml($childXml);
          break;
      }
      $childModels[] = $childModel;
    }
    return $childModels;
  }

}
