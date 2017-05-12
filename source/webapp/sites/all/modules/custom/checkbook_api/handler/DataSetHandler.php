<?php
/**
* This file is part of the Checkbook NYC financial transparency software.
* 
* Copyright (C) 2012, 2013 New York City
* 
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU Affero General Public License as
* published by the Free Software Foundation, either version 3 of the
* License, or (at your option) any later version.
* 
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU Affero General Public License for more details.
* 
* You should have received a copy of the GNU Affero General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
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
    $required_columns = $criteria['required_columns'];

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

        if(isset($required_columns)) {
            foreach ($required_columns as $required_column) {
                if (array_search($required_column, $columns) == FALSE) {
                    $columns[] = $required_column;
                }
            }

        }

      // Prepare offset:
      $start_with = (isset($request_start_with)) ? $request_start_with : 0;
      $limit = $request_limit;

      $parameters = $this->getDataSetParameters($data_set, $criteria);

        // array of new sort order for contracts datafeed export
        $contractsOrder = array("fiscal_year@checkbook:contracts_coa_aggregates_datafeeds",
            "document_code",
            "contract_number",
            "scntrc_status_name",
            "vendor_record_type",
            "prime_vendor_name",
            "prime_minority_type_name",
            "prime_purpose",
            "prime_maximum_contract_amount",
            "prime_original_contract_amount",
            "prime_rfed_amount",
            "prime_effective_begin_date",
            "sub_effective_end_date_export",
            "registered_date",
            "agency_name",
            "version",
            "master_contract_number_export",
            "agreement_type_name",
            "award_method_name",
            "expenditure_object_names",
            "prime_industry_type_name",
            "prime_pin",
            "prime_apt_pin",
            "sub_vendor_name_export",
            "sub_minority_type_name_export",
            "sub_purpose_export",
            "aprv_sta_name_export",
            "sub_industry_type_name_export",
            "sub_maximum_contract_amount",
            "sub_original_contract_amount",
            "sub_rfed_amount",
            "sub_effective_begin_date_export",
            "prime_effective_end_date",
            "sub_contract_id_export"
        );

        // takes $columns and sorts them by $intendedOrder (currently hardcoded as $contractsOrder)
        function sortColumnsForContracts(array $selectedColumns, array $intendedOrder) {
            $ordered = [];
            foreach ($intendedOrder as $key) {
                if (in_array($key, $selectedColumns)) {
                    array_push($ordered, $key);
                }
            }
            return $ordered;
        }

        // to debug array order, to be deleted - isaac
        var_dump($columns);
        var_dump(sortColumnsForContracts($columns, $contractsOrder));

      $data_set->columns = sortColumnsForContracts($columns, $contractsOrder);
//      $data_set->columns = $columns;
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
      case "contains":
          $pattern = "(.* $value .*)|(.* $value$)|(^$value.*)|(.* $value.*)";
          $parameters[$column] = data_controller_get_operator_factory_instance()->initiateHandler(RegularExpressionOperatorHandler::$OPERATOR__NAME, $pattern);
        break;

      default:
        $parameters[$column] = $value;
        break;
    }
  }
}
