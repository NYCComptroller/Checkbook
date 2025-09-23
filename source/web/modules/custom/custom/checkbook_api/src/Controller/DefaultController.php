<?php /**
 * @file
 * Contains \Drupal\checkbook_api\Controller\DefaultController.
 */

namespace Drupal\checkbook_api\Controller;

use Symfony\Component\HttpFoundation\Response;
use DOMDocument;
use Drupal\checkbook_api\API\CheckBookAPI;
use Drupal\checkbook_api\API\CheckbookAPIService;
use Drupal\checkbook_api\API\ResponseStatus;
use Drupal\checkbook_api\Criteria\XMLSearchCriteria;
use Drupal\checkbook_api\JsonAPI\CheckBookJsonApi;
use Drupal\Core\Controller\ControllerBase;
use Exception;
use Drupal\checkbook_log\LogHelper;

/**
 * Default controller for the checkbook_api module.
 */
class DefaultController extends ControllerBase {

  public function checkbook_json_api() {
    header('Content-Type: application/json');
    try {
      $args = func_get_args();
      $endpoint = isset($args[0]) ? $args[0] : 'index';
      $json_api = new CheckBookJsonApi($args);

      if ('index' == $endpoint) {
        $methods = get_class_methods($json_api);
        array_shift($methods);
        //      foreach ($methods as &$method) {
        //        $method = '/json_api/'.$method.'/';
        //      }
        echo json_encode($methods);
        return;
      }

      if (method_exists($json_api, $endpoint)) {
        echo json_encode($json_api->$endpoint());
      }
      else {
        throw new Exception('not implemented');
      }
    }

      catch (Exception $exception) {
      echo json_encode([
        'success' => FALSE,
        'message' => $exception->getMessage(),
      ]);
    }
  }

  public function checkbook_api() {
    $document = new DOMDocument();
    $document->preserveWhiteSpace = FALSE;
    $document->load('php://input');

    $search_criteria = new XMLSearchCriteria($document);
    $domain = $search_criteria->getTypeOfData();
    $request_xml = $document->saveXML();
    $client_ip = $_SERVER['HTTP_X_FORWARDED_F OR'] ?? ($_SERVER['REMOTE_ADDR'] ?? null);
    $client_location = $this->checkbook_api_get_ip_info($client_ip, "Location");
    $checkbook_api_service = new CheckbookAPIService();
    $api = $checkbook_api_service->createCheckbookAPI($domain, $request_xml, $client_ip, $client_location);
    $response_status = ResponseStatus::$SUCCEEDED;
    $response_log = null;
    $response = new Response();
    try {
      $checkbook_api = new CheckBookAPI($search_criteria);
      $response->headers->set("Content-Type", "application/xml");
      //drupal_add_http_header("Content-Type", "application/xml");

      if (isset($checkbook_api)) {
        if ($checkbook_api->validateRequest()) {
          $data = $checkbook_api->getData();
          $response_status = ResponseStatus::$SUCCEEDED;
          $response_log = "Request validated and response succeeded";
          $results = $data;
        }
        else {
          $error = $checkbook_api->getErrorResponse();
          $response_status = ResponseStatus::$INVALID;
          $response_log = "Response failed due to invalid request with error: " . $error;
          $results = $error;
        }
      }
    }
      catch (Exception $e) {
      $error = $e->getMessage();
      $response_status = ResponseStatus::$FAILED;
      $response_log = "Response failed with error: " . $error;
      $results = $error;
    }

    $checkbook_api_service->logStatus($api->api_id, $response_status, $response_log);
    $response->setContent($results);
    return $response;
  }

  /**
   * Using the IP address, returns location details
   *
   * @param null $ip
   * @param string $purpose
   * @param bool $deep_detect
   * @return array|null|string
   */
  public function checkbook_api_get_ip_info($ip = NULL, $purpose = "location", $deep_detect = TRUE) {
    $output = NULL;
    $ip = str_replace(array("\n", "\r"), '', $ip);
    if (filter_var($ip, FILTER_VALIDATE_IP) === FALSE) {
      $ip = $_SERVER["REMOTE_ADDR"];
      if ($deep_detect) {
        if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP))
          $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP))
          $ip = $_SERVER['HTTP_CLIENT_IP'];
      }
    }
    $purpose    = str_replace(array("name", "\n", "\t", " ", "-", "_"), NULL, strtolower(trim($purpose)));
    $support    = array("country", "countrycode", "state", "region", "city", "location", "address");
    $continents = array(
      "AF" => "Africa",
      "AN" => "Antarctica",
      "AS" => "Asia",
      "EU" => "Europe",
      "OC" => "Australia (Oceania)",
      "NA" => "North America",
      "SA" => "South America"
    );
    //LogHelper::log_info(\Drupal::service('smart_ip.smart_ip_location'));
    if (filter_var($ip, FILTER_VALIDATE_IP) && in_array($purpose, $support)) {
      $ipdat = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip));
      if (@strlen(trim($ipdat->geoplugin_countryCode)) == 2) {
        switch ($purpose) {
          case "location":
            $output = array(
              "city"           => @$ipdat->geoplugin_city,
              "state"          => @$ipdat->geoplugin_regionName,
              "country"        => @$ipdat->geoplugin_countryName,
              "country_code"   => @$ipdat->geoplugin_countryCode,
              "continent"      => @$continents[strtoupper($ipdat->geoplugin_continentCode)],
              "continent_code" => @$ipdat->geoplugin_continentCode
            );
            break;
          case "address":
            $address = array($ipdat->geoplugin_countryName);
            if (@strlen($ipdat->geoplugin_regionName) >= 1)
              $address[] = $ipdat->geoplugin_regionName;
            if (@strlen($ipdat->geoplugin_city) >= 1)
              $address[] = $ipdat->geoplugin_city;
            $output = implode(", ", array_reverse($address));
            break;
          case "city":
            $output = @$ipdat->geoplugin_city;
            break;
          case "region":
          case "state":
            $output = @$ipdat->geoplugin_regionName;
            break;
          case "country":
            $output = @$ipdat->geoplugin_countryName;
            break;
          case "countrycode":
            $output = @$ipdat->geoplugin_countryCode;
            break;
        }
      }
    }
    return $output;
  }

}
