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


class CheckbookAPIRepository {

    /**
     * Function to select records from the checkbook_api table
     *
     * @param $api_id
     * @return mixed
     */
    public function getCheckbookAPI($api_id){
        $api = db_query(
            "SELECT
            api_id,
            domain,
            request_xml,
            response_status,
            response_log,
            client_ip,
            city,
            state,
            country,
            country_code,
            continent,
            continent_code,
            created_date
            FROM {checkbook_api}
            WHERE api_id = :api_id", array(':api_id' => $api_id));

        $api_results = $api->fetchObject();
        return $api_results;


    }

    /**
     * Function to insert records into the checkbook_api table
     *
     * @param CheckbookAPI $checkbookAPI
     * @return mixed
     */
    public function insertCheckbookAPI(CheckbookAPI $checkbookAPI) {
        // Remove any null properties
        $fields = array_filter((array) $checkbookAPI);
        $api_id = db_insert('checkbook_api')->fields($fields)->execute();
        return self::getCheckbookAPI($api_id);
    }

    /**
     * Function to update records in the checkbook_api table
     *
     * @param CheckbookAPI $checkbookAPI
     * @return \DatabaseStatementInterface
     */
    public function updateCheckbookAPI(CheckbookAPI $checkbookAPI) {
        // Remove any null properties
        $fields = array_filter((array) $checkbookAPI);

        $db_update_query =
            db_update("checkbook_api")
                ->fields($fields)
                ->condition('api_id', $checkbookAPI->api_id);
        $rows_affected = $db_update_query->execute();
        return $rows_affected;
    }
}

/**
 * Class CheckbookAPI holds the structure of the CheckbookAPI entity
 *
 * @package checkbook_api
 */
class CheckbookAPI {
    public $api_id;
    public $domain;
    public $request_xml;
    public $response_status;
    public $response_log;
    public $client_ip;
    public $city;
    public $state;
    public $country;
    public $country_code;
    public $continent;
    public $continent_code;
    public $created_date;

    function __construct(Array $properties=array())
    {
        foreach($properties as $key => $value){
            $this->{$key} = $value;
        }
    }

    function getPropertyNames() {
        $properties = array();
        foreach ($this as $name => $value) {
            $properties[] = $name;
        }
        return $properties;
    }
}