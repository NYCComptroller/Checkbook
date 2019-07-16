<?php

include_once CUSTOM_MODULES_DIR . '/checkbook_solr/src/checkbook_solr_query.class.inc';
include_once CUSTOM_MODULES_DIR . '/checkbook_solr/src/checkbook_solr.class.inc';

/**
 * Class CheckbookApiModuleTest
 */
class CheckbookSolrQueryTest extends \PHPUnit\Framework\TestCase
{
    /**
     *
     */
    public function test_json_valid()
    {
        $facets = CheckbookSolr::getAutocompleteMapping();
        $this->assertEquals('agency_name_autocomplete', $facets->agency_name);
    }

    /**
     *
     */
    public function test_constructor()
    {
        $searchTerms = 'rei';
        $query = new CheckbookSolrQuery($searchTerms, 999, 7);
        $start = 999 * 7;
        $this->assertEquals('q=text:rei*&start=' . $start . '&rows=999&wt=phps', $query->buildQuery());

        $searchTerms = 'rei*!*vendor_names=rei%2Bsystems%252C%2Binc.';
        $query = new CheckbookSolrQuery($searchTerms, 9);
        $this->assertEquals('q=text:rei*&fq=vendor_name:"rei%5C%2Bsystems%252c%5C%2Binc."&start=0&rows=9&wt=phps',
            $query->buildQuery());
    }

    /**
     *
     */
    public function test_sort()
    {
        $query = new CheckbookSolrQuery();
        $query->setSort('vendor_name asc, original_amount desc');
        $this->assertEquals('q=*:*&start=0&rows=0&sort=vendor_name+asc%2C+original_amount+desc&wt=phps',
            $query->buildQuery());
    }

    /**
     *
     */
    public function testFq()
    {
        $query = new CheckbookSolrQuery();
        $query->setFq('vendor_name', 'vendor_name:rei systems');
        $this->assertEquals('q=*:*&fq=vendor_name:rei systems&start=0&rows=0&wt=phps', $query->buildQuery());
    }

    /**
     *
     */
    public function testPagination()
    {
        $query = new CheckbookSolrQuery();
        $query
            ->setRows(9)
            ->setPage(7);

        $this->assertEquals('q=*:*&start=63&rows=9&wt=phps', $query->buildQuery());
    }

}
