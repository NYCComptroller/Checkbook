<?php
/*
 * Redirects to the search results page upon submitting the search form
 *
*/

/**
 * Class CheckbookSolrQuery
 */
class CheckbookSolrQuery
{

  /**
   * @var array
   */
  private $facets = [];

  /**
   * @var string
   */
  private $q = '*:*';

  /**
   * @var array
   */
  private $fq = [];

  /**
   * @var string
   */
  private $sort = '';

  /**
   * @var int
   */
  private $page = 0;

  /**
   * @var int
   */
  private $rows = 10;

  /**
   * @var string
   */
  private $wt = 'phps';

  /**
   * @var array
   */
  private $termFields = [];

  /**
   * @var string
   */
  private $termsRegex = '';

  /**
   * @var string
   */
  private $query = '';

  /**
   * @var array
   */
  private $selectedFacets = [];

  /**
   * @var array
   */
  private $paramMapping = [];

  /**
   * CheckbookSolrQuery constructor.
   * @param string $searchTerms
   * @param int $rows
   * @param int $page
   */
  public function __construct(string $searchTerms='', int $rows=0, int $page=0)
  {
    $this->paramMapping = (array)CheckbookSolr::getParamMapping();

    if ($searchTerms) {
      $this
        ->setSearchTerms(explode('*|*', $searchTerms));
    }

    $this
      ->setRows($rows)
      ->setPage($page);

    return $this;
  }


  /**
   * @param string $termsRegex
   * @return CheckbookSolrQuery
   */
  public function setTermsRegex(string $termsRegex): CheckbookSolrQuery
  {
    $this->termsRegex = $termsRegex;
    return $this;
  }

  /**
   * @param string $sort
   * @return CheckbookSolrQuery
   */
  public function setSort(string $sort): CheckbookSolrQuery
  {
    $this->sort = $sort;
    return $this;
  }

  /**
   * @param string $fq
   * @return CheckbookSolrQuery
   */
  public function addFq(string $fq): CheckbookSolrQuery
  {
    $this->fq[] = $fq;
    return $this;
  }

  /**
   * @return string
   */
  public function getQ(): string
  {
    return $this->q;
  }

  /**
   * @return string
   */
  public function getWt(): string
  {
    return $this->wt;
  }

  /**
   * @param string $q
   * @return CheckbookSolrQuery
   */
  public function setQ(string $q): CheckbookSolrQuery
  {
    $this->q = $q;
    return $this;
  }

  /**
   * @param int $page
   * @return CheckbookSolrQuery
   */
  public function setPage(int $page): CheckbookSolrQuery
  {
    $this->page = $page;
    return $this;
  }

  /**
   * @param int $num_rows
   * @return CheckbookSolrQuery
   */
  public function setRows(int $num_rows): CheckbookSolrQuery
  {
    $this->rows = $num_rows;
    return $this;
  }

  /**
   * @param string $wt
   * @return CheckbookSolrQuery
   */
  public function setWt(string $wt): CheckbookSolrQuery
  {
    $this->wt = $wt;
    return $this;
  }

  /**
   * @param string $termField
   * @return CheckbookSolrQuery
   */
  public function addTermField(string $termField): CheckbookSolrQuery
  {
    if (!in_array($termField, $this->termFields)) {
      $this->termFields[] = $termField;
    }
    return $this;
  }

  /**
   * @return string
   */
  public function buildQuery(): string
  {
    $this->query = '';

    $query = [
      'wt' => $this->wt,
    ];

    if (sizeof($this->termFields)) {
      $terms = '';
      foreach ($this->termFields as $termField) {
        $terms .= '&' . http_build_query(['terms.fl' => $termField]);
      }

      $query['terms.regex.flag'] = 'case_insensitive';
      $query['terms.regex'] = $this->termsRegex;

      $this->query = http_build_query($query) . $terms;
      return $this->query;
    }

    $query = array_merge($query, [
      'start' => $this->page * $this->rows,
      'rows' => $this->rows,
    ]);

    $fq = '';
    if (sizeof($this->fq)) {
      $fq = '&fq=' . join('&fq=', $this->fq);
    }

    $facets = '';
    if (sizeof($this->facets)) {
      $facets = '&' . http_build_query([
          'facet' => 'true',
          'facet.mincount' => 1,
          'facet.sort' => 'count',
          'facet.limit' => -1
        ]);
      foreach ($this->facets as $facet) {
        $facets .= '&' . http_build_query(['facet.field' => $facet]);
      }
    }

    $sort = '';
    if ($this->sort) {
      $query['sort'] = $this->sort;
    }

    $q = 'q=' . $this->q;

    /**
     * ex.
     * http://sdw5.reisys.com:18983/solr/checkbook_dev.public.solr_index_full/select/?q=*:*&facet=true&facet.sort=count&facet.field=fiscal_year&wt=phps&facet.mincount=1
     */

    $this->query = $q . $fq . $sort . $facets . '&' . http_build_query($query);

    return $this->query;
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
    foreach ($escape_chars as $char) {
      $replace_to[] = "\\" . $char;
    }
    $return = str_replace($escape_chars, $replace_to, $string);
    $return = urlencode(strtolower($return));

    return $return;
  }

  /**
   * @param array $searchTerms
   * @return CheckbookSolrQuery
   */
  public function setSearchTerms(array $searchTerms): CheckbookSolrQuery
  {
    $searchTerms[0] = urldecode($searchTerms[0]); //"Education"
    $searchTerms[0] = ($searchTerms[0] == "") ? "*:*" : ucwords(strtolower(CheckbookSolrQuery::escape($searchTerms[0])));

    $this->q = array_shift($searchTerms);

    if (!sizeof($searchTerms)) {
      return $this;
    }

    foreach ($searchTerms as $term) {
      $this->addFqTerm($term);
    }

    return $this;
  }

  /**
   * @param string $param
   */
  public function mapParam(string &$param): void
  {
    if (in_array($param, array_keys($this->paramMapping))) {
      $param = $this->paramMapping[$param];
    }
  }

  /**
   * @param string $term
   * @param bool $exclude
   * @return CheckbookSolrQuery
   */
  public function addFqTerm(string $term, bool $exclude = false): CheckbookSolrQuery
  {
    list($param, $value) = explode('=', urldecode($term));
    $this->mapParam($param);

    $values = explode('~', $value);
    $this->selectedFacets[$param] = $values;

    $fq = [];
    foreach ($values as $value) {
      if ('vendor_type' == $param) {
        $value = get_vendor_type_mapping($value);
      }
      if ('contract_status' == $param) {
        $value = get_contract_status_mapping($value);
      }
      $minus = $exclude ? '-' : '';
      $fq[] = $minus . $param . ':"' . self::escape($value) . '"';
    }

    if ($exclude) {
      $this->addFq(join('%20AND%20', $fq));
    } else {
      $this->addFq(join('%20OR%20', $fq));
    }

    return $this;
  }

  /**
   * @param array $facets
   * @return CheckbookSolrQuery
   */
  public function addFacets(array $facets): CheckbookSolrQuery
  {
    foreach ($facets as $facet) {
      $this->addFacet($facet);
    }
    return $this;
  }

  /**
   * @param string $facet
   * @return CheckbookSolrQuery
   */
  public function addFacet(string $facet): CheckbookSolrQuery
  {
    $this->mapParam($facet);

    if (!in_array($facet, $this->facets)) {
      $this->facets[] = $facet;
    }
    return $this;
  }

  /**
   * @return CheckbookSolrQuery
   */
  public function clearFacets(): CheckbookSolrQuery
  {
    $this->facets = [];
    return $this;
  }

  /**
   * @param string $facet
   * @return CheckbookSolrQuery
   */
  public function excludeFqTermValues(string $facet): CheckbookSolrQuery
  {
    $this->mapParam($facet);

    $fq = $this->fq;
    $this->fq = [];
    foreach ($fq as $fq_line) {
      if (2 === strpos("  $fq_line", "$facet:")) {
        $fq_line = str_replace("$facet:", "-$facet:", $fq_line);
      }
      $this->fq[] = $fq_line;
    }
    return $this;
  }

  /**
   * @param string $facet
   * @return CheckbookSolrQuery
   */
  public function removeFqTermValues(string $facet): CheckbookSolrQuery
  {
    $this->mapParam($facet);

    $fq = $this->fq;
    $this->fq = [];
    foreach ($fq as $fq_line) {
      if (2 !== strpos("  $fq_line", "$facet:")) {
        $this->fq[] = $fq_line;
      }
    }
    return $this;
  }

  /**
   * @return array
   */
  public function getSelectedFacets(): array
  {
    return $this->selectedFacets;
  }
}
