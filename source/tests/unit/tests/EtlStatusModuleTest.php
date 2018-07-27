<?php

include_once __DIR__ . '/../../../webapp/sites/all/modules/custom/checkbook_etl_status/checkbook_etl_status.module';

use PHPUnit\Framework\TestCase;

/**
 * Class CheckbookApiModuleTest
 */
class EtlStatusModuleTest extends TestCase
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
    private $fakeTimeToday = 7980451260;
    /**
     * @var string
     */
    private $fakeYesterday = '2222-11-21 03:39:54.635115';

    /**
     * @var int
     * 2222-11-26 08:01:00  == Tuesday Morning !
     */
    private $fakeTuesday = 7980796860;

    /**
     * @var /CheckbookEtlStatus
     */
    private $CES;

    /**
     *
     */
    public function setUp()
    {
        parent::setUp();
        $this->CES = new CheckbookEtlStatus();
    }

    /**
     *
     */
    public function test_checkbook_etl_status_cron_empty_recepients_list()
    {
        global $conf;
        if (isset($conf['etl_status_recipients'])) {
            unset($conf['etl_status_recipients']);
        }
        $this->assertFalse($this->CES->run_cron());
    }

    /**
     *
     */
    public function test_checkbook_etl_status_cron_wrong_domain()
    {
        global $conf;
        global $base_url;
        $conf['etl_status_recipients'] = true;
        $base_url = 'wrong.domain';
        $this->assertFalse($this->CES->run_cron());
    }

    /**
     *
     */
    public function test_checkbook_etl_status_cron_already_ran()
    {
        global $conf;
        global $base_url;
        $conf['etl_status_recipients'] = true;
        $base_url = 'http://uat-checkbook-nyc.reisys.com/asdf';

        global $mocked_variable;
        global $mocked_date;

        $CheckbookEtlStatus =
            $this->getMockBuilder('CheckbookEtlStatus')
                ->setMethods(['date'])
                ->getMock();
        $CheckbookEtlStatus->expects($this->at(0))
            ->method('date')
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
        global $conf;
        global $base_url;
        $conf['etl_status_recipients'] = true;
        $base_url = 'http://uat-checkbook-nyc.reisys.com/asdf';

        global $mocked_variable;
        $mocked_variable['checkbook_etl_status_last_run'] = $this->fakeYesterday;
        $CheckbookEtlStatus =
            $this->getMockBuilder('CheckbookEtlStatus')
                ->setMethods(['date'])
                ->getMock();
        $CheckbookEtlStatus->expects($this->at(0))
            ->method('date')
            ->with($this->equalTo('Y-m-d'))
            ->will($this->returnValue($this->fakeTodayYMD));
        $CheckbookEtlStatus->expects($this->at(1))
            ->method('date')
            ->with($this->equalTo('H'))
            ->will($this->returnValue('5'));

        $this->assertFalse($CheckbookEtlStatus->run_cron());
    }

    /**
     *
     */
    public function test_checkbook_etl_status_cron_success()
    {
        global $conf, $base_url;
        $conf['etl_status_recipients'] = true;
        $base_url = 'http://uat-checkbook-nyc.reisys.com/asdf';

        global $mocked_variable;
        $mocked_variable['checkbook_etl_status_last_run'] = $this->fakeYesterday;
        $CheckbookEtlStatus =
            $this->getMockBuilder('CheckbookEtlStatus')
                ->setMethods(['date', 'sendmail'])
                ->getMock();
        $CheckbookEtlStatus->expects($this->at(0))
            ->method('date')
            ->with($this->equalTo('Y-m-d'))
            ->will($this->returnValue($this->fakeTodayYMD));
        $CheckbookEtlStatus->expects($this->at(1))
            ->method('date')
            ->with($this->equalTo('H'))
            ->will($this->returnValue('8'));
        $CheckbookEtlStatus->expects($this->once())
            ->method('sendmail')
            ->will($this->returnValue('777'));

        $this->assertEquals('777', $CheckbookEtlStatus->run_cron());
    }

    /**
     *
     */
    public function test_format_status_success()
    {
        $CheckbookEtlStatus =
            $this->getMockBuilder('CheckbookEtlStatus')
                ->setMethods(['timeNow'])
                ->getMock();

        $CheckbookEtlStatus->expects($this->once())
            ->method('timeNow')
            ->will($this->returnValue($this->fakeTimeToday));

        $sampleData = [
            'success' => true,
            'data' => $this->fakeToday
        ];

        $expected = [
            'success' => true,
            'data' => $this->fakeToday,
            'hint' => $this->CES->niceDisplayDateDiff($this->fakeToday)
        ];

        $this->assertEquals($expected,
            $CheckbookEtlStatus->formatStatus($sampleData));
    }

    /**
     *
     */
    public function test_sendmail()
    {
        $this->assertTrue($this->CES->sendmail());
    }

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
            ->will($this->returnValue($this->fakeTimeToday));

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
                ->setMethods(['date', 'timeNow', 'getUatStatus', 'getProdStatus'])
                ->getMock();

        $message = null;

        $CheckbookEtlStatus->expects($this->any())
            ->method('timeNow')
            ->will($this->returnValue($this->fakeTimeToday));

        $CheckbookEtlStatus->expects($this->once())
            ->method('date')
            ->will($this->returnValue($this->fakeToday));

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

        $CheckbookEtlStatus->mail($message);

        $expected = [
            'uat_status' => [
                'success' => true,
                'data' => $this->fakeToday,
                'hint' => $this->CES->niceDisplayDateDiff($this->fakeToday),
            ],
            'prod_status' => [
                'success' => false,
                'data' => $this->fakeYesterday,
                'hint' => 'Last success: '.$this->CES->niceDisplayDateDiff($this->fakeYesterday),
            ]
        ];

        $this->assertEquals('ETL Status: Success ('.$this->fakeToday.')', $message['subject']);
        $this->assertEquals($expected, $message['body']);
    }

    /**
     *
     */
    public function test_mail_success_prod()
    {
        $CheckbookEtlStatus =
            $this->getMockBuilder('CheckbookEtlStatus')
                ->setMethods(['date', 'timeNow', 'getUatStatus', 'getProdStatus'])
                ->getMock();

        $message = null;

        $CheckbookEtlStatus->expects($this->any())
            ->method('timeNow')
            ->will($this->returnValue($this->fakeTimeToday));

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

        $CheckbookEtlStatus->mail($message);

        $expected = [
            'uat_status' => [
                'success' => false,
                'data' => $this->fakeYesterday,
                'hint' => 'Last success: '.$this->CES->niceDisplayDateDiff($this->fakeYesterday),
            ],
            'prod_status' => [
                'success' => true,
                'data' => $this->fakeToday,
                'hint' => $this->CES->niceDisplayDateDiff($this->fakeToday),
            ]
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
        $this->assertEquals(date("Y-m-d"), $this->CES->date("Y-m-d"));
    }
}