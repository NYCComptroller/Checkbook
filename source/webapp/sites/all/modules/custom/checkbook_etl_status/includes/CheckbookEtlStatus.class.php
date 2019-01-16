<?php

/**
 * Class CheckbookEtlStatus
 */
class CheckbookEtlStatus
{
    /**
     * @var bool
     */
    private static $message = false;

    /**
     * Last ETL must successfully finish within last 12 hours
     */
    const SUCCESS_IF_RUN_LESS_THAN_X_SECONDS_AGO = 60 * 60 * 12;

    /**
     *
     */
    const CRON_LAST_RUN_DRUPAL_VAR = 'checkbook_etl_status_last_run';

    /**
     * List of conf db connections
     */
    const CONNECTIONS_KEYS = [
        'mysql',
        'psql_main',
        'psql_etl',
        'psql_oge',
        'psql_nycha',
        'solr',
    ];

    /**
     * @var string
     */
    public $successSubject = 'Success';

    /**
     * @param $format
     * @return false|string
     */
    public function get_date($format)
    {
        return date($format, $this->timeNow());
    }

    /**
     * @param $url
     * @return bool|string
     */
    public function get_contents($url)
    {
        return file_get_contents($url);
    }

    /**
     * @return int
     */
    public function timeNow()
    {
        return time();
    }

    /**
     * @return bool
     */
    public function run_cron()
    {
        global $conf;

        date_default_timezone_set('America/New_York');

//        always run cron for developer
        if (defined('CHECKBOOK_DEV')) {
            return $this->sendmail();
        }
        if (!isset($conf['checkbook_dev_group_email'])) {
            //error_log("ETL STATUS MAIL CRON skips. Reason: \$conf['checkbook_dev_group_email'] not defined");
            return false;
        }

        if (empty($conf['CHECKBOOK_ENV']) || !in_array($conf['CHECKBOOK_ENV'], ['UAT','PHPUNIT'])) {
            // we run this cron only on UAT and PHPUNIT
            return false;
        }

        $today = $this->get_date('Y-m-d');
        $current_hour = (int)$this->get_date('H');

        if (variable_get(self::CRON_LAST_RUN_DRUPAL_VAR) == $today) {
            //error_log("ETL STATUS MAIL CRON skips. Reason: already ran today :: $today :: ".variable_get($variable_name));
            return false;
        }

        if ($current_hour < 9 || $current_hour > 10) {
            //error_log("ETL STATUS MAIL CRON skips. Reason: will run between 9 AM and 11 AM EST :: current hour: $current_hour");
            return false;
        }

        variable_set(self::CRON_LAST_RUN_DRUPAL_VAR, $today);
        return $this->sendmail();
    }

    /**
     * @return bool
     */
    public function sendmail()
    {
        global $conf;

        $from = $conf['email_from'];

        if (isset($conf['checkbook_dev_group_email'])){
            $to_dev = $conf['checkbook_dev_group_email'];

            try{
                drupal_mail('checkbook_etl_status', 'send-dev-status', $to_dev, null, ['dev_mode'=> true], $from);
            } catch(Exception $ex1){
                error_log($ex1->getMessage());
            }
        }

        if (isset($conf['checkbook_client_emails'])) {
            $to_client = $conf['checkbook_client_emails'];
            try{
                drupal_mail('checkbook_etl_status', 'send-client-status', $to_client, null, ['dev_mode'=> false], $from);
            } catch(Exception $ex2){
                error_log($ex2->getMessage());
            }
        }

        return true;
    }

    /**
     * @return array
     */
    public function getUatStatus()
    {
        $local_api = new \checkbook_json_api\CheckBookJsonApi();
        $result = $local_api->etl_status();
        $result['source'] = 'UAT';
        return $result;
    }

    /**
     * @return bool|mixed
     */
    public function getProdStatus()
    {
        try {
            $prod_json_status = $this->get_contents('https://www.checkbooknyc.com/json_api/etl_status');
            $prod_status = json_decode($prod_json_status, true);
            $prod_status['source'] = 'PROD';
            return $prod_status;
        } catch (Exception $e) {
            error_log($e->getMessage());
        }
        return false;
    }

    /**
     * @param $date
     * @return false|string
     */
    public function niceDisplayDateDiff($date)
    {
        if (!$date) {
            return 'never';
        }
        if (is_numeric($date)) {
            $date = date('c', $date);
        }
        $date1 = date_create($date);
        $interval = date_diff($date1, date_create($this->get_date("Y-m-d H:i:s")));
        return $interval->format('%a day(s) %h hour(s) ago');
    }

    /**
     * @param $data
     * @return string
     */
    public function formatStatus($data)
    {
        global $conf;
        $now = $this->timeNow();

        if (!empty($data['success']) && true == $data['success']) {
            $data['hint'] = $this->niceDisplayDateDiff($data['data']);

            if (($now - strtotime($data['data'])) > self::SUCCESS_IF_RUN_LESS_THAN_X_SECONDS_AGO) {
                $data['hint'] = 'Last success: ' . $data['hint'];
                $data['success'] = false;
                $this->successSubject = 'Fail';
            }
        } else {
            $this->successSubject = 'Fail';
            $data['success'] = false;
            $data['hint'] = 'Could not get data from server';
        }

        if (!empty($data['invalid_records_timestamp'])) {
            if (!defined('CHECKBOOK_DEV') && (($now - $data['invalid_records_timestamp']) > self::SUCCESS_IF_RUN_LESS_THAN_X_SECONDS_AGO)) {
                unset($data['invalid_records_timestamp']);
                if (!empty($data['invalid_records'])) {
                    unset($data['invalid_records']);
                }
            } else {
                if (isset($conf['etl-status-skip-invalid-records-reasons'])) {
                    $filtered_invalid_records = [];
                    $skipped = 0;
                    $limitReached = false;
                    foreach ($data['invalid_records'] as $line) {
                        if (!in_array($line[3], $conf['etl-status-skip-invalid-records-reasons'])) {
                            $filtered_invalid_records[] = $line;
                        } elseif (++$skipped > $conf['etl-status-skip-invalid-records-limit']) {
                            if ('Success' == $this->successSubject) {
                                $this->successSubject = 'Needs attention - too many invalid reasons skipped (' .
                                    $conf['etl-status-skip-invalid-records-limit'] . '+)';
                            }
                            $limitReached = true;
                            break;
                        }
                    }
                    if (!$limitReached) {
                        if (1 < sizeof($filtered_invalid_records)) {
                            if ('Success' == $this->successSubject) {
                                $this->successSubject = 'Needs attention';
                            }
                            $data['invalid_records'] = $filtered_invalid_records;
                        } else {
                            unset($data['invalid_records']);
                            unset($data['invalid_records_timestamp']);
                        }
                    }
                } else {
                    if ('Success' == $this->successSubject) {
                        $this->successSubject = 'Needs attention';
                    }
                }
            }
        }

        if (!empty($data['audit_status_timestamp'])) {
            if (!defined('CHECKBOOK_DEV') && (($now - $data['audit_status_timestamp']) > self::SUCCESS_IF_RUN_LESS_THAN_X_SECONDS_AGO)) {
                unset($data['audit_status_timestamp']);
                if (!empty($data['audit_status'])) {
                    unset($data['audit_status']);
                }
            } else {
                $allGood = ['OK'];
                $data['audit_status_time_diff'] = $this->niceDisplayDateDiff($data['audit_status_timestamp']);
                if (($allGood !== $data['audit_status']) && ('Success' == $this->successSubject)) {
                    $this->successSubject = 'Needs attention';
                }
            }
        }

        if (!empty($data['match_status_timestamp'])) {
            if (!defined('CHECKBOOK_DEV') && (($now - $data['match_status_timestamp']) > self::SUCCESS_IF_RUN_LESS_THAN_X_SECONDS_AGO)) {
                unset($data['match_status_timestamp']);
                if (!empty($data['match_status'])) {
                    unset($data['match_status']);
                }
            }
        }

        return $data;
    }

    /**
     * @return array
     */
    public function getConnectionConfigs()
    {
        global $conf;

        $return = [];
        if (empty($conf['etl-status-footer']['line1'])) {
            return $return;
        }

        foreach ($conf['etl-status-footer']['line1'] as $env => $url) {
            try {
                $prod_json_status = $this->get_contents($url . 'json_api/etl_status');
                $json = json_decode($prod_json_status, true);
                if (!empty($json['connections'])) {
                    $return[$env] = $json['connections'];
                }
            } catch (Exception $e) {
                error_log($e->getMessage());
            }
        }
        return $return;
    }

    /**
     * @return array
     */
    public function getSolrHealthStatus()
    {
        global $conf;
        $solr_health = [];
        if (empty($conf['check_book']['solr23']) || empty($conf['check_book']['solr24'])) {
            return $solr_health;
        }
        foreach (['solr23', 'solr24'] as $server) {
            foreach ($conf['check_book'][$server] as $core => $url) {
                try {
                    $health = $this->get_contents($url . 'admin/ping?wt=json');
                    $solr_health[$server][$core]['status'] = json_decode($health, true)['status'];
                } catch (Exception $e) {
                    $solr_health[$server][$core]['status'] = $e->getMessage();
                }
                $solr_health[$server][$core]['url'] = $url . 'admin/ping';
            }
        }

        return $solr_health;
    }

    /**
     * @param $message
     * @return bool
     */
    public function prepareMessage(&$message)
    {
        if (false !== self::$message) {
            $message = array_merge($message, self::$message);
        }

        global $conf;
        $uat_status = $this->formatStatus($this->getUatStatus());
        $prod_status = $this->formatStatus($this->getProdStatus());
        $connections = $this->getConnectionConfigs();
        $solr_health_status = $this->getSolrHealthStatus();

        $msg = [];
        $msg['body'] =
            [
                'uat_status' => $uat_status,
                'prod_status' => $prod_status,
                'subject' => $this->successSubject,
                'solr_health_status' => $solr_health_status,
                'connections' => $connections,
                'connection_keys' => self::CONNECTIONS_KEYS,
            ];

        $date = $this->get_date('Y-m-d');

        $msg['subject'] = "[{$conf['CHECKBOOK_ENV']}] ETL Status: " . $this->successSubject . " ($date)";

        $message = array_merge($message, $msg);
        self::$message = $msg;

        return true;
    }
}
