<?php
/**
 * This file is part of the Checkbook NYC financial transparency software.
 *
 * Copyright (c) 2012 – 2023 New York City
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/*
 * Redirects to the search results page upon submitting the search form
 *
*/

namespace Drupal\checkbook_solr;

use Drupal\checkbook_log\LogHelper;
use Exception;
use stdClass;

/**
 * Class CheckbookSolr
 */
class CheckbookSolr {

  /**
   * @var string
   */
  private $solr_url = '';

  /**
   * @var
   */
  private static $smart_search_config_json;

  /**
   * @var
   */
  private static $auto_complete_config_json = 'autocomplete';

  /**
   * @var
   */
  private static $parameter_config_json = 'parameter';

  /**
   * @var string
   */
  private $datasource;

  /**
   * @param string $dataSource
   *
   * @return mixed
   */
  public static function getFacetConfigByDatasource(string $solr_datasource) {
    return (self::getSmartSearchConfig($solr_datasource)->facets) ? self::getSmartSearchConfig($solr_datasource)->facets : new StdClass();
  }

  /**
   * @param string $dataSource
   *
   * @return string
   */
  public static function getSortConfig(string $solr_datasource) {
    return (self::getSmartSearchConfig($solr_datasource)->sort->sort_by) ? self::getSmartSearchConfig($solr_datasource)->sort->sort_by : '';
  }

  /**
   * @return mixed
   */
  public static function getParamMapping() {
    return (self::getSmartSearchConfig(self::$parameter_config_json)->param_mapping) ? self::getSmartSearchConfig(self::$parameter_config_json)->param_mapping : new StdClass();
  }

  /**
   * @param string $solr_datasource
   * @param string $domain
   *
   * @return mixed
   */
  public static function getSearchFields(string $solr_datasource, string $domain) {
    return (self::getSmartSearchConfig($solr_datasource)->search_results_fields->$domain) ? self::getSmartSearchConfig($solr_datasource)->search_results_fields->$domain : new StdClass();
  }

  /**
   * @param string $solr_datasource
   * @param string $domain
   *
   * @return mixed
   */
  public static function getExportFields(string $solr_datasource, string $domain) {
    return (self::getSmartSearchConfig($solr_datasource)->export_fields->$domain) ? self::getSmartSearchConfig($solr_datasource)->export_fields->$domain : new StdClass();
  }

  /**
   * @param string $solr_datasource
   *
   * @return mixed
   */
  public static function getAutocompleteTerms(string $solr_datasource) {
    return (self::getSmartSearchConfig($solr_datasource)->autocomplete_terms) ? self::getSmartSearchConfig($solr_datasource)->autocomplete_terms : new StdClass();
  }

  /**
   * @return mixed
   */
  public static function getAutocompleteMapping() {
    return (self::getSmartSearchConfig(self::$auto_complete_config_json)->autocomplete_mapping) ? self::getSmartSearchConfig(self::$auto_complete_config_json)->autocomplete_mapping : new StdClass();
  }

  /**
   * @return object
   */
  private static function getSmartSearchConfig($config) {
    switch ($config) {
      case 'checkbook':
        $config = 'citywide';
        break;
      case 'checkbook_oge':
        $config = 'edc';
        break;
      case 'checkbook_nycha':
        $config = 'nycha';
        break;
    }
    $facet_json_config = file_get_contents(__DIR__ . '/Config/' . $config . '.json');
    $config = json_decode($facet_json_config);

    if (is_null($config)) {
      LogHelper::log_warn('Could not json decode ' . $config . '.json because: ' . json_last_error_msg());
      return new StdClass();
    }
    return self::$smart_search_config_json = $config;
  }

  /**
   * @var CheckbookSolr
   */
  public static $instance;

  /**
   * @param string $datasource
   *
   * @return CheckbookSolr
   */
  public static function getInstance(string $datasource) {
    if (self::$instance === NULL || ($datasource !== self::$instance->datasource)) {
      self::$instance = new self($datasource);
    }
    return self::$instance;
  }

  /**
   * CheckbookSolr constructor.
   *
   * @param string $datasource
   */
  public function __construct(string $datasource) {
    global $config;
    $this->datasource = $datasource;

    switch ($datasource) {
      case 'edc':
      case 'checkbook_oge':
      case 'checkbook_nycedc':
        $solr_datasource = 'solr_edc';
        break;
      case 'nycha':
      case 'checkbook_nycha':
        $solr_datasource = 'solr_nycha';
        break;
      case 'citywide':
      case 'checkbook':
      default:
        $solr_datasource = 'solr';
    }

    if ($config['check_book'][$solr_datasource]['url']) {
      $this->solr_url = $config['check_book'][$solr_datasource]['url'];
    }
    else {
      LogHelper::log_warn("Could not find config \$config['check_book']['$solr_datasource']['url']");
    }

    self::$instance = $this;
  }

  /**
   * @param string $query
   *
   * @return string
   */
  public function raw_query(string $query) {
    $result = '';

    if (!$this->solr_url) {
      return $result;
    }

    $url = $this->solr_url . $query;

    if ($cached = $this->cached($query)) {
      LogHelper::log_notice("Cache hit: " . $url);
      return $cached;
    }

    ini_set('default_socket_timeout', 600);
    LogHelper::log_notice("Getting solr: " . $url);
    try {
      $contents = file_get_contents($url);
      if ($contents && strlen($contents) < 100000) {
        $this->cache($query, $contents);
      }
    } catch (Exception $ex) {
      LogHelper::log_warn("Solr error: " . $ex->getMessage());
      $contents = '';
    }
    return $contents;
  }

  /**
   * @param string $query
   *
   * @return array
   */
  public function request_phps(string $query) {
    $response = $this->raw_query($query);
    $results = unserialize($response);

    if (!is_array($results)) {
      return [];
    }

    return $this->filterFacetValues($results);
  }

  /**
   * NYCCHKBK-9261
   *
   * @param array $results
   *
   * @return array
   */
  private function filterFacetValues(array $results) {
    if ($results['facet_counts']['facet_fields']) {
      $fcff = $results['facet_counts']['facet_fields'];
      foreach ($fcff as $k => $values) {

        if(array_key_exists('-', $values)){
          unset($values['-']);
        }
        if (!sizeof($values)) {
          unset($results['facet_counts']['facet_fields'][$k]);
        }
        else {
          $results['facet_counts']['facet_fields'][$k] = $values;
        }
      }
    }
    return $results;
  }

  /**
   * @param CheckbookSolrQuery $query
   *
   * @return array
   */
  public function requestTerms(CheckbookSolrQuery $query) {
    $results = [];
    $q = 'terms?' . $query->buildQuery();
    $response = $this->raw_query($q);
    if ('phps' == $query->getWt()) {
      $results = unserialize($response);
    }

    if (!is_array($results)) {
      return [];
    }

    return $results;
  }

  /**
   * @param string $query
   *
   * @return string
   */
  public function request_csv(string $query) {
    return $this->raw_query($query);
  }

  private function cache_key(string $query) {
    return '_solr_' . $this->datasource . '_' . md5($query);
  }

  private function cache(string $query, $value) {
    $cacheKey = $this->cache_key($query);
    LogHelper::log_info("Set Cached data in CheckbookSolr.php with cachekey " . $cacheKey);
    _checkbook_dmemcache_set($cacheKey, $value);
  }

  private function cached(string $query) {
    $cacheKey = $this->cache_key($query);
    $cached = _checkbook_dmemcache_get($cacheKey);
    if ($cached) {
      LogHelper::log_info("Get Cached data in CheckbookSolr.php with cachekey " . $cacheKey);
      return $cached;
    }
    return FALSE;
  }

}
