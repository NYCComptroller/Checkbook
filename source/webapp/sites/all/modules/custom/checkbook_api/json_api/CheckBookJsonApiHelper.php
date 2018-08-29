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
     * CheckBookJsonApiHelper constructor.
     * @param $jsonApi
     */
    public function __construct($jsonApi)
    {
        $this->JsonApi = $jsonApi;
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

        $match_status = [];
        $match_status_timestamp = 0;
        $match_status_csv_path = $conf['etl-status-path'] . 'file_data_statistics.csv';
        $data_source_name_file_path = $conf['etl-status-path'] . 'data_source_name.txt';

        try {
            if (is_file($match_status_csv_path) && is_file($data_source_name_file_path)) {
                $data_source_names = array_map('trim', file($data_source_name_file_path));
                if ('data_source_name' == $data_source_names[0]) {
//                    removing first line
                    array_shift($data_source_names);
                }
                foreach($data_source_names as $source) {
                    $match_status[$source] = false;
                }

                $match_status_csv = array_map('trim', file($match_status_csv_path));
                foreach($match_status_csv as $line){
                    list($source,) = explode(',',$line);
                    if ('data_source_name' == $source) {
                        continue;
                    }
                    if (in_array($source, $data_source_names)) {
                        $match_status[$source] = true;
                    }
                }
                $match_status_timestamp = filemtime($match_status_csv_path);
            } else {
                $match_status = false;
            }
        } catch (Exception $e) {
            $this->JsonApi->message .= $e->getMessage();
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
     * @return array
     * @throws \Exception
     */
    public function getUatEtlStatus()
    {
        $data = 0;

        try {
            $response = $this->JsonApi->Model->get_etl_status();
        } catch (Exception $e) {
            $this->JsonApi->success = false;
            $this->JsonApi->message = $e->getMessage();
        }

        if (!empty($response) && $response[0]['last_successful_run']) {
            $data = $response[0]['last_successful_run'];
        }

        return [
            'success' => $this->JsonApi->success,
            'data' => $data,
            'message' => $this->JsonApi->message,
            'info' => 'Last successful ETL run date'
        ];
    }

    /**
     * @param $databases
     * @param $conf
     * @param $return
     * @return array
     */
    public function get_connections_info()
    {
        global $conf, $databases;
        $connections_info = [];
        if (!empty($databases['default']['default']['host'])) {
            $connections_info['mysql'] = $databases['default']['default']['host']
                . '|' . $databases['default']['default']['database'];
        }
        if (!empty($databases['checkbook']['main']['host'])) {
            $connections_info['psql_main'] = $databases['checkbook']['main']['host']
                . '|' . $databases['checkbook']['main']['database'];
        }
        if (!empty($databases['checkbook']['etl']['host'])) {
            $connections_info['psql_etl'] = $databases['checkbook']['etl']['host']
                . '|' . $databases['checkbook']['etl']['database'];
        }
        if (!empty($databases['checkbook_oge']['main']['host'])) {
            $connections_info['psql_oge'] = $databases['checkbook_oge']['main']['host']
                . '|' . $databases['checkbook_oge']['main']['database'];
        }
        if (!empty($databases['checkbook_nycha']['main']['host'])) {
            $connections_info['psql_nycha'] = $databases['checkbook_nycha']['main']['host']
                . '|' . $databases['checkbook_nycha']['main']['database'];
        }
        if (!empty($conf['check_book']['solr']['url'])) {
            $solr_url = $conf['check_book']['solr']['url'];
            $connections_info['solr'] = substr($solr_url, 0, stripos($solr_url, '/solr/') + 6)
                . '|' . substr($solr_url, $pos = stripos($solr_url, '/solr/') + 6, strlen($solr_url) - $pos - 1);
        }
        return $connections_info;
    }

}
