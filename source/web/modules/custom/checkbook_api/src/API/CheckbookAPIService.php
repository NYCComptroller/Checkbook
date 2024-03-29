<?php
namespace Drupal\checkbook_api\API;

class CheckbookAPIService {

    private $apiRepository;

    public function __construct(){
        $this->apiRepository = new CheckbookAPIRepository();
    }

    /**
     * returns the CheckbookAPI entity from the CheckbookAPI repository
     *
     * @param $api_id
     * @return mixed
     */
    public function getCheckbookAPI($api_id) {
        $api = $this->apiRepository->getCheckbookAPI($api_id);
        return $api;
    }

    /**
     * Creates a CheckbookAPI entity using the CheckbookAPI repository
     * @param $domain
     * @param $request_xml
     * @param $client_ip
     * @param $client_location
     * @param string $response_log
     * @return mixed
     */
    public function createCheckbookAPI($domain, $request_xml, $client_ip, $client_location, string $response_log = '') {

        if(isset($client_location)) {
            $city = $client_location['city'];
            $state = $client_location['state'];
            $country = $client_location['country'];
            $country_code = $client_location['country_code'];
            $continent = $client_location['continent'];
            $continent_code = $client_location['continent_code'];
        }

        $api_properties = array(
            'domain' => $domain,
            'request_xml' => $request_xml,
            'response_log' => $response_log,
            'client_ip' => $client_ip,
            'city' => $city,
            'state' => $state,
            'country' => $country,
            'country_code' => $country_code,
            'continent' => $continent,
            'continent_code' => $continent_code,
            'created_date' => time(),
        );
        $api = new CheckbookAPIEntity($api_properties);
        return $this->apiRepository->insertCheckbookAPI($api);
    }

    /**
     * Uses the CheckbookAPI repository to update the status of the API record as well as the status log
     *
     * @param $api_id
     * @param $response_status
     * @param $response_log
     */
    public function logStatus($api_id, $response_status, $response_log) {
        $api_properties = array(
            'api_id' => $api_id,
            'response_status' => $response_status,
            'response_log' => $response_log,
        );
        $api = new CheckbookAPIEntity($api_properties);
        $this->apiRepository->updateCheckbookAPI($api);
    }
}
