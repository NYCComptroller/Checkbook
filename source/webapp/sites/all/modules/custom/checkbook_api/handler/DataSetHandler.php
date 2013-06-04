<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


/**
 * Class for preparing data sets based on request
 */
class DataSetHandler {
  private $requestCriteria;
  private $configuration;

  /**
   * @param $requestCriteria
   * @param $configuration
   */
  function __construct($requestCriteria, $configuration) {
    $this->requestCriteria = $requestCriteria;
    $this->configuration = $configuration;
  }

  /**
   * @return mixed
   */
  function prepareDataSet() {
    $criteria = $this->requestCriteria->getCriteria();

    $request_start_with = (isset($criteria['global']['records_from'])) ? $criteria['global']['records_from'] : NULL;
    $request_limit = (isset($criteria['global']['max_records'])) ? $criteria['global']['max_records'] : NULL;
    $response_columns_criteria = $criteria['responseColumns'];
    $response_format = $criteria['global']['response_format'];

    $data_set = $this->configuration->dataset;
    $data_set->configuration = $this->configuration;
    if (isset($data_set)) {
      // Prepare columns:
      $columns = NULL;

      $elements_columns = get_object_vars($data_set->displayConfiguration->$response_format->elementsColumn);
      if (isset($response_columns_criteria)) {
        foreach ($response_columns_criteria as $response_column) {
          $columns[] = $elements_columns[$response_column];
        }
      }
      else {
        $columns = array_values($elements_columns);
      }


      // Prepare offset:
      $start_with = (isset($request_start_with)) ? $request_start_with : 0;
      $limit = $request_limit;

      $parameters = $this->getDataSetParameters($data_set, $criteria);
      $data_set->columns = $columns;
      $data_set->parameters = $parameters;
      $data_set->startWith = $start_with;
      $data_set->limit = $limit;
    }

    return $data_set;
  }

  /**
   * @param $data_set
   * @param $criteria
   * @return null
   */
  private function getDataSetParameters(&$data_set, $criteria) {
    // Prepare parameters:
    $parameters = NULL;
    $search_criteria_map = $this->configuration->searchCriteriaMap;
    $column_types = $data_set->columnTypes;

    // Value based parameters:
    if (is_array($criteria['value'])) {
      foreach ($criteria['value'] as $parameter => $value) {
        $column = $search_criteria_map->$parameter;
        if (isset($column)) {
          $this->prepareParameterConfiguration($parameters, $column, $value, (isset($column_types) ? $column_types->$column : NULL));
        }
      }
    }

    // Range based parameters:
    if (is_array($criteria['range'])) {
      foreach ($criteria['range'] as $parameter => $values) {
        $column = $search_criteria_map->$parameter;

        if (isset($column)) {
          $this->prepareParameterConfiguration($parameters, $column, $values, 'range');
        }
      }
    }

    if (isset($data_set->adjustParameters)) {
      eval($data_set->adjustParameters);
    }

    return $parameters;
  }

  /**
   * @param $parameters
   * @param $column
   * @param $value
   * @param $config_type
   */
  private function prepareParameterConfiguration(&$parameters, $column, $value, $config_type) {
    switch ($config_type) {
      case "range":
        $conditions[] = data_controller_get_operator_factory_instance()->initiateHandler(RangeOperatorHandler::$OPERATOR__NAME, array(
          $value[0],
          $value[1],
        ));
        $parameters[$column] = $conditions;
        break;

      case "like":
        $parameters[$column] = data_controller_get_operator_factory_instance()->initiateHandler(WildcardOperatorHandler::$OPERATOR__NAME, array(
          $value,
          FALSE,
          TRUE,
        ));
        break;

      case "trueLike":
        $parameters[$column] = data_controller_get_operator_factory_instance()->initiateHandler(WildcardOperatorHandler::$OPERATOR__NAME, array(
          $value,
          TRUE,
          TRUE,
        ));
        break;

      default:
        $parameters[$column] = $value;
        break;
    }
  }
}
