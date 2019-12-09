<?php

namespace checkbook_json_api;

use \Exception;

/**
 * Class CheckBookJsonApiHelper
 * @package checkbook_json_api
 *
 */
class CheckBookJsonApiHelper
{
    /**
     * @var CheckBookJsonApi
     */
    private $JsonApi;

    /**
     * @var int
     */
    public $timeNow;

    /**
     * @var
     */
    public $dataSourceLastSuccess;

    /**
     * CheckBookJsonApiHelper constructor.
     * @param $jsonApi
     */
    public function __construct($jsonApi)
    {
        $this->JsonApi = $jsonApi;
        $this->timeNow = time();
    }


    /**
     * @param $args
     * @return bool|false|int|string
     */
    public function validate_year($args)
    {
        $year = !empty($args[1]) ? $args[1] : false;
        $year = $year ?: date('Y');
        $year = (is_numeric($year) && $year > 2009 && $year <= (int)date('Y')) ? $year : false;
        if (!$year) {
            $this->JsonApi->message = 'invalid year';
            $this->JsonApi->success = false;
            return false;
        }
        return $year;
    }

    /**
     * @param $year_type
     * @return string
     */
    public function get_verbal_year_type($year_type)
    {
        if ('c' == strtolower($year_type)) {
            return 'calendar';
        }
        return 'fiscal';
    }

    /**
     * @param $args
     * @param $default
     * @return string
     */
    public function validate_year_type($args, $default = 'B')
    {
        $year_type = !empty($args[2]) ? $args[2] : $default;
        switch (strtolower($year_type)) {
            case 'c':
            case 'calendar':
                return 'C';
            case 'b':
            case 'fiscal':
                return 'B';
            default:
                return $default;
        }
    }

    /**
     * @return array
     */
    public function getProdEtlStatus()
    {
        global $conf;

        $data = 0;

        try {
            $data = file_get_contents($conf['etl-status-path'] . 'etl_status.txt');
            list(, $date) = explode(',', $data);
            $data = trim($date);
        } catch (Exception $e) {
            $this->JsonApi->message .= $e->getMessage();
        }

        $invalid_records = '';
        $invalid_records_timestamp = 0;
        $invalid_records_csv_path = $conf['etl-status-path'] . 'invalid_records_details.csv';
        try {
            if (is_file($invalid_records_csv_path)) {
                $invalid_records = array_map('str_getcsv', file($invalid_records_csv_path));
                $invalid_records_timestamp = filemtime($invalid_records_csv_path);
            } else {
                $invalid_records = [
                    'FATAL ERROR',
                    'Could not find `invalid_records_details.csv` on server'
                ];
            }
        } catch (Exception $e) {
            $this->JsonApi->message .= $e->getMessage();
        }

        $audit_status = '';
        $audit_status_timestamp = 0;
        $audit_status_csv_path = $conf['etl-status-path'] . 'audit_status.txt';
        try {
            if (is_file($audit_status_csv_path)) {
                $audit_status = array_map('trim', file($audit_status_csv_path));
                $audit_status_timestamp = filemtime($audit_status_csv_path);
            } else {
                $audit_status = [
                    'FATAL ERROR',
                    'Could not find `audit_status_details.csv` on server'
                ];
            }
        } catch (Exception $e) {
            $this->JsonApi->message .= $e->getMessage();
        }

        $match_status_timestamp = filemtime($conf['etl-status-path'] . 'file_data_statistics.csv');
        $match_status = false;
        if (defined('PHPUNIT_RUNNING') || (60 * 60 * 12 > (time() - $match_status_timestamp))) {
            $match_status = $this->getMatchStatus();
        }


        return [
            'success' => $this->JsonApi->success,
            'data' => $data,
            'message' => $this->JsonApi->message,
            'invalid_records' => $invalid_records,
            'invalid_records_timestamp' => $invalid_records_timestamp,
            'audit_status' => $audit_status,
            'audit_status_timestamp' => $audit_status_timestamp,
            'match_status' => $match_status,
            'match_status_timestamp' => $match_status_timestamp,
        ];
    }

    /**
     * @return array|bool
     */
    public function getMatchStatus()
    {
        global $conf;

        $match_status = [];
        $match_status_csv_path = $conf['etl-status-path'] . 'file_data_statistics.csv';
        $data_source_name_file_path = $conf['etl-status-path'] . 'data_source_name.txt';
        $data_source_last_success_file_path = $conf['etl-status-path'] . 'data_source_last_success.txt';
        $data_source_last_success_md5 = 0;

        try {
            if (is_file($match_status_csv_path) && is_file($data_source_name_file_path)) {
//                reading last success dates for each data source
                if (is_file($data_source_last_success_file_path)) {
                    $data = file_get_contents($data_source_last_success_file_path);
                    $data_source_last_success = unserialize($data);
                    $data_source_last_success_md5 = md5($data);
                    unset($data);
                } else {
                    $data_source_last_success = [];
                }

                if (!empty($this->dataSourceLastSuccess)) {
//                    phpunit
                    $data_source_last_success = unserialize($this->dataSourceLastSuccess);
                }

//                reading full list of data source names
                $data_source_names = array_map('trim', file($data_source_name_file_path));
                if ('data_source_name' == $data_source_names[0]) {
//                    removing first (header) line
                    array_shift($data_source_names);
                }

//                filling $match_status with zeros
                foreach ($data_source_names as $source) {
                    $match_status[$source] = false;
                }

//                reading today etl status result - list of successfully ran data sources
                $match_status_csv = array_map('trim', file($match_status_csv_path));
                foreach ($match_status_csv as $line) {
                    list($source,) = explode(',', $line);
                    if (in_array($source, ['Data Source Name', 'data_source_name'])) {
//                        skipping header
                        continue;
                    }

//                    removing successfully ran data source names from output
                    if (in_array($source, $data_source_names)) {
                        unset($match_status[$source]);
                        $data_source_last_success[$source] = date('Y-m-d', $this->timeNow);
                    } else {
                        $match_status[$source] = 'unknown data source name';
                    }
                }

//                checking last success run date
                foreach ($match_status as $source => $value) {
                    if ($value) {
//                        skipping preset values like 'unknown data source name'
                        continue;
                    }
//                   fail
                    if (empty($data_source_last_success[$source])) {
                        $match_status[$source] = 'unknown';
                    } else {
                        $interval = date_diff(
                            date_create($data_source_last_success[$source]),
                            date_create(date('Y-m-d', $this->timeNow)));
                        $match_status[$source] = $interval->format('%a days ago');
                    }
                }

                $data = serialize($data_source_last_success);
                if ($data_source_last_success && (md5($data) !== $data_source_last_success_md5)) {
                    if (!defined('PHPUNIT_RUNNING')) {
                        if (!file_put_contents($data_source_last_success_file_path, $data)) {
                            \LogHelper::log_error('Could not write file: '.$data_source_last_success_file_path);
                        }
                    } else {
                        $this->dataSourceLastSuccess = $data;
                    }
                }
            } else {
                return false;
            }
        } catch (Exception $e) {
            $this->JsonApi->message .= $e->getMessage();
        }

        return $match_status;
    }
}
