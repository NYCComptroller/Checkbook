<?php

include_once CUSTOM_MODULES_DIR . '/checkbook_etl_status/checkbook_etl_status.module';

use PHPUnit\Framework\TestCase;

/**
 * Class CheckbookEtlStatusModuleTest
 */
class CheckbookEtlStatusModuleTest extends TestCase
{

    /**
     * @var string
     */
    private $fakeToday = '2222-11-22 03:39:54.635115';
    /**
     * @var string
     */
    private $fakeTodayYMD = '2222-11-22';

    /**
     * @var int
     * 2222-11-22 08:01:00
     */
    private $fakeTodayTimestamp = 7980453594;
    /**
     * @var string
     */
    private $fakeYesterday = '2222-11-21 03:39:54.635115';

    /**
     * @var int
     */
    private $fakeYesterdayTime = 7980367194;

    /**
     * @var CheckbookEtlStatus
     */
    private $CES;

    /**
     *
     */
    public function setUp(): void
    {
        parent::setUp();
        CheckbookEtlStatus::$successSubject = 'Success';
        CheckbookEtlStatus::$message_body = '';
        $this->CES = new CheckbookEtlStatus();
    }

    /**
     *
     */
    public function test_checkbook_etl_status_cron_empty_recipients_list()
    {
        global $conf;
        $email = $conf['checkbook_dev_group_email'];
        unset($conf['checkbook_dev_group_email']);
        $this->assertFalse($this->CES->run_cron());
        $conf['checkbook_dev_group_email'] = $email;
    }

    /**
     *
     */
    public function test_checkbook_etl_status_cron_wrong_env()
    {
        global $conf;
        $env = $conf['CHECKBOOK_ENV'];
        $conf['CHECKBOOK_ENV'] = 'wrong';
        $this->assertFalse($this->CES->run_cron());
        $conf['CHECKBOOK_ENV'] = $env;
    }

    /**
     *
     */
    public function test_checkbook_etl_status_cron_already_ran()
    {
        global $mocked_variable;
        global $mocked_date;

        $CheckbookEtlStatus =
            $this->getMockBuilder('CheckbookEtlStatus')
                ->setMethods(['get_date'])
                ->getMock();
        $CheckbookEtlStatus->expects($this->at(0))
            ->method('get_date')
            ->with($this->equalTo('Y-m-d'))
            ->will($this->returnValue($this->fakeTodayYMD));

        $mocked_variable['checkbook_etl_status_last_run'] = $mocked_date['Y-m-d'] = $this->fakeTodayYMD;

        $this->assertFalse($CheckbookEtlStatus->run_cron());
    }

    /**
     *
     */
    public function test_checkbook_etl_status_cron_too_early()
    {
        global $mocked_variable;
        $mocked_variable['checkbook_etl_status_last_run'] = $this->fakeYesterday;
        $CheckbookEtlStatus =
            $this->getMockBuilder('CheckbookEtlStatus')
                ->setMethods(['get_date'])
                ->getMock();
        $CheckbookEtlStatus->expects($this->at(0))
            ->method('get_date')
            ->with($this->equalTo('Y-m-d'))
            ->will($this->returnValue($this->fakeTodayYMD));
        $CheckbookEtlStatus->expects($this->at(1))
            ->method('get_date')
            ->with($this->equalTo('H'))
            ->will($this->returnValue('5'));

        $this->assertFalse($CheckbookEtlStatus->run_cron());
    }

    /**
     *
     */
    public function test_checkbook_etl_status_cron_success()
    {
        global $mocked_variable;
        $mocked_variable['checkbook_etl_status_last_run'] = $this->fakeYesterday;
        $CheckbookEtlStatus =
            $this->getMockBuilder('CheckbookEtlStatus')
                ->setMethods(['get_date', 'sendmail'])
                ->getMock();
        $CheckbookEtlStatus->expects($this->at(0))
            ->method('get_date')
            ->with($this->equalTo('Y-m-d'))
            ->will($this->returnValue($this->fakeTodayYMD));
        $CheckbookEtlStatus->expects($this->at(1))
            ->method('get_date')
            ->with($this->equalTo('H'))
            ->will($this->returnValue('9'));
        $CheckbookEtlStatus->expects($this->once())
            ->method('sendmail')
            ->will($this->returnValue('777'));

        $this->assertEquals('777', $CheckbookEtlStatus->run_cron());
    }

    /**
     *
     */
    public function testNiceDisplayDateDiff()
    {
        $this->assertEquals('never', $this->CES->niceDisplayDateDiff(false));
    }

    /**
     *
     */
    public function test_format_status_success()
    {
        $CheckbookEtlStatus =
            $this
                ->getMockBuilder('CheckbookEtlStatus')
                ->setMethods(['timeNow'])
                ->getMock();

        $CheckbookEtlStatus
            ->expects($this->any())
            ->method('timeNow')
            ->will($this->returnValue($this->fakeTodayTimestamp - 7500));

        $sampleData = [
            'success' => true,
            'data' => $this->fakeToday
        ];

        $expected = [
            'success' => true,
            'data' => $this->fakeToday,
            'hint' => $CheckbookEtlStatus->niceDisplayDateDiff($this->fakeToday)
        ];

        $this->assertEquals($expected, $CheckbookEtlStatus->formatStatus($sampleData));
    }

    /**
     *
     */
    public function test_sendmail()
    {
        $this->assertTrue($this->CES->sendmail());
    }

    /**
     *
     */
    public function test_getProdStatus()
    {
        $CheckbookEtlStatus =
            $this->getMockBuilder('CheckbookEtlStatus')
                ->setMethods(['get_contents'])
                ->getMock();
        $CheckbookEtlStatus->expects($this->once())
            ->method('get_contents')
            ->will($this->returnValue('{"success":true}'));

        $expected = [
            'success' => true,
            'source' => 'PROD',
        ];

        $this->assertEquals($expected, $CheckbookEtlStatus->getProdStatus());

    }

    /**
     *
     */
    public function test_format_status_fail()
    {
        $CheckbookEtlStatus =
            $this->getMockBuilder('CheckbookEtlStatus')
                ->setMethods(['timeNow'])
                ->getMock();

        $CheckbookEtlStatus->expects($this->once())
            ->method('timeNow')
            ->will($this->returnValue($this->fakeTodayTimestamp));

        $sampleData = [
        ];

        $expected = [
            'success' => false,
            'hint' => 'Could not get data from server',
        ];

        $this->assertEquals($expected, $CheckbookEtlStatus->formatStatus($sampleData));
    }

    /**
     *
     */
    public function test_mail_success_uat()
    {
        $CheckbookEtlStatus =
            $this->getMockBuilder('CheckbookEtlStatus')
                ->setMethods(['timeNow', 'getUatStatus', 'getProdStatus', 'getConnectionConfigs'])
                ->getMock();

        $message = [];

        $CheckbookEtlStatus->expects($this->any())
            ->method('timeNow')
            ->will($this->returnValue($this->fakeTodayTimestamp));

        $CheckbookEtlStatus->expects($this->once())
            ->method('getUatStatus')
            ->will($this->returnValue([
                'success' => true,
                'data' => $this->fakeToday
            ]));

        $CheckbookEtlStatus->expects($this->once())
            ->method('getProdStatus')
            ->will($this->returnValue([
                'success' => true,
                'data' => $this->fakeYesterday
            ]));

        $CheckbookEtlStatus->gatherData($message);

        $expected = [
            'subject' => 'Fail',
            'uat_status' => [
                'success' => true,
                'data' => $this->fakeToday,
                'hint' => $CheckbookEtlStatus->niceDisplayDateDiff($this->fakeToday),
            ],
            'prod_status' => [
                'success' => false,
                'data' => $this->fakeYesterday,
                'hint' => 'Last success: ' . $CheckbookEtlStatus->niceDisplayDateDiff($this->fakeYesterday),
            ],
            'connections' => null,
            'connection_keys' => CheckbookEtlStatus::CONNECTIONS_KEYS,
            'fisa_files' => false,
        ];

        $this->assertEquals('[PHPUNIT] ETL Status: Fail (' . $this->fakeTodayYMD . ')', $message['subject']);
        $this->assertEquals($expected, $message['body']);
    }

    /**
     *
     */
    public function test_audit_status_okay()
    {
        $CheckbookEtlStatus =
            $this->getMockBuilder('CheckbookEtlStatus')
                ->setMethods(['timeNow', 'getUatStatus', 'getProdStatus', 'getConnectionConfigs'])
                ->getMock();

        $message = [];

        $CheckbookEtlStatus->expects($this->any())
            ->method('timeNow')
            ->will($this->returnValue($this->fakeTodayTimestamp));

        $CheckbookEtlStatus->expects($this->once())
            ->method('getUatStatus')
            ->will($this->returnValue([
                'success' => true,
                'data' => $this->fakeToday,
                'audit_status_timestamp' => $this->fakeTodayTimestamp,
                'audit_status' => ['OK'],
            ]));

        $CheckbookEtlStatus->expects($this->once())
            ->method('getProdStatus')
            ->will($this->returnValue([
                'success' => true,
                'data' => $this->fakeToday
            ]));

        $CheckbookEtlStatus->gatherData($message);

        $expected = [
            'subject' => 'Success',
            'uat_status' => [
                'success' => true,
                'data' => $this->fakeToday,
                'hint' => $CheckbookEtlStatus->niceDisplayDateDiff($this->fakeToday),
                'audit_status_timestamp' => $this->fakeTodayTimestamp,
                'audit_status' => ['OK'],
                'audit_status_time_diff' => $CheckbookEtlStatus->niceDisplayDateDiff($this->fakeToday),
            ],
            'prod_status' => [
                'success' => true,
                'data' => $this->fakeToday,
                'hint' => $CheckbookEtlStatus->niceDisplayDateDiff($this->fakeToday),
            ],
            'connections' => null,
            'connection_keys' => CheckbookEtlStatus::CONNECTIONS_KEYS,
            'fisa_files' => false
        ];

        $this->assertEquals('[PHPUNIT] ETL Status: Success (' . $this->fakeTodayYMD . ')', $message['subject']);
        $this->assertEquals($expected, $message['body']);
    }

    /**
     *
     */
    public function test_get_connection_configs()
    {
        global $conf;
        $conf['etl-status-footer'] = [
            'line1' => [
                'fakeKey' => 'fakeUrl',
            ]
        ];

        $CheckbookEtlStatus =
            $this->getMockBuilder('CheckbookEtlStatus')
                ->setMethods(['timeNow', 'getUatStatus', 'getProdStatus', 'get_contents'])
                ->getMock();

        $message = [];

        $CheckbookEtlStatus->expects($this->any())
            ->method('timeNow')
            ->will($this->returnValue($this->fakeTodayTimestamp));

        $CheckbookEtlStatus->expects($this->once())
            ->method('getUatStatus')
            ->will($this->returnValue([
                'success' => true,
                'data' => $this->fakeToday,
                'audit_status_timestamp' => $this->fakeTodayTimestamp,
                'audit_status' => ['OK'],
            ]));

        $CheckbookEtlStatus->expects($this->once())
            ->method('getProdStatus')
            ->will($this->returnValue([
                'success' => true,
                'data' => $this->fakeToday
            ]));

        $fakeConnectionsConfig = [];
        foreach (CheckbookEtlStatus::CONNECTIONS_KEYS as $key) {
            $fakeConnectionsConfig[$key] = 'https://' . $key;
        }

        $CheckbookEtlStatus->expects($this->once())
            ->method('get_contents')
            ->will($this->returnValue(json_encode(
                [
                    'success' => true,
                    'connections' => $fakeConnectionsConfig
                ]
            )));

        $CheckbookEtlStatus->gatherData($message);

        $expected = [
            'subject' => 'Success',
            'uat_status' => [
                'success' => true,
                'data' => $this->fakeToday,
                'hint' => $CheckbookEtlStatus->niceDisplayDateDiff($this->fakeToday),
                'audit_status_timestamp' => $this->fakeTodayTimestamp,
                'audit_status' => ['OK'],
                'audit_status_time_diff' => $CheckbookEtlStatus->niceDisplayDateDiff($this->fakeToday),
            ],
            'prod_status' => [
                'success' => true,
                'data' => $this->fakeToday,
                'hint' => $CheckbookEtlStatus->niceDisplayDateDiff($this->fakeToday),
            ],
            'connections' => [
                'fakeKey' => $fakeConnectionsConfig
            ],
            'connection_keys' => CheckbookEtlStatus::CONNECTIONS_KEYS,
            'fisa_files' => false
        ];

        $this->assertEquals('[PHPUNIT] ETL Status: Success (' . $this->fakeTodayYMD . ')', $message['subject']);
        $this->assertEquals($expected, $message['body']);
    }

    /**
     *
     */
    public function test_audit_status_not_okay()
    {
        $CheckbookEtlStatus =
            $this->getMockBuilder('CheckbookEtlStatus')
                ->setMethods(['timeNow', 'getUatStatus', 'getProdStatus', 'getConnectionConfigs'])
                ->getMock();

        $message = [];

        $CheckbookEtlStatus->expects($this->any())
            ->method('timeNow')
            ->will($this->returnValue($this->fakeTodayTimestamp));

        $CheckbookEtlStatus->expects($this->once())
            ->method('getUatStatus')
            ->will($this->returnValue([
                'success' => true,
                'data' => $this->fakeToday,
                'audit_status_timestamp' => $this->fakeTodayTimestamp,
                'audit_status' => ['Not okay'],
            ]));

        $CheckbookEtlStatus->expects($this->once())
            ->method('getProdStatus')
            ->will($this->returnValue([
                'success' => true,
                'data' => $this->fakeToday
            ]));

        $CheckbookEtlStatus->gatherData($message);

        $expected = [
            'subject' => 'Needs attention',
            'uat_status' => [
                'success' => true,
                'data' => $this->fakeToday,
                'hint' => $CheckbookEtlStatus->niceDisplayDateDiff($this->fakeToday),
                'audit_status_timestamp' => $this->fakeTodayTimestamp,
                'audit_status' => ['Not okay'],
                'audit_status_time_diff' => $CheckbookEtlStatus->niceDisplayDateDiff($this->fakeToday),
            ],
            'prod_status' => [
                'success' => true,
                'data' => $this->fakeToday,
                'hint' => $CheckbookEtlStatus->niceDisplayDateDiff($this->fakeToday),
            ],
            'connections' => null,
            'connection_keys' => CheckbookEtlStatus::CONNECTIONS_KEYS,
            'fisa_files' => false,
        ];

        $this->assertEquals('[PHPUNIT] ETL Status: Needs attention (' . $this->fakeTodayYMD . ')', $message['subject']);
        $this->assertEquals($expected, $message['body']);
    }

    /**
     *
     */
    public function test_needs_attention()
    {
        $CheckbookEtlStatus =
            $this->getMockBuilder('CheckbookEtlStatus')
                ->setMethods(['get_date', 'timeNow', 'getUatStatus', 'getProdStatus', 'getConnectionConfigs'])
                ->getMock();

        $message = [];

        $CheckbookEtlStatus->expects($this->any())
            ->method('timeNow')
            ->will($this->returnValue($this->fakeTodayTimestamp));

        $CheckbookEtlStatus->expects($this->once())
            ->method('getUatStatus')
            ->will($this->returnValue([
                'success' => true,
                'data' => $this->fakeToday,
                'invalid_records' => 'asdf',
                'invalid_records_timestamp' => ($this->fakeTodayTimestamp - 7200),
            ]));

        $CheckbookEtlStatus->expects($this->once())
            ->method('getProdStatus')
            ->will($this->returnValue([
                'success' => true,
                'data' => $this->fakeToday,
                'invalid_records' => 'asdf',
                'invalid_records_timestamp' => ($this->fakeYesterdayTime),
            ]));

        $CheckbookEtlStatus->gatherData($message);

        $expected = [
            'subject' => 'Needs attention',
            'uat_status' => [
                'success' => true,
                'data' => $this->fakeToday,
                'hint' => $CheckbookEtlStatus->niceDisplayDateDiff($this->fakeToday),
                'invalid_records' => 'asdf',
                'invalid_records_timestamp' => ($this->fakeTodayTimestamp - 7200)

            ],
            'prod_status' => [
                'success' => true,
                'data' => $this->fakeToday,
                'hint' => $CheckbookEtlStatus->niceDisplayDateDiff($this->fakeToday),
            ],
            'connections' => null,
            'connection_keys' => CheckbookEtlStatus::CONNECTIONS_KEYS,
            'fisa_files' => false,
        ];
        $this->assertEquals($expected, $message['body']);
    }

    /**
     *
     */
    public function test_needs_attention_real_file()
    {
        global $conf;
        $conf['etl-status-path'] = __DIR__ . '/files/';

        /**
         * @var CheckbookEtlStatus
         */
        $CheckbookEtlStatus =
            $this->getMockBuilder('CheckbookEtlStatus')
                ->setMethods(['get_date', 'timeNow', 'getUatStatus', 'getProdStatus', 'getConnectionConfigs'])
                ->getMock();

        $message = [];

        $CheckbookEtlStatus->expects($this->any())
            ->method('timeNow')
            ->will($this->returnValue($this->fakeTodayTimestamp));

        $CheckbookEtlStatus->expects($this->once())
            ->method('getUatStatus')
            ->will($this->returnValue([
                'success' => true,
                'data' => $this->fakeToday,
                "invalid_records" => array_map('str_getcsv',
                    file($conf['etl-status-path'] . 'invalid_records_details.csv')),
                'invalid_records_timestamp' => ($this->fakeTodayTimestamp - 7200),
            ]));

        $CheckbookEtlStatus->expects($this->once())
            ->method('getProdStatus')
            ->will($this->returnValue([
                'success' => true,
                'data' => $this->fakeToday,
                'invalid_records' => 'asdf',
                'invalid_records_timestamp' => ($this->fakeYesterdayTime),
            ]));

        $CheckbookEtlStatus->gatherData($message);

        $expected = [
            'subject' => 'Needs attention',
            'uat_status' => [
                'success' => true,
                'data' => $this->fakeToday,
                'hint' => $CheckbookEtlStatus->niceDisplayDateDiff($this->fakeToday),
                "invalid_records" => array_map('str_getcsv',
                    file($conf['etl-status-path'] . 'invalid_records_details.csv')),
                'invalid_records_timestamp' => ($this->fakeTodayTimestamp - 7200),

            ],
            'prod_status' => [
                'success' => true,
                'data' => $this->fakeToday,
                'hint' => $CheckbookEtlStatus->niceDisplayDateDiff($this->fakeToday),
            ],
            'connections' => null,
            'connection_keys' => CheckbookEtlStatus::CONNECTIONS_KEYS,
            'fisa_files' => false,
        ];
        $this->assertEquals($expected, $message['body']);
    }

    /**
     *
     */
    public function test_needs_attention_reasons()
    {
        global $conf;
        $conf['etl-status-path'] = __DIR__ . '/files/';
        $conf['etl-status-skip-invalid-records-reasons'] = ['Duplicate'];
        $conf['etl-status-skip-invalid-records-limit'] = 99;

        /**
         * @var CheckbookEtlStatus
         */
        $CheckbookEtlStatus =
            $this->getMockBuilder('CheckbookEtlStatus')
                ->setMethods(['get_date', 'timeNow', 'getUatStatus', 'getProdStatus', 'getConnectionConfigs'])
                ->getMock();

        $message = [];

        $CheckbookEtlStatus->expects($this->any())
            ->method('timeNow')
            ->will($this->returnValue($this->fakeTodayTimestamp));

        $CheckbookEtlStatus->expects($this->once())
            ->method('getUatStatus')
            ->will($this->returnValue([
                'success' => true,
                'data' => $this->fakeToday,
                "invalid_records" => array_map('str_getcsv',
                    file($conf['etl-status-path'] . 'invalid_records_details.csv')),
                'invalid_records_timestamp' => ($this->fakeTodayTimestamp - 7200),
            ]));

        $CheckbookEtlStatus->expects($this->once())
            ->method('getProdStatus')
            ->will($this->returnValue([
                'success' => true,
                'data' => $this->fakeToday,
                'invalid_records' => 'asdf',
                'invalid_records_timestamp' => ($this->fakeYesterdayTime),
            ]));

        $CheckbookEtlStatus->gatherData($message);

        $expected = [
            'subject' => 'Success',
            'uat_status' => [
                'success' => true,
                'data' => $this->fakeToday,
                'hint' => $CheckbookEtlStatus->niceDisplayDateDiff($this->fakeToday),

            ],
            'prod_status' => [
                'success' => true,
                'data' => $this->fakeToday,
                'hint' => $CheckbookEtlStatus->niceDisplayDateDiff($this->fakeToday),
            ],
            'connections' => null,
            'connection_keys' => CheckbookEtlStatus::CONNECTIONS_KEYS,
            'fisa_files' => false,
        ];
        $this->assertEquals($expected, $message['body']);
    }

    /**
     *
     */
    public function test_needs_attention_reasons_hit_limit()
    {
        global $conf;
        $conf['etl-status-path'] = __DIR__ . '/files/';
        $conf['etl-status-skip-invalid-records-reasons'] = ['Duplicate'];
        $conf['etl-status-skip-invalid-records-limit'] = 11;

        /**
         * @var CheckbookEtlStatus
         */
        $CheckbookEtlStatus =
            $this->getMockBuilder('CheckbookEtlStatus')
                ->setMethods(['get_date', 'timeNow', 'getUatStatus', 'getProdStatus', 'getConnectionConfigs'])
                ->getMock();

        $message = [];

        $CheckbookEtlStatus->expects($this->any())
            ->method('timeNow')
            ->will($this->returnValue($this->fakeTodayTimestamp));

        $CheckbookEtlStatus->expects($this->once())
            ->method('getUatStatus')
            ->will($this->returnValue([
                'success' => true,
                'data' => $this->fakeToday,
                "invalid_records" => array_map('str_getcsv',
                    file($conf['etl-status-path'] . 'invalid_records_details.csv')),
                'invalid_records_timestamp' => ($this->fakeTodayTimestamp - 7200),
            ]));

        $CheckbookEtlStatus->expects($this->once())
            ->method('getProdStatus')
            ->will($this->returnValue([
                'success' => true,
                'data' => $this->fakeToday,
                'invalid_records' => 'asdf',
                'invalid_records_timestamp' => ($this->fakeYesterdayTime),
            ]));

        $CheckbookEtlStatus->gatherData($message);

        $expected = [
            'subject' => 'Needs attention - too many invalid reasons skipped (11+)',
            'uat_status' => [
                'success' => true,
                'data' => $this->fakeToday,
                'hint' => $CheckbookEtlStatus->niceDisplayDateDiff($this->fakeToday),
                "invalid_records" => array_map('str_getcsv',
                    file($conf['etl-status-path'] . 'invalid_records_details.csv')),
                'invalid_records_timestamp' => ($this->fakeTodayTimestamp - 7200),

            ],
            'prod_status' => [
                'success' => true,
                'data' => $this->fakeToday,
                'hint' => $CheckbookEtlStatus->niceDisplayDateDiff($this->fakeToday),
            ],
            'connections' => null,
            'connection_keys' => CheckbookEtlStatus::CONNECTIONS_KEYS,
            'fisa_files' => false,
        ];
        $this->assertEquals($expected, $message['body']);
    }

    /**
     *
     */
    public function test_needs_attention_reasons_under_limit()
    {
        global $conf;
        $conf['etl-status-path'] = __DIR__ . '/files/';
        $conf['etl-status-skip-invalid-records-reasons'] = ['Duplicate'];
        $conf['etl-status-skip-invalid-records-limit'] = 31;

        /**
         * @var CheckbookEtlStatus
         */
        $CheckbookEtlStatus =
            $this->getMockBuilder('CheckbookEtlStatus')
                ->setMethods(['get_date', 'timeNow', 'getUatStatus', 'getProdStatus', 'getConnectionConfigs'])
                ->getMock();

        $message = [];

        $CheckbookEtlStatus->expects($this->any())
            ->method('timeNow')
            ->will($this->returnValue($this->fakeTodayTimestamp));

        $CheckbookEtlStatus->expects($this->once())
            ->method('getUatStatus')
            ->will($this->returnValue([
                'success' => true,
                'data' => $this->fakeToday,
                "invalid_records" => array_map('str_getcsv',
                    file($conf['etl-status-path'] . 'invalid_records_details.csv')),
                'invalid_records_timestamp' => ($this->fakeTodayTimestamp - 7200),
            ]));

        $CheckbookEtlStatus->expects($this->once())
            ->method('getProdStatus')
            ->will($this->returnValue([
                'success' => true,
                'data' => $this->fakeToday,
                'invalid_records' => 'asdf',
                'invalid_records_timestamp' => ($this->fakeYesterdayTime),
            ]));

        $CheckbookEtlStatus->gatherData($message);

        $expected = [
            'subject' => 'Success',
            'uat_status' => [
                'success' => true,
                'data' => $this->fakeToday,
                'hint' => $CheckbookEtlStatus->niceDisplayDateDiff($this->fakeToday),
            ],
            'prod_status' => [
                'success' => true,
                'data' => $this->fakeToday,
                'hint' => $CheckbookEtlStatus->niceDisplayDateDiff($this->fakeToday),
            ],
            'connections' => null,
            'connection_keys' => CheckbookEtlStatus::CONNECTIONS_KEYS,
            'fisa_files' => false,
        ];
        $this->assertEquals($expected, $message['body']);
    }

    /**
     *
     */
    public function test_mail_success_prod()
    {
        $CheckbookEtlStatus =
            $this->getMockBuilder('CheckbookEtlStatus')
                ->setMethods(['get_date', 'timeNow', 'getUatStatus', 'getProdStatus', 'getConnectionConfigs'])
                ->getMock();

        $message = [];

        $CheckbookEtlStatus->expects($this->any())
            ->method('timeNow')
            ->will($this->returnValue($this->fakeTodayTimestamp));

        $CheckbookEtlStatus->expects($this->once())
            ->method('getUatStatus')
            ->will($this->returnValue([
                'success' => true,
                'data' => $this->fakeYesterday
            ]));

        $CheckbookEtlStatus->expects($this->once())
            ->method('getProdStatus')
            ->will($this->returnValue([
                'success' => true,
                'data' => $this->fakeToday
            ]));

        $CheckbookEtlStatus->gatherData($message);

        $expected = [
            'subject' => 'Fail',
            'uat_status' => [
                'success' => false,
                'data' => $this->fakeYesterday,
                'hint' => 'Last success: ' . $CheckbookEtlStatus->niceDisplayDateDiff($this->fakeYesterday),
            ],
            'prod_status' => [
                'success' => true,
                'data' => $this->fakeToday,
                'hint' => $CheckbookEtlStatus->niceDisplayDateDiff($this->fakeToday),
            ],
            'connections' => false,
            'connection_keys' => CheckbookEtlStatus::CONNECTIONS_KEYS,
            'fisa_files' => false,
        ];
        $this->assertEquals($expected, $message['body']);
    }

    /**
     *
     */
    public function test_time_now()
    {
        $this->assertEquals(time(), $this->CES->timeNow());
    }

    /**
     *
     */
    public function test_date_now()
    {
        $this->assertEquals(date("Y-m-d"), $this->CES->get_date("Y-m-d"));
    }

    /**
     *
     */
    public function test_checkbook_etl_status_mail_alter_success()
    {
        $msg = [
            'subject' => 'Amazing Success',
            'body' => "Great\tSUCCESS",
            'module' => 'checkbook_etl_status',
            'headers' => []
        ];
        checkbook_etl_status_mail_alter($msg);
        $this->assertEquals('text/html; charset=UTF-8; format=flowed; delsp=yes', $msg['headers']['Content-Type']);
        $this->assertEquals("Great\tSUCCESS", $msg['body']);
        $this->assertTrue(empty($msg['headers']['X-Priority']));
        $this->assertTrue(empty($msg['headers']['Importance']));
        $this->assertTrue(empty($msg['headers']['X-MSMail-Priority']));
    }

    /**
     *
     */
    public function test_checkbook_etl_status_mail_alter_fail()
    {
        $msg = [
            'subject' => 'Big Fail',
            'body' => "Huge\tFAIL",
            'module' => 'checkbook_etl_status',
            'headers' => []
        ];
        checkbook_etl_status_mail_alter($msg);
        $this->assertEquals('text/html; charset=UTF-8; format=flowed; delsp=yes', $msg['headers']['Content-Type']);
        $this->assertEquals("Huge\tFAIL", $msg['body']);
        $this->assertFalse(empty($msg['headers']['X-Priority']));
        $this->assertFalse(empty($msg['headers']['Importance']));
        $this->assertFalse(empty($msg['headers']['X-MSMail-Priority']));
    }
}
