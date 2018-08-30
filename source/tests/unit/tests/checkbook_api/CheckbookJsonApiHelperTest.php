<?php

include_once CUSTOM_MODULES_DIR . '/checkbook_api/json_api/CheckBookJsonApiHelper.php';

use PHPUnit\Framework\TestCase;
use \checkbook_json_api\CheckBookJsonApiHelper;

/**
 * Class CheckbookJsonApiHelperTest
 */
class CheckbookJsonApiHelperTest extends TestCase
{
    /**
     * @var
     */
    public $api;

    /**
     * @var CheckBookJsonApiHelper
     */
    public $helper;

    /**
     *
     */
    public function setUp()
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->api = new CheckBookJsonApiStub();
        $this->api->Model = new CheckBookJsonModelStub();
        $this->helper = new CheckBookJsonApiHelper($this->api);
    }

    /**
     *
     */
    public function test_validate_year_empty()
    {
        $year = $this->helper->validate_year([]);
        $this->assertEquals(date('Y'), $year);
        $this->assertTrue($this->api->success);
        $this->assertEquals('', $this->api->message);
    }

    /**
     *
     */
    public function test_validate_year_invalid_string()
    {
        $year = $this->helper->validate_year(['', 'invalid']);
        $this->assertFalse($year);
        $this->assertFalse($this->api->success);
        $this->assertEquals('invalid year', $this->api->message);
    }

    /**
     *
     */
    public function test_validate_year_future()
    {
        $year = $this->helper->validate_year(['', date('Y') + 5]);
        $this->assertFalse($year);
        $this->assertFalse($this->api->success);
        $this->assertEquals('invalid year', $this->api->message);
    }

    /**
     *
     */
    public function test_validate_year_success()
    {
        $year = $this->helper->validate_year(['', 2013]);
        $this->assertEquals(2013, $year);
        $this->assertTrue($this->api->success);
        $this->assertEquals('', $this->api->message);
    }

    /**
     *
     */
    public function test_get_verbal_year_type()
    {
        $this->assertEquals('calendar', $this->helper->get_verbal_year_type('C'));
        $this->assertEquals('fiscal', $this->helper->get_verbal_year_type('F'));
        $this->assertEquals('fiscal', $this->helper->get_verbal_year_type('A'));
        $this->assertEquals('fiscal', $this->helper->get_verbal_year_type('4'));
        $this->assertEquals('fiscal', $this->helper->get_verbal_year_type('B'));
    }

    /**
     *
     */
    public function test_validate_year_type()
    {
        $this->assertEquals('B', $this->helper->validate_year_type([]));
        $this->assertEquals('C', $this->helper->validate_year_type([0, 0, 'C']));
        $this->assertEquals('C', $this->helper->validate_year_type([0, 0, 'cAlEnDaR']));
        $this->assertEquals('B', $this->helper->validate_year_type([0, 0, 'B']));
        $this->assertEquals('B', $this->helper->validate_year_type([0, 0, 'fIsCaL']));
        $this->assertEquals('B', $this->helper->validate_year_type([0, 0, 'weirdo']));
        $this->assertEquals('C', $this->helper->validate_year_type([0, 0, 'weirdo'], 'C'));
    }

    /**
     *
     */
    public function test_get_prod_etl_status()
    {
        global $conf;
        $conf['etl-status-path'] = __DIR__ . '/files/';
        $expected = [
            'success' => true,
            'data' => 'great success',
            'message' => '',
            "invalid_records" => array_map('str_getcsv',
                file($conf['etl-status-path'] . 'invalid_records_details.csv')),
            'invalid_records_timestamp' => filemtime(
                $conf['etl-status-path'] . 'invalid_records_details.csv'),
            'audit_status' => ['OK'],
            'audit_status_timestamp' => filemtime(
                $conf['etl-status-path'] . 'audit_status.txt'),
            'match_status' => [
                'PMSSummary' => 'unknown',
                'COAExpenditureObject' => 'unknown',
                'COARevenueSource' => '7 days ago',
                'MAG' => '5 days ago',
                'Some Incredible Name' => 'unknown data source name',
            ],
            'match_status_timestamp' => filemtime(
                $conf['etl-status-path'] . 'file_data_statistics.csv'),
        ];
        $fakeToday = '2222-12-22';
        $this->helper->timeNow = strtotime($fakeToday);
        $this->helper->dataSourceLastSuccess = serialize([
            'MAG' => '2222-12-17',
            'COARevenueSource' => '2222-12-15',
        ]);
        $this->assertEquals($expected, $this->helper->getProdEtlStatus());
        $expected = [
            'COARevenueSource' => '2222-12-15',
            'MAG' => '2222-12-17',
            'PendingContracts' => $fakeToday,
            'PMS' => $fakeToday,
        ];
        $this->assertEquals($expected, unserialize($this->helper->dataSourceLastSuccess));
    }

    /**
     *
     */
    public function test_get_uat_etl_status()
    {
        $uat_status = $this->helper->getUatEtlStatus();
        $expected = [
            'success' => True,
            'data' => 'yesterday',
            'message' => '',
            'info' => 'Last successful ETL run date',
        ];
        $this->assertEquals($expected, $uat_status);
    }


    /**
     *
     */
    public function test_get_connections_info()
    {
        global $conf, $databases;
        $databases = [
            'default' => [
                'default' => [
                    'host' => 'def-def-host',
                    'database' => 'def-def-db',
                ]
            ],
            'checkbook' => [
                'main' => [
                    'host' => 'ckbk-main-host',
                    'database' => 'ckbk-main-db',
                ],
                'etl' => [
                    'host' => 'ckbk-etl-host',
                    'database' => 'ckbk-etl-db',
                ],
            ],
            'checkbook_oge' => [
                'main' => [
                    'host' => 'oge-main-host',
                    'database' => 'oge-main-db',
                ],
            ],
            'checkbook_nycha' => [
                'main' => [
                    'host' => 'nycha-main-host',
                    'database' => 'nycha-main-db',
                ],
            ],
        ];
        $conf = [
            'check_book' => [
                'solr' => [
                    'url' => 'http://solr-url/solr/solr-db/'
                ]
            ]
        ];
        $expected = [
            'mysql' => 'def-def-host|def-def-db',
            'psql_main' => 'ckbk-main-host|ckbk-main-db',
            'psql_etl' => 'ckbk-etl-host|ckbk-etl-db',
            'psql_oge' => 'oge-main-host|oge-main-db',
            'psql_nycha' => 'nycha-main-host|nycha-main-db',
            'solr' => 'http://solr-url/solr/|solr-db',
        ];
        $this->assertEquals($expected, $this->helper->get_connections_info());
    }
}

/**
 * Class CheckBookJsonApiStub
 */
class CheckBookJsonApiStub
{
    /**
     * @var bool
     */
    public $success = True;
    /**
     * @var string
     */
    public $message = '';

    /**
     * @var
     */
    public $Model;
}

/**
 * Class CheckBookJsonModelStub
 */
class CheckBookJsonModelStub
{
    /**
     * @return array
     */
    function get_etl_status()
    {
        return [
            [
                'last_successful_run' => 'yesterday'
            ]
        ];
    }
}
