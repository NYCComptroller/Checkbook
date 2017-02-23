<?php
/**
 * Created by PhpStorm.
 * User: atorkelson
 * Date: 5/27/15
 * Time: 11:41 AM
 */

namespace checkbook_api;

/**
 * Class CheckbookAPIEntity holds the structure of the CheckbookAPI entity
 *
 * @package checkbook_api
 */
class CheckbookAPIEntity {
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