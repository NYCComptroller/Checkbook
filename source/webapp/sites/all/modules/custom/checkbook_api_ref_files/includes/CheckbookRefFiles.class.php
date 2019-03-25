<?php

/**
 * Class CheckbookRefFiles
 */
class CheckbookRefFiles
{
    /**
     * @var string
     */
    public $successSubject = 'No changes';

    /**
     *
     */
    const CRON_LAST_RUN_DRUPAL_VAR = 'checkbook_api_ref_files_last_run';

    /**
     * for easier phpUnit testing
     * @param $format
     * @return false|string
     */
    public function get_date($format)
    {
        return date($format, $this->timeNow());
    }

    /**
     * for easier phpUnit testing
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

        $today_week = $this->get_date('Y-W');
        $current_hour = (int)$this->get_date('H');

        if (variable_get(self::CRON_LAST_RUN_DRUPAL_VAR) == $today_week) {
            //error_log("ETL STATUS MAIL CRON skips. Reason: already ran this week :: $today_week :: ".variable_get($variable_name));
            return false;
        }

        if ($current_hour < 9 || $current_hour > 10) {
            //error_log("ETL STATUS MAIL CRON skips. Reason: will run between 9 AM and 11 AM EST :: current hour: $current_hour");
            return false;
        }

        variable_set(self::CRON_LAST_RUN_DRUPAL_VAR, $today_week);
        return $this->sendmail();
    }

    /**
     * @return bool
     */
    public function sendmail()
    {
        global $conf;

        $to = $conf['checkbook_dev_group_email'];
        $from = $conf['email_from'];
        drupal_mail('checkbook_api_ref_files', 'send-status', $to, null, [], $from, TRUE);

        return true;
    }

    /**
     * @return array
     */
    public function generate_files()
    {
        global $conf;

        $return = [
            'error' => false,
            'files' => [],
        ];

        ini_set('max_execution_time',60*60);

        $dir = variable_get('file_public_path', 'sites/default/files') . '/' . $conf['check_book']['data_feeds']['output_file_dir'];
        $dir = realpath($dir);
        if (!file_prepare_directory($dir, FILE_CREATE_DIRECTORY)) {
            $return['error'] = "Could not prepare directory $dir for generating reference data.";
            return $return;
        }

        $dir .= '/' . $conf['check_book']['ref_data_dir'];
        if (!file_prepare_directory($dir, FILE_CREATE_DIRECTORY)) {
            $return['error'] = "Could not prepare directory $dir for generating reference data.";
            return $return;
        }

        $ref_files_list = json_decode(file_get_contents(__DIR__.'/../config/ref_files_list.json'));

        foreach($ref_files_list as $filename => $ref_file) {
            if ($ref_file->disabled) {
                continue;
            }
            $file_info = [];
            $file = $dir . '/' . $filename . '.csv';
            $file_info['error'] = false;
            $file_info['old_timestamp'] = filemtime($file);
            if ($file_info['old_timestamp']) {
                $file_info['old_timestamp'] = date('Y-m-d', $file_info['old_timestamp']);
            }
            $file_info['old_filesize'] = filesize($file);
            $file_new = $file.'.new';

            $force_quote = '';
            if ($ref_file->force_quote??false) {
                $force_quote = ' FORCE QUOTE "'.join('","', $ref_file->force_quote).'"';
            }

            /**
             * RTFM https://gpdb.docs.pivotal.io/510/ref_guide/sql_commands/COPY.html
             */
            $psql_command = "COPY ({$ref_file->sql}) TO '{$file_new}' WITH CSV HEADER {$force_quote}";

            $db_name = 'main';
            $data_source = $ref_file->database??'checkbook';

            $command = _checkbook_psql_command($data_source);
            $command .= ' -c "\\';
            $command .= addcslashes($psql_command, '"').'"';

            $file_info['command'] = $command;
            LogHelper::log_notice('Generating ref file: '.$command);

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
                        $file_info['info'] = 'New file is same as old one, no changes needed';
                        $file_info['updated'] = false;
                        unlink($file_new);
                    }
                } else {
                    $file_info['warning'] = 'Newly generated file is zero byte size, keeping old file intact ';
                    unlink($file_new);
                }
            } catch(Exception $ex){
                $file_info['error'] = "Could not run sql command: $command Error:".$ex->getMessage();
                $this->successSubject = 'Fail';
            }

            $php_sql = str_replace('\\','',$ref_file->sql);
            $file_info['sample'] = _checkbook_project_execute_sql($php_sql.' LIMIT 5', $db_name, $data_source);
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
        global $conf;
        $result = $this->generate_files();
        $message['body'] =
            [
                'status' => $this->successSubject,
                'error' => $result['error'],
                'files' => $result['files'],
            ];

        $date = $this->get_date('Y-m-d');

        $message['subject'] = "[{$conf['CHECKBOOK_ENV']}] API Ref Files: " . $this->successSubject . " ($date)";

        return true;
    }
}
