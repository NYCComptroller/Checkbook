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
    public function test_format_status_yesterday_date()
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
            'data' => $this->fakeYesterday
        ];

        $this->assertEquals('<strong style="color:red">FAIL</strong> (last success: ' .
            $this->CES->niceDisplayDate($this->fakeYesterday) . ")",
            $CheckbookEtlStatus->formatStatus($sampleData));
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

        $this->assertEquals('<strong style="color:darkgreen">SUCCESS</strong> (finished: ' .
            $this->CES->niceDisplayDate($this->fakeToday) . ")",
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

        $expected = ['success' => true];

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

        $this->assertEquals("FAIL (unknown)", $CheckbookEtlStatus->formatStatus($sampleData));
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

        $expected = <<<EOM
UAT  ETL STATUS:\t<strong style="color:darkgreen">SUCCESS</strong> (finished: {$this->CES->niceDisplayDate($this->fakeToday)})<br /><br />
PROD ETL STATUS:\t<strong style="color:red">FAIL</strong> (last success: {$this->CES->niceDisplayDate($this->fakeYesterday)})

EOM;

        $this->assertEquals('ETL Status: Fail ('.$this->fakeToday.')', $message['subject']);
        $this->assertEquals($expected, $message['body'][0]);
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

        $expected = <<<EOM
UAT  ETL STATUS:\t<strong style="color:red">FAIL</strong> (last success: {$this->CES->niceDisplayDate($this->fakeYesterday)})<br /><br />
PROD ETL STATUS:\t<strong style="color:darkgreen">SUCCESS</strong> (finished: {$this->CES->niceDisplayDate($this->fakeToday)})

EOM;

        $this->assertEquals($expected, $message['body'][0]);
    }

    /**
     *
     */
    public function test_format_display_date()
    {
        $testDate = '2099-12-11 01:02:03.456789';
        $this->assertEquals('2099-12-11 01:02AM', $this->CES->niceDisplayDate($testDate));
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

    /**
     *
     */
    public function test_monday()
    {
        $CheckbookEtlStatus =
            $this->getMockBuilder('CheckbookEtlStatus')
                ->setMethods(['timeNow', 'getUatStatus', 'getProdStatus'])
                ->getMock();

        $message = null;

        $CheckbookEtlStatus->expects($this->any())
            ->method('timeNow')
            ->will($this->returnValue($this->fakeTuesday));

        $expectedWarning = "\n<br /><br />" . CheckbookEtlStatus::MONDAY_NOTICE;

        $this->assertEquals($expectedWarning,
            $CheckbookEtlStatus->comment());
    }
}