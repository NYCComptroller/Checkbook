<?php
/*
 * Redirects to the search results page upon submitting the search form
 *
*/

/**
 * Class CheckbookSolr
 */
class CheckbookSolr
{

  /**
   * @var string
   */
  private $solr_url = '';

  /**
   * @var
   */
  private static $smart_search_config_json;

  /**
   * @var string
   */
  private $datasource;

  /**
   * @param string $domain
   * @return mixed
   */
  public static function getFacetConfigByDatasource(string $domain): object
  {
    return self::getSmartSearchConfig()->facets->$domain ?? new StdClass();
  }

  /**
   * @param string $domain
   * @return string
   */
  public static function getSortConfig(string $domain): string
  {
    return self::getSmartSearchConfig()->sort_by->$domain ?? '';
  }

  /**
   * @return mixed
   */
  public static function getParamMapping(): object
  {
    return self::getSmartSearchConfig()->param_mapping ?? new StdClass();
  }

  /**
   * @param string $solr_datasource
   * @param string $domain
   * @return mixed
   */
  public static function getSearchFields(string $solr_datasource, string $domain): object
  {
    return self::getSmartSearchConfig()->search_results_fields->$solr_datasource->$domain ?? new StdClass();
  }

  /**
   * @param string $solr_datasource
   * @param string $domain
   * @return mixed
   */
  public static function getExportFields(string $solr_datasource, string $domain): object
  {
    return self::getSmartSearchConfig()->export_fields->$solr_datasource->$domain ?? new StdClass();
  }

  /**
   * @param string $solr_datasource
   * @return mixed
   */
  public static function getAutocompleteTerms(string $solr_datasource): object
  {
    return self::getSmartSearchConfig()->autocomplete_terms->$solr_datasource ?? new StdClass();
  }

  /**
   * @return mixed
   */
  public static function getAutocompleteMapping(): object
  {
    return self::getSmartSearchConfig()->autocomplete_mapping ?? new StdClass();
  }

  /**
   * @return object
   */
  private static function getSmartSearchConfig(): object
  {
    if (isset(self::$smart_search_config_json)) {
      return self::$smart_search_config_json;
    }

    $facet_json_config = file_get_contents(__DIR__ . '/../config/smart_search_config.json');
    $config = json_decode($facet_json_config);

    if (is_null($config)) {
      LogHelper::log_warn('Could not json decode smart_search_config.json because: ' . json_last_error_msg());
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
   * @return CheckbookSolr
   */
  public static function getInstance(string $datasource)
  {
    if (self::$instance === null || ($datasource !== self::$instance->datasource)) {
      self::$instance = new self($datasource);
    }
    return self::$instance;
  }

  /**
   * CheckbookSolr constructor.
   * @param string $datasource
   */
  public function __construct(string $datasource)
  {
    global $conf;

    $this->datasource = $datasource;

    switch ($datasource) {
      case 'edc':
      case 'checkbook_oge':
        $solr_datasource = 'solr_edc';
        break;
      case 'nycha':
        $solr_datasource = 'solr_nycha';
        break;
      case 'citywide':
      case 'checkbook':
      default:
        $solr_datasource = 'solr';
    }

    if ($conf['check_book'][$solr_datasource]['url']) {
      $this->solr_url = $conf['check_book'][$solr_datasource]['url'];
    } else {
      LogHelper::log_warn("Could not find config \$conf['check_book']['$solr_datasource']['url']");
    }

    self::$instance = $this;
  }

  /**
   * @param string $query
   * @return string
   */
  public function raw_query(string $query): string
  {
    $result = '';

    if (!$this->solr_url) {
      return $result;
    }

    ini_set('default_socket_timeout', 120);
    $url = $this->solr_url . $query;
    LogHelper::log_notice("Getting solr: " . $url);
    try {
      $contents = file_get_contents($url);
    } catch (Exception $ex) {
      LogHelper::log_warn("Solr error: " . $ex->getMessage());
      $contents = '';
    }
    return $contents;
  }

  /**
   * @param string $query
   * @return array
   */
  public function request_phps(string $query): array
  {
    $response = $this->raw_query($query);
    $results = unserialize($response);

    if (!is_array($results)) {
      return [];
    }

    return $this->filterFacetValues($results);
  }

  /**
   * NYCCHKBK-9261
   * @param array $results
   * @return array
   */
  private function filterFacetValues(array $results): array
  {
    if ($results['facet_counts']['facet_fields'] ?? false) {
      $fcff = $results['facet_counts']['facet_fields'];
      foreach ($fcff as $k => $values) {
        if ($values['-'] ?? false) {
          unset($values['-']);
        }
        if (!sizeof($values)) {
          unset($results['facet_counts']['facet_fields'][$k]);
        } else {
          $results['facet_counts']['facet_fields'][$k] = $values;
        }
      }
    }
    return $results;
  }

  /**
   * @param CheckbookSolrQuery $query
   * @return array
   */
  public function requestTerms(CheckbookSolrQuery $query): array
  {
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
   * @return string
   */
  public function request_csv(string $query): string
  {
    return $this->raw_query($query);
  }
}
