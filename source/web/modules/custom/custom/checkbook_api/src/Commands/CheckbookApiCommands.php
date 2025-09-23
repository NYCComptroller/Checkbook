<?php

namespace Drupal\checkbook_api\Commands;

use Drupal\checkbook_api\Queue\QueueJob;
use Drupal\checkbook_log\LogHelper;
use Drupal\data_controller_log\TextLogMessageTrimmer;
use Drush\Commands\DrushCommands;
use Drupal\checkbook_api\Queue\JobRecoveryException;
use Drupal\checkbook_api\Queue\QueueUtil;
use Exception;

/**
 * A Drush commandfile.
 *
 * In addition to this file, you need a drush.services.yml
 * in root of your module, and a composer.json file that provides the name
 * of the services file to use.
 *
 * See these files for an example of injecting Drupal services:
 *   - http://cgit.drupalcode.org/devel/tree/src/Commands/DevelCommands.php
 *   - http://cgit.drupalcode.org/devel/tree/drush.services.yml
 */
class CheckbookApiCommands extends DrushCommands {

  /**
   * Processes job that are in queue for large file downlaods for datafeeds.
   *
   * @usage checkbook_api:processQueueJob
   *   Processes job that are in queue for large file downlaods for datafeeds
   *
   * @command checkbook_api:processQueueJob
   * @aliases processQueueJob
   */
  public function processQueueJob() {
    set_time_limit(0);
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
  }

  /**
   * Sends email confirmation for jobs that have been processes by checkbook_api:processQueueJob
   *
   * @usage checkbook_api:sendFeedCompletionEmails
   *   sends email confirmation for jobs that have been processes by checkbook_api:processQueueJob
   *
   * @command checkbook_api:sendFeedCompletionEmails
   * @aliases sendFeedCompletionEmails
   */
  public function sendFeedCompletionEmails() {
    try{
      $completedJobRequests = QueueUtil::getPendingEmailsInfo();
      //LogHelper::log_debug( $completedJobRequests );
      LogHelper::log_debug( '<pre><code>' . print_r($completedJobRequests, TRUE) . '</code></pre>' );

    }catch(Exception $claimException){
      LogHelper::log_debug("Error while fetching job from queue: ". $claimException);
      return;
    }

    foreach($completedJobRequests as $request){
      //LogHelper::log_info( $request );
      LogHelper::log_debug( '<pre><code>' . print_r($request, TRUE) . '</code></pre>' );
      try{
        $dir = \Drupal::state()->get('file_public_path','sites/default/files')
          . '/' . \Drupal::config('check_book')->get('data_feeds')['output_file_dir'];

        $file = $dir . '/' . $request['filename'];
        $token = $request['token'];
        $job_details = QueueUtil::getRequestDetailsByToken($token);
        switch ($job_details['status']) {
          case 0:
            LogHelper::log_debug( 'File for token ' . $request['token'] . ' is still in queue to generate' );
            break;
          case 1:
            LogHelper::log_debug( 'File for token ' . $request['token'] . ' is being egnerated' );
            break;
          case 2:
            $params= array("download_url"=>$file
              ,"download_url_compressed"=>$file.'.zip'
              ,"expiration_date"=>date('d-M-Y',$request['end_time'] + 3600 * 24 * 7 )
              ,"contact_email"=>$request['contact_email']
              ,"tracking_num"=>$token
              ,"user_criteria" => $job_details['user_criteria']
            );
            //LogHelper::log_debug( $params );
            LogHelper::log_debug( '<pre><code>' . print_r($params, TRUE) . '</code></pre>' );
            $response = \Drupal::service('plugin.manager.mail')->mail('checkbook_datafeeds', "download_notification", $request['contact_email'], null, $params);
            //LogHelper::log_debug( $response );
            LogHelper::log_debug( '<pre><code>' . print_r($response, TRUE) . '</code></pre>' );
            if($response['result'] )
              QueueUtil::updateJobRequestEmailStatus($request['rid']);
            break;
          case 3:
            LogHelper::log_debug( 'File for token ' . $request['token'] . ' failed to generate' );
            break;
        }

      }catch(Exception $claimException){
        LogHelper::log_debug("Error while Sending Email Notification: ". $claimException . $params );
        return;
      }
    }
  }
}
