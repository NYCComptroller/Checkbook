<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
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
   */
  static function createNewQueueRequest($queue_request) {
    $job_query = db_insert('custom_queue_job')
      ->fields(array(
      'name' => $queue_request['name'],
      'request' => $queue_request['request'],
      'request_criteria' => $queue_request['request_criteria'],
      'user_criteria' => $queue_request['user_criteria'],
      'data_command' => $queue_request['data_command'],
      'created' => time(),
    ));

    $job_id = $job_query->execute();

    return self::createQueueRequest($queue_request['token'], $queue_request['email'], $job_id);
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
}
