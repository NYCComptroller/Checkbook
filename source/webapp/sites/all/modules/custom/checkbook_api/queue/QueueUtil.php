<?php
/**
* This file is part of the Checkbook NYC financial transparency software.
* 
* Copyright (C) 2012, 2013 New York City
* 
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU Affero General Public License as
* published by the Free Software Foundation, either version 3 of the
* License, or (at your option) any later version.
* 
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU Affero General Public License for more details.
* 
* You should have received a copy of the GNU Affero General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/


/**
 * Class to persist & fetch queue jobs
 */
class QueueUtil {

  /**
   * @static
   * @param $email
   * @param $request
   * @return array|null
   */
  static function searchQueue($email, $request) {
    $result = NULL;

    // Check if a job exists for the request:
    $job_sql = "SELECT qr.job_id
                 FROM custom_queue_request qr
                  JOIN custom_queue_job job on qr.job_id = job.job_id
                 AND job.request = :request
                 AND date(from_unixtime(job.created)) = curdate()
                 ORDER BY job.created DESC LIMIT 1";
    $job_id = db_query($job_sql, array(':request' => $request))->fetchField(0);
    if ($job_id) {
      $result['job_id'] = $job_id;
    }
    else {
      return $result;
    }

    if (isset($email)) {
      $token_sql = "SELECT qr.token
                 FROM custom_queue_request qr
                  JOIN custom_queue_job job on qr.job_id = job.job_id
                 WHERE qr.contact_email = :email
                 AND job.request = :request
                 AND date(from_unixtime(job.created)) = curdate()
                 ORDER BY job.created DESC LIMIT 1";

      $token = db_query($token_sql, array(':email' => $email, ':request' => $request))->fetchField(0);
      if ($token) {
        $result['token'] = $token;
      }
    }
    else {
      $token_sql = "SELECT qr.token
                     FROM custom_queue_request qr
                      JOIN custom_queue_job job on qr.job_id = job.job_id
                     WHERE qr.contact_email IS NULL
                     AND job.request = :request
                     AND date(from_unixtime(job.created)) = curdate()
                     ORDER BY job.created DESC LIMIT 1";

      $token = db_query($token_sql, array(':request' => $request))->fetchField(0);

      if ($token) {
        $result['token'] = $token;
      }
    }

    return $result;
  }

    /**
     * @static
     * @param $token
     * @param $email
     * @param $job_id
     * @return DatabaseStatementInterface|int|null
     * @throws Exception
     */
  static function createQueueRequest($token, $email, $job_id) {
    $fields = array(
      'token' => $token,
      'job_id' => $job_id,
      'created' => time(),
    );

    if (isset($email)) {
      $fields['contact_email'] = $email;
    }

    return db_insert('custom_queue_request')->fields($fields)->execute();
  }


    /**
     * @static
     * @param $queue_request
     * @return DatabaseStatementInterface|int|null
     * @throws Exception
     */
  static function createNewQueueRequest($queue_request) {
      $input_parameters = array(
          'name' => $queue_request['name'],
          'request' => $queue_request['request'],
          'request_criteria' => $queue_request['request_criteria'],
          'user_criteria' => $queue_request['user_criteria'],
          'data_command' => $queue_request['data_command'],
          'created' => time(),
          'last_update_date' => time(),
      );
      if(isset($queue_request['status'])) {
          $input_parameters['status'] = $queue_request['status'];
      }
    $job_query = db_insert('custom_queue_job')
      ->fields($input_parameters);

    $job_id = $job_query->execute();

    return self::createQueueRequest($queue_request['token'], $queue_request['email'], $job_id);
  }

    /**
     * @static
     * @param $queue_request
     * @return DatabaseStatementInterface|int|null
     * @throws Exception
     */
    static function createImmediateNewQueueRequest($queue_request) {
        $input_parameters = array(
            'name' => $queue_request['name'],
            'request' => $queue_request['request'],
            'request_criteria' => $queue_request['request_criteria'],
            'user_criteria' => $queue_request['user_criteria'],
            'data_command' => $queue_request['data_command'],
            'created' => time(),
            'last_update_date' => time(),
        );
        if(isset($queue_request['status'])) {
            $input_parameters['status'] = $queue_request['status'];
        }
        $job_query = db_insert('custom_queue_job')
            ->fields($input_parameters);

        $job_id = $job_query->execute();

        return self::createImmediateQueueRequest($queue_request['token'], $queue_request['email'], $job_id);
    }

    /**
     * @static
     * @param $token
     * @param $email
     * @param $job_id
     * @return DatabaseStatementInterface|int|null
     * @throws Exception
     */
    static function createImmediateQueueRequest($token, $email, $job_id) {
        $fields = array(
            'token' => $token,
            'job_id' => $job_id,
            'created' => time(),
            'download_count' => 1,
        );

        if (isset($email)) {
            $fields['contact_email'] = $email;
        }

        return db_insert('custom_queue_request')->fields($fields)->execute();
    }

  /**
   * @static
   * @return array
   */
  static function getNextJob() {
    $job_details = db_query('SELECT job_id, name, data_command, request_criteria FROM {custom_queue_job} WHERE status = 0 ORDER BY created ASC LIMIT 1', array())->fetchAssoc();
    if (isset($job_details['request_criteria'])) {
      $job_details['request_criteria'] = json_decode($job_details['request_criteria'], TRUE);
    }

    return $job_details;
  }

  /**
   * @static
   * @param $job_id
   * @return DatabaseStatementInterface
   */
  static function claimJob($job_id) {
    $db_update_query = db_update("custom_queue_job")->fields(array(
      'status' => 1,
      'start_time' => time(),
    ))
      ->expression("generation_status", "CONCAT(generation_status,'~~Job Claimed on " . date("m-d-Y, H:i:s") . "')")
      ->condition('job_id', $job_id)
      ->condition('status', 0);

    $rows_affected = $db_update_query->execute();

    return $rows_affected;
  }

    /**
     * Function updates the last_update_date of the existing job
     *
     * @param $queue_search_results
     */
    static function updateJobTimestamp($queue_search_results) {
        $log_id = date('mdYHis');
        $job_log = "~~$log_id: Updated Job last_update_date " . date("m-d-Y, H:i:s");
        $job_details = array(
            'last_update_date' => time(),
        );
        self::updateJobDetails($queue_search_results['job_id'], $job_details, $job_log);
    }

  static function updateJobDetails($job_id, $job_details, $job_log) {

    if (empty($job_details) && empty($job_log)) {
      LogHelper::log_notice('QueueUtil::updateJobDetails invoked without values.');
      return NULL;
    }

    $update_query = db_update("custom_queue_job");

    if (!empty($job_details)) {
      $fields = array();
      foreach ($job_details as $column => $value) {
        $fields[$column] = $value;
      }

      $update_query->fields($fields);
    }

    if (!empty($job_log)) {
      $expression = "CONCAT(IFNULL(generation_status,:startValue),:newValue)";
      $expression_args = array(':startValue' => '', ':newValue' => $job_log);
      $update_query->expression("generation_status", $expression, $expression_args);
    }

    $update_query->condition('job_id', $job_id);

    $rows_affected = $update_query->execute();

    return $rows_affected;
  }

  /**
   * @static
   * @return bool
   */
  static function isJobsInProgress() {
    $record_count = db_query('SELECT count(*) recordCount FROM {custom_queue_job} WHERE status = 1', array())->fetchField(0);

    if ($record_count >= MAXIMUM_JOBS_TO_RUN) {
      return TRUE;
    }

    return FALSE;
  }

  /**
   * @static
   * @param $token
   * @return array
   */
  static function getRequestDetailsByToken($token) {
    $sql = "SELECT qr.token, job.status, job.filename As file_name, job.user_criteria,
                CASE WHEN job.end_time > 0 THEN from_unixtime(job.end_time) END As file_generated_time
                 FROM custom_queue_request qr
                  JOIN custom_queue_job job on qr.job_id = job.job_id
                 AND qr.token = :token LIMIT 1";

    $job_details = db_query($sql, array(':token' => $token))->fetchAssoc();

    if (isset($job_details['user_criteria'])) {
      $job_details['user_criteria'] = json_decode($job_details['user_criteria'], TRUE);
    }

    return $job_details;
  }

  /**
   * @static
   * @return array
   */
  static function getPendingEmailsInfo() {
    $sql = "select
			r.rid, j.filename, r.token, j.end_time, r.contact_email
			from 
			custom_queue_job j , custom_queue_request r
			where r.contact_email IS NOT NULL
			AND j.status =2
			and r.sent_email = 'N'
			and r.job_id = j.job_id";

    $result = db_query($sql);
    $requests = array();
    $i = 0;
    foreach ($result as $record) {
      $requests[$i] = array(
        "filename" => $record->filename,
        "end_time" => $record->end_time,
        "contact_email" => $record->contact_email,
        "token" => $record->token,
        "rid" => $record->rid,
      );
      $i++;
    }
    return $requests;
  }

  /**
   * @static
   * @param $token
   * @return array
   */
  static function getPendingEmailInfo($token) {
    $sql = "select
			r.rid, j.filename, r.token, j.end_time, r.contact_email
			from
			custom_queue_job j , custom_queue_request r
			where r.token = '" . $token . "'
			and r.job_id = j.job_id";

    $result = db_query($sql);
    $requests = array();
    $i = 0;
    foreach ($result as $record) {
      $requests[$i] = array(
        "filename" => $record->filename,
        "end_time" => $record->end_time,
        "contact_email" => $record->contact_email,
        "token" => $record->token,
        "rid" => $record->rid,
      );
      $i++;
    }
    return $requests;
  }

  /**
   * @static
   * @param $request_id
   * @return DatabaseStatementInterface
   */
  static function updateJobRequestEmailStatus($request_id) {
    $db_update_query = db_update("custom_queue_request")->fields(array(
      'sent_email' => 'Y',
    ))
      ->condition('rid', $request_id);
    $rows_affected = $db_update_query->execute();
    return $rows_affected;
  }

    /**
     * @param $token
     * @return DatabaseStatementInterface
     */
    static function incrementDownloadCount($token) {
        $sql = "SELECT job_id,download_count
                FROM custom_queue_request
                WHERE token = :token";
        $job_details = db_query($sql, array(':token' => $token))->fetchAssoc();

        if (isset($job_details['download_count']) && isset($job_details['job_id'])) {
            $job_id = $job_details['job_id'];
            $download_count = $job_details['download_count'] + 1;
            $db_update_query = db_update("custom_queue_request")->fields(array(
                'download_count' => $download_count,
            ))
                ->condition('job_id', $job_id)
                ->condition('token', $token);
            $rows_affected = $db_update_query->execute();
        }

        return $rows_affected;
    }

    /**
     * Function will update the confirmation_mail_sent_date in the
     * custom_queue_request table with current timestamp
     * @param $token
     * @return DatabaseStatementInterface
     */
    static function updateConfirmationMailSentDate($token) {
        $db_update_query = db_update("custom_queue_request")
            ->fields(array('confirmation_mail_sent_date' => time()))
            ->condition('token', $token);
        $rows_affected = $db_update_query->execute();
        return $rows_affected;
    }
}
