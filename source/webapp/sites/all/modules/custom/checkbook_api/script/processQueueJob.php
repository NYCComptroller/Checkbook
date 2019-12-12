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
 * Script to handle process queue jobs.
 */
set_time_limit(0);

if (!defined('DRUPAL_ROOT')) {
    define('DRUPAL_ROOT', dirname(__DIR__, 6));
}

require_once DRUPAL_ROOT . '/includes/bootstrap.inc';
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);
//_drush_bootstrap_drupal_full();

define('MAXIMUM_JOBS_TO_RUN', 1);

TextLogMessageTrimmer::$LOGGED_TEXT_LENGTH__MAXIMUM = 512 * 10;
// Make sure data is not getting updated:
// TODO - This  will be handled by server scripts.
$log_id = date('mdYHis');

LogHelper::log_notice("$log_id: Cron invoked for processing next job in queue.");

if (!QueueUtil::isJobsInProgress()) {
  LogHelper::log_notice("$log_id: No job is in progress. Getting next job.");

  try {
    $job_details = QueueUtil::getNextJob();
  }
  catch (Exception $claim_exception) {
    LogHelper::log_error("$log_id: Error while fetching job from queue: " . $claim_exception);
    return;
  }

  if ($job_details) {
    try {
      $job_id = $job_details['job_id'];
      LogHelper::log_notice("$log_id: Fetched next job from queue for job id:" . $job_details['job_id']);

      LogHelper::log_notice("$log_id: Claiming job from queue:" . $job_id);
      $job_claimed = QueueUtil::claimJob($job_id);
      if ($job_claimed) {
        LogHelper::log_notice("$log_id: Job $job_id is claimed for processing.");
        LogHelper::log_notice("$log_id: Started processing job $job_id.");
        $queue_job = new QueueJob($job_details);
        $queue_job->setLogId($log_id);
        $queue_job->processJob();
        LogHelper::log_notice("$log_id: Completed processing job $job_id.");

        LogHelper::log_notice("$log_id: Started updating status 2 for job $job_id.");
        $job_details = array(
          'status' => 2,
          'end_time' => time(),
          'filename' => $queue_job->getFileName(),
        );
        $job_log = "~~$log_id: File generated Successfully on " . date("m-d-Y, H:i:s");

        LogHelper::log_notice("$log_id: jobId:" . $job_id);
        LogHelper::log_notice("$log_id: filename:" . $queue_job->getFileName());
        LogHelper::log_notice("$log_id: jobLog:" . $job_log);

        QueueUtil::updateJobDetails($job_id, $job_details, $job_log);
        LogHelper::log_notice("$log_id: Completed updating status 2 for job $job_id.");
      }
      else {
        // Do not expect this to occur.
        LogHelper::log_notice("$log_id: Could not claim the Job $job_id.");

        LogHelper::log_notice("$log_id: Started updating failed status(COULD NOT CLAIM THE JOB) 3 for job $job_id.");
        $job_details = array('status' => 3, 'end_time' => time());
        $job_log = "~~$log_id: COULD NOT CLAIM THE JOB on " . date("m-d-Y, H:i:s");
        QueueUtil::updateJobDetails($job_id, $job_details, $job_log);
        LogHelper::log_notice("$log_id: Completed updating failed status(COULD NOT CLAIM THE JOB) 3 for job $job_id.");
      }
    }
    catch (JobRecoveryException $jre) {
      LogHelper::log_error("$log_id: Job recoverable Exception occurred while processing job $job_id. Exception is " . $jre);

      LogHelper::log_notice("$log_id: Started recovering job to set status to 0 for job $job_id.");
      $job_details = array(
        'status' => 0,
        'start_time' => NULL,
        'end_time' => NULL,
      );
      $job_log = "~~$log_id: Job recovered for job $job_id on " . date("m-d-Y, H:i:s") . ". Exception is " . $jre->getMessage();
      QueueUtil::updateJobDetails($job_id, $job_details, $job_log);
      LogHelper::log_notice("$log_id: Completed recovering job and updated status to 0 for job $job_id for reprocessing.");
    }
    catch (Exception $exception) {
      LogHelper::log_error("$log_id: Error while processing queue job $job_id. Exception is " . $exception);

      LogHelper::log_notice("$log_id: Started updating failed status 3 for job $job_id.");
      $job_details = array('status' => 3, 'end_time' => time());
      $job_log = "~~$log_id: Error while processing queue job on " . date("m-d-Y, H:i:s") . ". Exception is " . $exception->getMessage();
      QueueUtil::updateJobDetails($job_id, $job_details, $job_log);
      LogHelper::log_notice("$log_id: Completed updating failed status 3 for job $job_id.");
    }
  }
  else {
    LogHelper::log_notice("$log_id: No requests are found for processing. Sleep until next job is available.");
  }

} else {
  LogHelper::log_notice("$log_id: Currently a job is in progress. Skipping processing next job until current job is finished.");
}

LogHelper::log_notice("$log_id: Completed process queue cron.");
