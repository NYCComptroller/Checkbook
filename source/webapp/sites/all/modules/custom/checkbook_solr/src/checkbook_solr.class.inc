<?php
/*
 * Redirects to the search results page upon submitting the search form
 *
*/

/**
 * Class Checkbook_Solr
 */
class Checkbook_Solr
{

  /**
   * @var string
   */
  private $solr_url = '';

  /**
   * Checkbook_Solr constructor.
   * @param string $datasource
   */
  public function __construct(string $datasource = 'citywide')
  {
    global $conf;

    switch ($datasource) {
      case 'oge':
      case 'edc':
        $solr_datasource = 'solr_edc';
        break;
      case 'nycha':
        $solr_datasource = 'solr_nycha';
        break;
      case 'citywide':
      default:
        $solr_datasource = 'solr';
    }

    if ($conf['check_book'][$solr_datasource]['url']) {
      $this->solr_url = $conf['check_book'][$solr_datasource]['url'];
    } else {
      LogHelper::log_warn("Could not find config \$conf['check_book']['$solr_datasource']['url']");
    }
  }

  /**
   * @param string $string
   * @return string
   */
  public static function escape(string $string): string
  {
    /**
     * DO NOT CHANGE ORDER, BACKSLASH MUST BE FIRST
     */
    $escape_chars = explode(' ', '\\ + - & | ! ( ) { } [ ] ^ " ~ * ? : /');
    $replace_to = [];
    foreach($escape_chars as $char) {
      $replace_to[] = "\\".$char;
    }
    $return = str_replace($escape_chars, $replace_to, $string);
    $return = urlencode(strtolower($return));

    return $return;
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
    try{
      $contents = file_get_contents($url);
    } catch (Exception $ex) {
      LogHelper::log_warn("Solr error: ".$ex->getMessage());
      $contents='';
    }
    $result = utf8_encode($contents);

    return $result;
  }

  /**
   * @param string $query
   * @return array
   */
  public function request_phps(string $query): array
  {
    $results = [];
    $response = $this->raw_query($query);
    if (stristr($query, 'wt=phps')) {
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
