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
 * Base criteria class
 */
abstract class AbstractAPISearchCriteria {

  protected $criteria;

  private $errors = array();
  private $messages = array();

  /**
   * Get search criteria.
   *
   * @return mixed
   *   criteria
   */
  function getCriteria() {
    return $this->criteria;
  }

  /**
   * Validate search criteria.
   */
  function validateCriteria() {
    $type_of_data = $this->criteria['global']['type_of_data'];

    if (!isset($this->criteria['global']['records_from'])) {
      $this->criteria['global']['records_from'] = 1;
    }
    $records_from = $this->criteria['global']['records_from'];

    if (!isset($this->criteria['global']['max_records'])) {
      $this->criteria['global']['max_records'] = $this->getMaxAllowedTransactionResults();
    }
    $max_records = $this->criteria['global']['max_records'];

    $response_format = $this->criteria['global']['response_format'];

    if (!isset($type_of_data)) {
      $this->addError(1000, array('@paramName' => 'type_of_data'));
    }
    else {
      if (!in_array($type_of_data, ValidatorConfig::$domains)) {
        $this->addError(1001, array(
          '@value' => $type_of_data,
          '@paramName' => 'type_of_data',
          '@validValues' => implode(',', ValidatorConfig::$domains),
        ));
      }
    }

    if (!in_array($response_format, ValidatorConfig::$response_formats)) {
      $this->addError(1001, array(
        '@value' => $response_format,
        '@paramName' => 'response_format',
        '@validValues' => implode(',', ValidatorConfig::$response_formats),
      ));
    }

    $this->validateResultRecordRange($records_from, $max_records);

    if (!$this->hasErrors()) {
      $domain_config = ConfigUtil::getDomainConfiguration(strtolower($type_of_data));
      $this->validateDomainCriteria($type_of_data, $domain_config);
    }
  }

  /**
   * Validate result record range.
   *
   * @param int $records_from
   *   Start of records
   * @param int $max_records
   *   Max records
   */
  protected function validateResultRecordRange($records_from, $max_records) {
    $limit_param_errors = array();
    if (!is_numeric($records_from) || !is_int($records_from + 0) || $records_from < 1) {
      $limit_param_errors[1002][] = t(Messages::$message[1002], array(
        '@value' => $records_from,
        '@paramName' => 'records_from',
      ));
    }
    else {
      // Reducing since DB row starts at 0
      $this->criteria['global']['records_from'] = $records_from - 1;
    }

    if (!is_numeric($max_records) || !is_int($max_records + 0) || $max_records < 1) {
      $limit_param_errors[1002][] = t(Messages::$message[1002], array(
        '@value' => $max_records,
        '@paramName' => 'max_records',
      ));
    }

    if (!empty($limit_param_errors)) {
      $this->addErrors($limit_param_errors);
    }
    else {
      $allowed_limit = $this->getMaxAllowedTransactionResults();
      if (isset($allowed_limit) && $max_records > $allowed_limit) {
        $this->addError(1003, array('@requestedRecords' => $max_records, '@allowedLimit' => $allowed_limit));
      }
    }
  }

  /**
   * @param string $domain
   * @param string $domain_config
   */
  private function validateRequiredCriteria($domain, $domain_config) {
    $required_criteria_config = $domain_config->requiredCriteria;
    if (!isset($required_criteria_config)) {
      return;
    }

    foreach ($required_criteria_config as $required_criteria) {
      $value = $this->criteria[$required_criteria->criteriaLevel][$required_criteria->name];
      if (!isset($value)) {
        $this->addError(1000, array('@paramName' => $required_criteria->name));
      }
      else {
        if (isset($required_criteria->allowedValues) && !in_array($value, $required_criteria->allowedValues)) {
          $this->addError(1001, array(
            '@value' => $value,
            '@paramName' => $required_criteria->name,
            '@validValues' => implode(',', $required_criteria->allowedValues),
          ));
        }
      }
    }
  }

  /**
   * @param $domain
   * @param $domain_config
   */
  private function validateDomainLevelCriteria($domain, $domain_config) {
    $config_key = $this->getConfigKey();

    $domain_validators_configuration = $domain_config->validators;
    if (!isset($domain_validators_configuration)) {
      return;
    }

    // If this gets more complex then move each handler validation to respective methods.
    foreach ($domain_validators_configuration as $validator) {
      if (isset($validator->configKey) && !in_array($config_key, $validator->configKey)) {
        // Validator not applicable to this.
        continue;
      }

      switch ($validator->name) {

        // Only one of the filters must/can be provided.
        case "optionalSingleFilter":
          $validator_config = $validator->config;
          switch ($validator_config->validatorType) {
            case "filterName":
              if (is_array($validator_config->criteriaLevel)) {
                foreach ($validator_config->criteriaLevel as $level) {
                  if (isset($criteria_filter_names) && isset($this->criteria[$level])) {
                    $criteria_filter_names = array_merge($criteria_filter_names, (array) array_keys($this->criteria[$level]));
                  }
                  else {
                    if (isset($this->criteria[$level])) {
                      $criteria_filter_names = array_keys($this->criteria[$level]);
                    }
                  }
                }
              }
              else {
                if (isset($this->criteria[$validator_config->criteriaLevel])) {
                  $criteria_filter_names = array_keys($this->criteria[$validator_config->criteriaLevel]);
                }
              }

              $validator_type_config = $validator_config->validatorTypeConfig;
              $filter_names = $validator_type_config->filterNames;
              if (!isset($criteria_filter_names) && $validator_type_config->required) {
                $this->addError(1000, array('@paramName' => implode(' or ', $filter_names)));
              }
              else {
                if (isset($criteria_filter_names)) {
                  $provided_filter_names = array_intersect($criteria_filter_names, $filter_names);
                  if ($validator_type_config->required && (!isset($provided_filter_names) || empty($provided_filter_names))) {
                    $this->addError(1000, array('@paramName' => implode(' or ', $filter_names)));
                  }
                  else {
                    if ((is_array($provided_filter_names) && count($provided_filter_names) > 1)) {
                      $this->addError(1112, array(
                        '@parameterNames' => implode(',', $provided_filter_names),
                        '@possibleParameters' => implode(',', $filter_names),
                      ));
                    }
                  }
                }
              }

              break;
          }
          break;

        // Required filter validation
        /*case "requiredFilter":
        $validatorConfig = $validator->config;
        switch($validatorConfig->validatorType){
        case "paramValueList":
        $validatorTypeConfig = $validatorConfig->validatorTypeConfig;
        $paramValue = $this->criteria[$validatorConfig->criteriaLevel][$validatorConfig->filterName];
        $allowedValues = $validatorTypeConfig->allowedValues;
        if(!isset($paramValue)){
        $this->addError(1000, array('@paramName' => $validatorConfig->filterName));
        }else if(!in_array($paramValue,$allowedValues)){
        $this->addError(1001, array('@value' => $paramValue, '@paramName' => $validatorConfig->filterName,'@validValues' => implode(',',$allowedValues)));
        }
        break;
        }
        break;
        */
      }
    }
  }

  /**
   * @param $domain
   * @param $domain_config
   */
  private function validateDomainCriteria($domain, $domain_config) {
    // Validate domain specific required parameters.
    $this->validateRequiredCriteria($domain, $domain_config);
    if ($this->hasErrors()) {
      return;
    }

    $this->validateDomainLevelCriteria($domain, $domain_config);
    if ($this->hasErrors()) {
      return;
    }

    $config_key = $this->getConfigKey();
    $domain_criteria_config = json_decode(json_encode($domain_config->$config_key->requestParameters), TRUE);
    $allowed_parameters = array_keys($domain_criteria_config);
    $special_chars = str_split(ValidatorConfig::$specialChars);
    $domain_title = $this->getDomainTitle();
    // Validate value criteria.
    if (isset($this->criteria['value'])) {
      foreach ($this->criteria['value'] as $name => &$value) {
        $value_parameter_config = $domain_criteria_config[$name];
        // Check for allowed parameters.
        if (!in_array($name, $allowed_parameters)) {
          $this->addError(1101, array(
            '@paramName' => $name,
            '@domain' => $domain_title,
            '@validValues' => implode(',', $allowed_parameters),
          ));
          continue;
        }

        // Since some value is provided.
        $value = trim($value);
        if (strlen($value) == 0) {
          $this->addError(1102, array('@paramName' => $name));
          continue;
        }
        else {
          if (isset($value_parameter_config['maxLength']) && strlen($value) > $value_parameter_config['maxLength']) {
            $this->addError(1108, array(
              '@paramValue' => $value,
              '@paramName' => $name,
              '@maxAllowedCharacters' => $value_parameter_config['maxLength'],
            ));
            continue;
          }
          else {
            if (isset($value_parameter_config['allowedValues'])) {
              if (isset($value) && !in_array($value, $value_parameter_config['allowedValues'])) {
                $this->addError(1001, array(
                  '@value' => $value,
                  '@paramName' => $name,
                  '@validValues' => implode(',', $value_parameter_config['allowedValues']),
                ));
                continue;
              }
              else {
                if (isset($value_parameter_config['paramMap'])) {
                  $mapped_value = $value_parameter_config['paramMap'][$value];
                  if (isset($mapped_value)) {
                    $value = explode('~', $mapped_value);
                  }
                }
              }
            }
          }
        }

        // Check for valid value.
        $data_type = $domain_criteria_config[$name]['dataType'];
        if (!$this->isValidData($data_type, $value)) {
          $this->addError(1103, array(
            '@paramValue' => $value,
            '@paramName' => $name,
            '@dataType' => $data_type,
          ));
        }
        else {
          if ($data_type == 'text' && $this->hasSpecialCharacters($value, $special_chars)) {
            if(!in_array($name,ValidatorConfig::$allow_special_chars_params)){
                $this->addError(1111, array('@paramName' => $name, '@paramValue' => $value));
            }
          }
        }
      }
    }

    // Validate range criteria.
    if (isset($this->criteria['range'])) {
      foreach ($this->criteria['range'] as $name => &$value) {
        $range_parameter_config = $domain_criteria_config[$name];
        $lower_limit = $value[0];
        $upper_limit = $value[1];

        if (!in_array($name, $allowed_parameters)) {
          // Check for allowed parameters.
          $this->addError(1101, array(
            '@paramName' => $name,
            '@domain' => $domain_title,
            '@validValues' => implode(',', $allowed_parameters),
          ));
          continue;
        }
        else {
          if ($range_parameter_config['valueType'] != 'range') {
            // Check if range is allowed for this parameter.
            $this->addError(1110, array('@paramName' => $name));
            continue;
          }
          else {
            if (strlen(trim($lower_limit)) == 0 && strlen(trim($upper_limit)) == 0) {
              // Non-zero length values.
              $this->addError(1102, array('@paramName' => $name));
              continue;
            }
          }
        }

        // Since some value is provided.
        $value[0] = isset($value[0]) ? trim($value[0]) : $value[0];
        $value[1] = isset($value[1]) ? trim($value[1]) : $value[1];

        $param_errors = array();
        if (isset($range_parameter_config['maxLength']) && strlen($value[0]) > $range_parameter_config['maxLength']) {
          $this->addError(1109, array(
            '@limit' => 'start',
            '@paramValue' => $value[0],
            '@paramName' => $name,
            '@maxAllowedCharacters' => $range_parameter_config['maxLength'],
          ));
        }
        if (isset($range_parameter_config['maxLength']) && strlen($value[1]) > $range_parameter_config['maxLength']) {
          $this->addError(1109, array(
            '@limit' => 'end',
            '@paramValue' => $value[1],
            '@paramName' => $name,
            '@maxAllowedCharacters' => $range_parameter_config['maxLength'],
          ));
        }
        if (!empty($param_errors)) {
          $this->addErrors($param_errors);
          continue;
        }

        $param_errors = array();
        // Check for valid range values.
        $data_type = $domain_criteria_config[$name]['dataType'];
        if (isset($value[0]) && !$this->isValidData($data_type, $value[0])) {
          $param_errors[1104][] = t(Messages::$message[1104], array(
            '@limit' => 'start',
            '@paramValue' => $value[0],
            '@paramName' => $name,
            '@dataType' => $data_type,
          ));
        }
        if (isset($value[1]) && !$this->isValidData($data_type, $value[1])) {
          $param_errors[1104][] = t(Messages::$message[1104], array(
            '@limit' => 'end',
            '@paramValue' => $value[1],
            '@paramName' => $name,
            '@dataType' => $data_type,
          ));
        }

        if (!empty($param_errors)) {
          $this->addErrors($param_errors);
          continue;
        }

        if (isset($value[0]) && isset($value[1]) && !$this->isValidRangeData($data_type, $value[0], $value[1])) {
          $this->addError(1105, array(
            '@starLimit' => 'start',
            '@startValue' => $value[0],
            '@endLimit' => 'end',
            '@endValue' => $value[1],
            '@paramName' => $name,
          ));
        }
      }
    }

    // Validate response Columns.
    if (isset($this->criteria['responseColumns'])) {
      $response_format = $this->criteria['global']['response_format'];
      $allowed_response_parameters = array_keys(get_object_vars($domain_config->$config_key->dataset->displayConfiguration->$response_format->elementsColumn));
      foreach ($this->criteria['responseColumns'] as $response_column) {
        if (!in_array($response_column, $allowed_response_parameters)) {
          $this->addError(1106, array(
            '@responseColumn' => $response_column,
            '@domain' => $domain_title,
            '@validValues' => implode(',', $allowed_response_parameters),
          ));
        }
      }
    }

  }

  /**
   * @param $data_type
   * @param $value
   * @return bool
   */
  private function isValidData($data_type, $value) {
    $is_valid_value = FALSE;
    switch ($data_type) {
      case "year":
        $is_valid_value = (is_numeric($value) && is_int($value + 0) && strlen($value) == 4);
        break;

      case "integer":
        $is_valid_value = (is_numeric($value) && is_int($value + 0));
        break;

      case "text":
        $is_valid_value = strlen($value) > 0;
        break;

      case "amount":
        $is_valid_value = is_numeric($value);
        break;

      case "date":
        $is_valid_value = $this->validDateFormat($value);
        break;

      case "list":
        // No need to validate these. These are validated in calling function.
        $is_valid_value = TRUE;
        break;
    }

    return $is_valid_value;
  }

  /**
   * @param $data
   * @param $special_chars
   * @return bool
   */
  private function hasSpecialCharacters($data, $special_chars) {
    $characters = str_split($data);
    foreach ($characters as $character) {
      if (in_array($character, $special_chars)) {
        return TRUE;
      }
    }
    return FALSE;
  }

  /**
   * @param $data_type
   * @param $lower_limit
   * @param $upper_limit
   * @return bool
   */
  private function isValidRangeData($data_type, $lower_limit, $upper_limit) {
    $is_valid_value = FALSE;
    switch ($data_type) {
      case "year":
      case "integer":
      case "amount":
        $is_valid_value = $lower_limit <= $upper_limit;
        break;

      case "text":
        $is_valid_value = TRUE;
        break;

      case "date":
        $lower_limit_date = strtotime($lower_limit);
        $upper_limit_date = strtotime($upper_limit);
        $is_valid_value = $lower_limit_date <= $upper_limit_date;
        break;
    }

    return $is_valid_value;
  }

  /**
   * @param $error_code
   * @param $values
   */
  function addError($error_code, $values) {
    if (isset(Messages::$message[$error_code])) {
      $this->errors[$error_code][] = t(Messages::$message[$error_code], $values);
    }
  }

  /**
   * @param $errors
   */
  function addErrors($errors) {
    $this->errors += $errors;
  }

  /**
   * @return bool
   */
  function hasErrors() {
    return count($this->errors) > 0;
  }

  /**
   * @return array
   */
  function getErrors() {
    return $this->errors;
  }

  /**
   * @param $message_code
   * @param $values
   */
  function addMessage($message_code, $values) {
    if (isset(Messages::$message[$message_code])) {
      $this->messages[$message_code][] = t(Messages::$message[$message_code], $values);
    }
  }

  /**
   * @return bool
   */
  function hasMessages() {
    return count($this->messages) > 0;
  }

  /**
   * @return array
   */
  function getMessages() {
    return $this->messages;
  }

  /**
   * @param $date
   * @return bool
   */
  function validDateFormat($date) {
    // $converted=str_replace('/','-',$date);
    if (preg_match("/^((((19|20)(([02468][048])|([13579][26]))-02-29))|((20[0-9][0-9])|(19[0-9][0-9]))-((((0[1-9])|(1[0-2]))-((0[1-9])|(1\d)|(2[0-8])))|((((0[13578])|(1[02]))-31)|(((0[1,3-9])|(1[0-2]))-(29|30)))))$/", $date) === 1) {
      return TRUE;
    }

    return FALSE;
  }

  /**
   * @return string
   */
  function getConfigKey() {
    $domain = strtolower($this->criteria['global']['type_of_data']);

    $config_key = $domain;
    switch ($config_key) {
      case "payroll":
      case "payroll_nycha":
        if (isset($this->criteria['value']['fiscal_year'])) {
          $config_key .= "_fiscal_year";
        }
        else {
          if (isset($this->criteria['value']['calendar_year'])) {
            $config_key .= "_calendar_year";
          }
          else {
            $config_key .= "_fiscal_year";
          }
         }
        break;

      case "contracts":
          $category = $this->criteria['value']['category'];
          $status = $this->criteria['value']['status'];

          if ($status == 'active' || $status == 'registered') {
            $config_key .= "_active_{$category}";
            if (!isset($this->criteria['value']['fiscal_year'])) {
              $config_key .= "_all_years";
            }
          }
          else {
            if ($status == 'pending') {
              $config_key .= "_{$status}";
            }
            else {
              $config_key .= "_{$status}_{$category}";
            }
          }
          break;
      case "spending":
      case "spending_oge":
          if($this->criteria['value']['spending_category'] == 'ts'){
              unset($this->criteria['value']['spending_category']);
          }
          break;
      case "contracts_oge":
        $category = $this->criteria['value']['category'];
        $status = $this->criteria['value']['status'];
        $this->criteria['value']['is_vendor_flag'] = (isset($this->criteria['value']['prime_vendor'])) ? "Y" : "N";

        if(isset($this->criteria['value']['fiscal_year']) || isset($this->criteria['value']['year'])){
            $this->criteria['value']['if_for_all_years'] = "N";
        }else{
            $this->criteria['value']['if_for_all_years'] = "Y";
            $this->criteria['value']['latest_flag'] = "Y";
        }

        if ($status == 'active' || $status == 'registered') {
          $config_key .= "_active_{$category}";
          if (!isset($this->criteria['value']['fiscal_year'])) {
            $config_key .= "_all_years";
          }
        }
        break;
        case "contracts_nycha":
            if (!isset($this->criteria['value']['fiscal_year'])) {
                $config_key .= "_all_years";
            }
        break;

      default:
        break;
    }

    return $config_key;
  }

  /**
   * @return string
   */
  function getDomainTitle() {
    $domain = strtolower($this->criteria['global']['type_of_data']);

    switch ($domain) {
      case "budget":
        $domain .= '(' . strtolower($this->criteria['value']['budget_type']) . ')';
        break;

      case "contracts":
      case "contracts_oge":
        $category = $this->criteria['value']['category'];
        $status = $this->criteria['value']['status'];

        $domain = "$status $domain($category)";
        if (!isset($this->criteria['value']['fiscal_year'])
          && ($status != 'pending')
        ) {
          $domain .= " All Years";
        }
        break;

      default:
        break;
    }

    return ucwords($domain);
  }

  /**
   * @abstract
   * @return mixed
   */
  abstract function getRequest();

  /**
   * @abstract
   * @return mixed
   */
  abstract function getMaxAllowedTransactionResults();
}
