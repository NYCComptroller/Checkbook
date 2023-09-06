<?php
namespace Drupal\checkbook_project\WidgetUtilities;
use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;
use Drupal\data_controller\Datasource\Formatter\Handler\ArrayResultFormatter;
use Drupal\data_controller\Datasource\Formatter\Handler\SpecialCharacterResultFormatter;
use Drupal\data_controller\Datasource\Operator\Handler\GreaterOrEqualOperatorHandler;
use Drupal\data_controller\Datasource\Operator\Handler\GreaterThanOperatorHandler;
use Drupal\data_controller\Datasource\Operator\Handler\LessOrEqualOperatorHandler;
use Drupal\data_controller\Datasource\Operator\Handler\NotEqualOperatorHandler;
use Drupal\data_controller\Datasource\Operator\Handler\NotInRangeOperatorHandler;
use Drupal\data_controller\Datasource\Operator\Handler\NotRegularExpressionOperatorHandler;
use Drupal\data_controller\Datasource\Operator\Handler\NotWildcardOperatorHandler;
use Drupal\data_controller\Datasource\Operator\Handler\RangeOperatorHandler;
use Drupal\data_controller\Datasource\Operator\Handler\RegularExpressionOperatorHandler;
use Drupal\data_controller\Datasource\Operator\Handler\WildcardOperatorHandler;

Class WidgetProcessor{
  /**
   * For nodes that cannot have an aggregate value, this function will aggregate any column and return the value
   * on the fly using the provided widget config.
   * @param $node
   * @param $aggregateColumn
   * @return mixed
   */
  public static function _checkbook_project_pre_process_aggregation($node, $aggregateColumn){
    $params = $node->nodeAdjustedParamaterConfig;
    $ds = _update_dataset_datasource($node->widgetConfig->dataset, $node->widgetConfig->keepOriginalDatasource);
    $column = $aggregateColumn;
    $orderBy = $node->widgetConfig->orderBy;
    $startWith = $node->widgetConfig->startWith ?? 0;
    $limit = $node->widgetConfig->limit;
    $resultFormatter = new SpecialCharacterResultFormatter(NULL, new ArrayResultFormatter());
    //Handle multiple cube joins
    if (str_contains($aggregateColumn, '@')) {
      $col_array = explode("@", $aggregateColumn);
      $column = $col_array[0];
      $ds = $col_array[1];
    }
    $dataQueryController = data_controller_get_instance();
    $result = $dataQueryController->queryCube($ds, array($column), $params, $orderBy, $startWith, $limit, $resultFormatter);
    return $result[0][$column];
  }

  /**
   * Function to update the JSON configuration at runtime for advance search spending results.
   * The vendor facet will use 'LIKE' search initially, once the facet is populated and clicked the first time,
   * the vendor name is exact and an 'IN' search can be used for speed and accuracy.
   * @param $node
   */
  public static function _checkbook_project_adjust_vendor_facet_json(&$node){
    if (str_contains(RequestUtilities::getCurrentPageUrl(), 'fvendor')) {
      //Vendor Facet
      if ($node->widgetConfig->filterName == 'Vendor') {
        if (isset($node->widgetConfig->urlParamMap->fvendor)) {
          unset($node->widgetConfig->urlParamMap->fvendor);
        }
        if (($key = array_search('fvendor', $node->widgetConfig->cleanURLParameters)) !== false) {
          unset($node->widgetConfig->cleanURLParameters[$key]);
        }
      } else {
        if (isset($node->widgetConfig->urlParamMap->vendornm)) {
          unset($node->widgetConfig->urlParamMap->vendornm);
        }
        if (($key = array_search('vendornm', $node->widgetConfig->cleanURLParameters)) !== false) {
          unset($node->widgetConfig->cleanURLParameters[$key]);
        }
      }
    }
  }

  /**
   * Function to adjust custom parameters passed to data controller module
   * @param $node Node data
   * @param $parameters Widget parsed request parameters
   * @return Widget adjusted parameters
   */
  public static function _checkbook_project_applyParameterFilters($node, $parameters){
    $adjustedParameters = $parameters;

    //Convert configuration to array for processing
    $paramTypeConfig = $node->widgetConfig->paramTypeConfig ?? NULL;
    $defaultParamTypeConfig = isset($node->widgetConfig->defaultParamTypeConfig) ? get_object_vars($node->widgetConfig->defaultParamTypeConfig) : NULL;
    if ((empty($paramTypeConfig) && empty($defaultParamTypeConfig)) || empty($adjustedParameters)) {//Nothing to adjust
      return $adjustedParameters;
    }

    //Convert configuration to array for processing
    $urlParamMap = isset($node->widgetConfig->urlParamMap) ? get_object_vars($node->widgetConfig->urlParamMap) : array();

    $originalRequestParams = $node->widgetConfig->originalRequestParams;
    $logicalOrFacet = $node->widgetConfig->logicalOrFacet;
    $unionOrFacet = $node->widgetConfig->unionOrFacet;

    //adjust request parameter configurations
    $configurationTypes = array();
    if (!empty($paramTypeConfig)) {
      foreach ($adjustedParameters as $param => $value) {
        $flippedParamTypeConfigKeys = array_flip(array_keys($urlParamMap, $param));
        if (!isset($originalRequestParams)) {
          $originalRequestParams = array();
        }
        $intersectKeys = array_intersect_key($flippedParamTypeConfigKeys, $originalRequestParams);

        foreach ($intersectKeys as $intersectKey => $keyValue) {
          if ($paramTypeConfig->$intersectKey && $urlParamMap[$intersectKey] == $param) {
            if ((!isset($logicalOrFacet) || !$logicalOrFacet) && (!isset($unionOrFacet) || !$unionOrFacet)) {
              $configurationTypes[$param][$paramTypeConfig->$intersectKey] = explode("~", $originalRequestParams[$intersectKey]);
            }
          }
        }
      }
    }

    //adjust default parameter configurations if not adjusted above.
    $defaultParameters = $node->widgetConfig->defaultParameters;
    if (isset($defaultParameters) && isset($defaultParamTypeConfig)) {
      $defaultConfigParameters = get_object_vars($defaultParameters);
      $defaultParamTypeConfigArray = get_object_vars($node->widgetConfig->defaultParamTypeConfig);
      if (!empty($defaultParamTypeConfigArray)) {
        foreach ($defaultConfigParameters as $key => $value) {
          if (!array_key_exists($key, $configurationTypes) //It might already been adjusted if passed in request URL.
            && array_key_exists($key, $defaultParamTypeConfigArray)) {
            $tempValues = explode("~", isset($originalRequestParams[$key]) ? $originalRequestParams[$key] : $value);
            $adjustedParameters[$key] = $tempValues;
            $configurationTypes[$key][$defaultParamTypeConfigArray[$key]] = $tempValues;
          }
        }
      }
    }

    if (empty($configurationTypes)) {//Nothing to adjust
      return $adjustedParameters;
    }

    $data_controller_instance = data_controller_get_operator_factory_instance();
    foreach ($adjustedParameters as $param => $value) {
      if (!array_key_exists($param, $configurationTypes)) {//Configuration for param do not exist
        continue;
      }

      $paramConfigTypes = $configurationTypes[$param];
      $conditions = NULL;

      foreach ($paramConfigTypes as $paramConfigType => $paramValues) {
        switch ($paramConfigType) {
          case "capitalize":
            foreach($paramValues as $value) {
              $conditions[] = strtoupper($value);
            }
            break;
          case "necapitalize":
            $vals = [];
            foreach($paramValues as $value) {
              $vals[] = strtoupper($value);
            }
            $conditions[] = $data_controller_instance->initiateHandler(NotEqualOperatorHandler::$OPERATOR__NAME, $vals);
            break;
          case "like":
            $localValue = (is_array($paramValues) && count($paramValues) > 1) ? implode('~', $paramValues) : $paramValues[0];
            $conditions[] = $data_controller_instance->initiateHandler(WildcardOperatorHandler::$OPERATOR__NAME, array($localValue, FALSE, TRUE));
            break;
          case "contains":
            $localValue = (is_array($paramValues) && count($paramValues) > 1) ? implode('~', $paramValues) : $paramValues[0];
            $localValue = _checkbook_regex_replace_pattern($localValue);
            $pattern = "(.* $localValue .*)|(.* $localValue$)|(^$localValue.*)|(.* $localValue.*)";
            $conditions[] = $data_controller_instance->initiateHandler(RegularExpressionOperatorHandler::$OPERATOR__NAME, $pattern);
            break;
          case "nlike":
            $localValue = (is_array($paramValues) && count($paramValues) > 1) ? implode('~', $paramValues) : $paramValues[0];
            $conditions[] = $data_controller_instance->initiateHandler(NotWildcardOperatorHandler::$OPERATOR__NAME, array($localValue, FALSE, TRUE));
            break;
          case "autocomplete":
            $localValue = (is_array($paramValues) && count($paramValues) > 1) ? implode('~', $paramValues) : $paramValues[0];
            $parts = explode(" ", $localValue);
            foreach ($parts as $part) {
              //$part = str_replace("(","\\(",$part);
              $part = _checkbook_regex_replace_pattern($part);
              $pattern = "(.* " . $part . ".*)|(^" . $part . ".*)";
              $conditions[] = $data_controller_instance->initiateHandler(RegularExpressionOperatorHandler::$OPERATOR__NAME, $pattern);
            }
            break;
          case "eqignorecase":
            $patterns = array();
            foreach ($paramValues as $value) {
              $patterns[] = "(^" . _checkbook_regex_replace_pattern($value) . "$)";
            }
            $conditions[] = $data_controller_instance->initiateHandler(RegularExpressionOperatorHandler::$OPERATOR__NAME, implode( "|",$patterns));
            break;
          case "neqignorecase":
            $patterns = array();
            foreach ($paramValues as $value) {
              $patterns[] = "(^" . _checkbook_regex_replace_pattern($value) . "$)";
            }
            $conditions[] = $data_controller_instance->initiateHandler(NotRegularExpressionOperatorHandler::$OPERATOR__NAME, implode("|",$patterns));
            break;
          case "ne":
            $conditions[] = $data_controller_instance->initiateHandler(NotEqualOperatorHandler::$OPERATOR__NAME, $paramValues);
            break;
          case "gt":
            $conditions[] = $data_controller_instance->initiateHandler(GreaterThanOperatorHandler::$OPERATOR__NAME, $paramValues[0]);
            break;
          case "gte":
            $conditions[] = $data_controller_instance->initiateHandler(GreaterOrEqualOperatorHandler::$OPERATOR__NAME, $paramValues[0]);
            break;
          case "range":
            $conditions[] = $data_controller_instance->initiateHandler(RangeOperatorHandler::$OPERATOR__NAME, array($paramValues[0], $paramValues[1]));
            break;
          case "le":
            $conditions[] = $data_controller_instance->initiateHandler(LessOrEqualOperatorHandler::$OPERATOR__NAME, $paramValues[0]);
            break;
          case "rangeid":
            //Since support for for OR conditions is not present,
            //currently negating the selection criteria is only alternative.
            for ($i = 1; $i < 7; $i++) {
              switch ($i) {
                case 1:
                  if (!in_array($i, $paramValues)) {
                    $conditions[] = $data_controller_instance->initiateHandler(GreaterOrEqualOperatorHandler::$OPERATOR__NAME, 1000000);
                  }
                  break;
                case 2:
                  if (!in_array($i, $paramValues)) {
                    $conditions[] = $data_controller_instance->initiateHandler(NotInRangeOperatorHandler::$OPERATOR__NAME, array(1000000, 10000000));
                  }
                  break;
                case 3:
                  if (!in_array($i, $paramValues)) {
                    $conditions[] = $data_controller_instance->initiateHandler(NotInRangeOperatorHandler::$OPERATOR__NAME, array(10000000.01, 25000000));
                  }
                  break;
                case 4:
                  if (!in_array($i, $paramValues)) {
                    $conditions[] = $data_controller_instance->initiateHandler(NotInRangeOperatorHandler::$OPERATOR__NAME, array(25000000.01, 50000000));
                  }
                  break;
                case 5:
                  if (!in_array($i, $paramValues)) {
                    $conditions[] = $data_controller_instance->initiateHandler(NotInRangeOperatorHandler::$OPERATOR__NAME, array(50000000.01, 100000000));
                  }
                  break;
                case 6:
                  if (!in_array($i, $paramValues)) {
                    $conditions[] = $data_controller_instance->initiateHandler(LessOrEqualOperatorHandler::$OPERATOR__NAME, 100000000);
                  }
                  break;
                default:
                  break;
              }
            }
            break;
          case "rangeid2":
            //Since support for for OR conditions is not present,
            //currently negating the selection criteria is only alternative.
            for ($i = 1; $i < 7; $i++) {
              switch ($i) {
                case 1:
                  if (!in_array($i, $paramValues)) {
                    $conditions[] = $data_controller_instance->initiateHandler(GreaterOrEqualOperatorHandler::$OPERATOR__NAME, 1000000000);
                  }
                  break;
                case 2:
                  if (!in_array($i, $paramValues)) {
                    $conditions[] = $data_controller_instance->initiateHandler(NotInRangeOperatorHandler::$OPERATOR__NAME, array(1000000000, 2000000000));
                  }
                  break;
                case 3:
                  if (!in_array($i, $paramValues)) {
                    $conditions[] = $data_controller_instance->initiateHandler(NotInRangeOperatorHandler::$OPERATOR__NAME, array(2000000000.01, 3000000000));
                  }
                  break;
                case 4:
                  if (!in_array($i, $paramValues)) {
                    $conditions[] = $data_controller_instance->initiateHandler(NotInRangeOperatorHandler::$OPERATOR__NAME, array(3000000000.01, 4000000000));
                  }
                  break;
                case 5:
                  if (!in_array($i, $paramValues)) {
                    $conditions[] = $data_controller_instance->initiateHandler(NotInRangeOperatorHandler::$OPERATOR__NAME, array(4000000000.01, 5000000000));
                  }
                  break;
                case 6:
                  if (!in_array($i, $paramValues)) {
                    $conditions[] = $data_controller_instance->initiateHandler(LessOrEqualOperatorHandler::$OPERATOR__NAME, 5000000000);
                  }
                  break;
                default:
                  break;
              }
            }
            break;
          case "rangeid3":
            for ($i = 1; $i < 6; $i++) {
              switch ($i) {
                case 1:
                  if (!in_array($i, $paramValues)) {
                    $conditions[] = $data_controller_instance->initiateHandler(GreaterOrEqualOperatorHandler::$OPERATOR__NAME, 20000);
                  }
                  break;
                case 2:
                  if (!in_array($i, $paramValues)) {
                    $conditions[] = $data_controller_instance->initiateHandler(NotInRangeOperatorHandler::$OPERATOR__NAME, array(20000, 50000));
                  }
                  break;
                case 3:
                  if (!in_array($i, $paramValues)) {
                    $conditions[] = $data_controller_instance->initiateHandler(NotInRangeOperatorHandler::$OPERATOR__NAME, array(50000.01, 100000));
                  }
                  break;
                case 4:
                  if (!in_array($i, $paramValues)) {
                    $conditions[] = $data_controller_instance->initiateHandler(NotInRangeOperatorHandler::$OPERATOR__NAME, array(100000.01, 250000));
                  }
                  break;
                case 5:
                  if (!in_array($i, $paramValues)) {
                    $conditions[] = $data_controller_instance->initiateHandler(LessOrEqualOperatorHandler::$OPERATOR__NAME, 250000);
                  }
                  break;
                default:
                  break;
              }
            }
            break;
          default:
            break;
        }
      }

      if (empty($conditions)) {
        unset($adjustedParameters[$param]);
      } else {
        $adjustedParameters[$param] = $conditions;
      }
    }
    return $adjustedParameters;
  }

}
