<?php

/**
 * Class CheckbookRefFiles
 */
class CheckbookRefFiles
{
    const REF_DATA_QUERIES = [
        'agency_code_list' => 'SELECT agency_code \\"Agency Code\\",agency_name \\"Agency Name\\"  FROM ref_agency where is_display = \'Y\' ORDER BY agency_name',
        'vendor_code_list' => 'SELECT vendor_customer_code \\"Vendor Code\\", legal_name \\"Vendor Name\\" FROM vendor',
        'department_code_list' => 'SELECT distinct d.department_code \\"Department Code\\", d.department_name \\"Department Name\\",a.agency_code \\"Agency Code\\", a.agency_name \\"Agency Name\\" FROM ref_department d LEFT OUTER JOIN ref_agency a  ON d.agency_id = a.agency_id ORDER BY d.department_name',
        'mwbe_code_list' => 'SELECT DISTINCT minority_type_name \\"Minority Type Name\\", minority_type_id \\"Minority Type Id\\" FROM ref_minority_type',
        'industry_code_list' => 'SELECT DISTINCT industry_type_name \\"Industry Type Name\\", industry_type_id \\"Industry Type Id\\" FROM ref_industry_type',

        // Budget:
        'budget_code_list' => 'SELECT distinct budget_code \\"Budget Code\\",attribute_name \\"Budget Code Name\\"  FROM ref_budget_code ORDER BY attribute_name',
        'budget_expense_category_code_list' => 'SELECT distinct object_class_code \\"Expense Category Code\\",object_class_name \\"Expense Category Name\\"  FROM ref_object_class ORDER BY object_class_name',

        // Revenue:
        'revenue_class_code_list' => 'SELECT distinct revenue_class_code \\"Revneue Class Code\\",revenue_class_name \\"Revneue Class Name\\"  FROM ref_revenue_class ORDER BY revenue_class_name',
        'fund_class_code_list' => 'SELECT distinct fund_class_code \\"Fund Class Code\\",fund_class_name \\"Fund Class Name\\"  FROM ref_fund_class where fund_class_name = \'General Fund\' ORDER BY fund_class_name',
        'funding_source_code_list' => 'SELECT distinct funding_class_code \\"Funding Class Code\\",funding_class_name \\"Funding Class Name\\"  FROM ref_funding_class ORDER BY funding_class_name',
        'revenue_category_code_list' => 'SELECT distinct revenue_category_code \\"Revenue Category Code\\",revenue_category_name \\"Revenue Category Name\\"  FROM ref_revenue_category ORDER BY revenue_category_name',
        'revenue_source_code_list' => 'SELECT distinct revenue_source_code \\"Revenue Source Code\\",revenue_source_name \\"Revenue Source Name\\"  FROM ref_revenue_source ORDER BY revenue_source_name',

        // Spending:
        'payee_code_list' => 'SELECT vendor_customer_code \\"Payee Code\\",legal_name \\"Payee Name\\"  FROM vendor ORDER BY legal_name',
        'expense_code_list' => 'SELECT DISTINCT document_id \\"Expense Id\\"  FROM history_master_agreement ORDER BY document_id',
        'spending_expense_category_code_list' => 'SELECT DISTINCT expenditure_object_code \\"Expense Category Code\\",expenditure_object_name \\"Expense Catergory Name\\" FROM ref_expenditure_object ORDER BY expenditure_object_name',
        'capital_project_code_list' => 'SELECT DISTINCT reporting_code \\"Capital Project Code\\" FROM disbursement_line_item_details where coalesce(reporting_code,\'\') <> \'\' ORDER BY reporting_code',
        'document_id_code_list' => 'SELECT DISTINCT disbursement_number \\"Document Id\\" FROM disbursement_line_item_details ORDER BY disbursement_number',
        'spending_category_code_list' => 'SELECT DISTINCT spending_category_name \\"Spending Category Name\\", spending_category_code \\"Spending Category Code\\" FROM ref_spending_category',

    ];

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
    public $successSubject = 'No changes';

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

        $variable_name = 'checkbook_api_ref_files_last_run';

        $today_week = $this->get_date('Y-W');
        $current_hour = (int)$this->get_date('H');

        if (variable_get($variable_name) == $today_week) {
            //error_log("ETL STATUS MAIL CRON skips. Reason: already ran this week :: $today_week :: ".variable_get($variable_name));
            return false;
        }

        if ($current_hour < 9 || $current_hour > 10) {
            //error_log("ETL STATUS MAIL CRON skips. Reason: will run between 9 AM and 11 AM EST :: current hour: $current_hour");
            return false;
        }

        variable_set($variable_name, $today_week);
        return $this->sendmail();
    }

    /**
     * @return bool
     */
    public function sendmail()
    {
        global $conf;

        $to = $conf['checkbook_dev_group_email'];
        drupal_mail('checkbook_api_ref_files', "send-status", $to,
            null, [], 'checkbook@reisys.com', TRUE);
        return true;
    }

    public function generate_files()
    {
        global $conf, $databases;

        $return = [
            'error' => false,
            'files' => [],
        ];

        ini_set('max_execution_time',0);
        error_reporting(E_ALL);
        ini_set('display_errors', true);
        ini_set('display_startup_errors', true);

        $dir = variable_get('file_public_path', 'sites/default/files') . '/' . $conf['check_book']['data_feeds']['output_file_dir'];
        $dir = realpath($dir);
        if (!file_prepare_directory($dir, FILE_CREATE_DIRECTORY)) {
            $return['error'] = "Could not prepare directory $dir for generating reference data.";
            return $return;
        }
        /*if(!is_link($dir) && !@chmod($dir,0777)){
            LogHelper::log_error("Could not change permissions to 777 for $dir.");
            echo $failure;
            return;
        }*/

        $dir .= '/' . $conf['check_book']['ref_data_dir'];
        if (!file_prepare_directory($dir, FILE_CREATE_DIRECTORY)) {
            $return['error'] = "Could not prepare directory $dir for generating reference data.";
            return $return;
        }

        foreach(self::REF_DATA_QUERIES as $filename => $sql) {
            $file = $dir . '/' . $filename . '.csv';
            $file_info['error'] = false;
            $file_info['old_timestamp'] = filemtime($file);
            if ($file_info['old_timestamp']) {
                $file_info['old_timestamp'] = date('Y-m-d', $file_info['old_timestamp']);
            }
            $file_info['old_filesize'] = filesize($file);
            $file_new = $file.'.new';

            $command = $conf['check_book']['data_feeds']['command'];
            $command .= ' ' . $databases['checkbook']['main']['database'] . ' ';
            $command .= " -c \"\\\\COPY (" . $sql . ") TO '"
                . $file_new
                . "'  WITH DELIMITER ',' CSV HEADER QUOTE '\\\"' ESCAPE '\\\"' \" ";

            $file_info['command'] = $command;

            try{
                shell_exec($command);
                $file_info['new_filesize'] = filesize($file_new);
                $file_info['new_timestamp'] = filemtime($file_new);
                if ($file_info['new_filesize']) {
                    $file_info['new_timestamp'] = date('Y-m-d', $file_info['new_timestamp']);
                    if ($file_info['new_filesize'] !== $file_info['old_filesize']) {
                        if (rename($file_new, $file)) {
                            $file_info['updated'] = true;
                            if ('No changes' == $this->successSubject) {
                                $this->successSubject = 'Updated';
                            }
                        } else {
                            $file_info['error'] = 'Could not replace old file with new one: mv $file_new $file';
                            $this->successSubject = 'Fail';
                        }
                    } else {
                        $file_info['updated'] = false;
                    }
                }
            } catch(Exception $ex){
                $file_info['error'] = "Could not run sql command: $command Error:".$ex->getMessage();
                $this->successSubject = 'Fail';
            }

            $php_sql = str_replace('\\','',$sql);
            $file_info['sample'] = _checkbook_project_execute_sql($php_sql.' LIMIT 5');
            $return['files'][$filename] = $file_info;
        }
        return $return;
    }

    /**
     * @param $message
     * @return bool
     */
    public function mail(&$message)
    {
        $result = $this->generate_files();
        $message['body'] =
            [
                'status' => $this->successSubject,
                'error' => $result['error'],
                'files' => $result['files'],
            ];

        $date = $this->get_date('Y-m-d');

        $message['subject'] = 'API Ref Files: ' . $this->successSubject . " ($date)";

        return true;
    }
}
