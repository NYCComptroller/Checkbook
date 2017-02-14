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


namespace checkbook_api;

/**
 * Constants for the CheckbookAPI
 *
 * Class ResponseStatus
 * @package checkbook_api
 */
class ResponseStatus {
    public static $SUCCEEDED = 1;
    public static $INVALID = 2;
    public static $FAILED = 3;
}

class CheckbookAPIService {

    private $apiRepository;

    function __construct()
    {
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
     * @param $host_name
     * @param string $response_log
     * @return mixed
     */
    public function createCheckbookAPI($domain, $request_xml, $client_ip, $client_location, $host_name, $response_log = '') {

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
            'host_name' => $host_name,
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

