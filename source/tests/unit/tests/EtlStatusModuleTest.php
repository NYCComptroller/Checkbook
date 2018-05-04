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
    private $fakeToday = '2222-11-22';
    /**
     * @var string
     */
    private $fakeYesterday = '2222-11-21';

    /**
     *
     */
    public function test_checkbook_etl_status_cron_empty_recepients_list()
    {
        global $conf;
        if (isset($conf['etl_status_recipients'])) {
            unset($conf['etl_status_recipients']);
        }
        $CES = new CheckbookEtlStatus();
        $this->assertFalse($CES->run_cron());
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
        $CES = new CheckbookEtlStatus();
        $this->assertFalse($CES->run_cron());
    }

    /**
     *
     */
    public function test_checkbook_etl_status_cron_already_ran()
    {
        global $conf;
        global $base_url;
        $conf['etl_status_recipients'] = true;
        $base_url = 'uat-checkbook-nyc.reisys.com';

        global $mocked_variable;
        global $mocked_date;
        $mocked_variable['checkbook_etl_status_last_run'] = $mocked_date['Y-m-d'] = $this->fakeToday;

        $CES = new CheckbookEtlStatus();
        $this->assertFalse($CES->run_cron());
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
            ->will($this->returnValue($this->fakeToday));
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
            ->will($this->returnValue($this->fakeToday));
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
    public function test_mail_format_success()
    {
        $CheckbookEtlStatus =
            $this->getMockBuilder('CheckbookEtlStatus')
                ->setMethods(['date', 'getUatStatus'])
                ->getMock();

        $message = null;

        $CheckbookEtlStatus->expects($this->at(0))
            ->method('date')
            ->with($this->equalTo('Y-m-d'))
            ->will($this->returnValue($this->fakeToday));

        $CheckbookEtlStatus->expects($this->once())
            ->method('getUatStatus')
            ->will($this->returnValue([
                'success' => true,
                'data' => $this->fakeToday
            ]));

        $CheckbookEtlStatus->mail($message);

        $expected = <<<EOM
UAT  ETL STATUS:\t\tSUCCESS (ran today {$this->fakeToday})
PROD ETL STATUS:\tUNKNOWN
EOM;

        $this->assertEquals($expected, $message['body'][0]);
    }

    /**
     *
     */
    public function test_mail_format_fail()
    {
        $CheckbookEtlStatus =
            $this->getMockBuilder('CheckbookEtlStatus')
                ->setMethods(['date', 'getUatStatus'])
                ->getMock();

        $message = null;

        $CheckbookEtlStatus->expects($this->at(0))
            ->method('date')
            ->with($this->equalTo('Y-m-d'))
            ->will($this->returnValue($this->fakeToday));

        $CheckbookEtlStatus->expects($this->once())
            ->method('getUatStatus')
            ->will($this->returnValue([
                'success' => true,
                'data' => $this->fakeYesterday
            ]));

        $CheckbookEtlStatus->mail($message);

        $expected = <<<EOM
UAT  ETL STATUS:\t\tFAIL (last successful run {$this->fakeYesterday})
PROD ETL STATUS:\tUNKNOWN
EOM;

        $this->assertEquals($expected, $message['body'][0]);
    }
}
