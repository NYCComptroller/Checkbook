<?php
namespace Drupal\checkbook_api\API;

use Drupal\checkbook_api\Criteria\AbstractAPISearchCriteria;
use Drupal\checkbook_api\Handler\CSVDataHandler;
use Drupal\checkbook_api\Handler\XMLDataHandler;
use Drupal\checkbook_api\Queue\QueueUtil;
use Drupal\checkbook_log\LogHelper;
use Exception;

/**
 * Class to interact with API
 *
 * Provides interface to get data in CSV/XML formats.
 * Accepts requests to queue large dataSet.
 */
class CheckBookAPI {
  private $request_criteria;
  private $request_handler;

    /**
     * Constructor function.
     *
     * @param array $request_criteria
     *   Request criteria
     * @throws Exception
     */
  public function __construct($request_criteria) {
    // Increasing to handle memory limits when exporting.
    ini_set('memory_limit', '512M');

    $this->request_criteria = $request_criteria;
    $this->setRequestHandler();
  }

  /**
   * Sets request handler.
   *
   * @throws Exception
   */
  private function setRequestHandler() {
    $criteria = $this->request_criteria->getCriteria();
    $response_format = $criteria['global']['response_format'];
    switch ($response_format) {
      case "xml":
        $this->request_handler = new XMLDataHandler($this->request_criteria);
        break;

      case "csv":
        $this->request_handler = new CSVDataHandler($this->request_criteria);
        break;

      default:
        break;
    }

    if (!isset($this->request_handler)) {
      throw new Exception("Could not find handler for request ($response_format)");
    }
  }

  /**
   * Function to call to get data.
   *
   * @return array
   *   Data
   */
public function getData() {
    return $this->request_handler->execute();
  }

  /**
   * Function to get Record Count.
   *
   * @return int
   *   Record Count
   */
 public function getRecordCount() {
    return $this->request_handler->getRecordCount();
  }

    /**
    * Function to Submit a request to queue for proccessing.
    *
    * @param string $email
    *   Optional email id.
    *
    * @return mixed
    *   Token for tracking purpose.
    *
    * @throws Exception
    *   Throws Exception if invalid email is provided.
    */
   public function queueRequest($email) {
        //Validate email
        $email = empty($email) ? null : $email;
        if (isset($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $msg = "Invalid email address {$email} is provided for queue request.";
            LogHelper::log_error($msg);
            throw new Exception($msg);
        }

        //Insert record into queue
        $token = $this->request_handler->queueRequest($email);
        $job_details = self::getRequestDetailsByToken($token);

        //Send confirmation email
        if(isset($email)) {
            $tracking_number = null;
            $params = array(
                "email" => $email,
                "tracking_number" => $token,
                "user_criteria" => $job_details['user_criteria']
            );
            self::sendConfirmationEmail($token,$email,$params);
        }
        return $job_details;
    }

    /**
     * Function to send the confirmation email for the data feed,
     * logs date in DB if success
     * @param $token
     * @param $email
     * @param $params
     * @throws Exception
     */
    public function sendConfirmationEmail($token,$email,$params) {
        $response = \Drupal::service('plugin.manager.mail')->mail('checkbook_datafeeds', "confirmation_notification", $email, null, $params);

        if(!$response['result']) {
            $msg = "Error sending email in class CheckBookAPI, function sendConfirmationEmail()";
            LogHelper::log_error($msg);
            throw new Exception($msg);
        }
        else {
            //Update custom_queue_request, set confirmation_mail_sent_date
            $rows_affected = QueueUtil::updateConfirmationMailSentDate($token);
            if($rows_affected != 1) {
                $msg = "Error sending email in class CheckBookAPI, function sendConfirmationEmail()";
                LogHelper::log_error($msg);
                throw new Exception($msg);
            }
        }
    }

    /**
     * Function to Submit a request to queue for immediate download.
     *
     * @return mixed
     *   Token for tracking purpose.
     *
     */
   public function queueImmediateRequest() {
        $token = $this->request_handler->queueImmediateRequest();
        return self::getRequestDetailsByToken($token);
    }

  /**
   * Function to validate the request criteria and indicate if errors are present.
   *
   * @return bool
   *   true indicates no validation errors else false.
   */
  public function validateRequest() {
    return $this->request_handler->validateRequest();
  }

  /**
   * Function to get response from API.
   *
   * @return mixed
   *   Response.
   */
  public function getErrorResponse() {
    return $this->request_handler->getErrorResponse();
  }

  /**
   * Function to track queue request details.
   *
   * @static
   *
   * @param string $token
   *   Track token
   *
   * @return array|null
   *   Request Details
   */
  public static function getRequestDetailsByToken($token) {
    if (empty($token)) {
      return NULL;
    }

    $token = trim($token);

    $job_details = QueueUtil::getRequestDetailsByToken($token);

    if (isset($job_details['file_name'])) {
      $dir = \Drupal::state()->get('file_public_path','sites/default/files')
        . '/' . \Drupal::config('check_book')->get('data_feeds')['output_file_dir'];


      $file = $dir . '/' . $job_details['file_name'];
      if (is_file($file)) {
        $job_details['file_path'] = $file;
      }

      if (is_file($file . '.zip')) {
        $job_details['compressed_file_path'] = $file . '.zip';
      }
    }

    return $job_details;
  }
    /**
     * Function to call to get data.
     *
     * @return array
     *   Data
     */
    public function generateFile() {
        return $this->request_handler->generateFile();
    }

    public function outputFile($fileName){
        return $this->request_handler->outputFile($fileName);
    }
}
