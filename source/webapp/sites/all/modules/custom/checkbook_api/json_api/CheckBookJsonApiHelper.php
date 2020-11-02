<?php

namespace checkbook_json_api;

use \Exception;

/**
 * Class CheckBookJsonApiHelper
 * @package checkbook_json_api
 *
 */
class CheckBookJsonApiHelper
{
    /**
     * @var CheckBookJsonApi
     */
    private $JsonApi;

    /**
     * @var int
     */
    public $timeNow;

    /**
     * @var
     */
    public $dataSourceLastSuccess;

    /**
     * CheckBookJsonApiHelper constructor.
     * @param $jsonApi
     */
    public function __construct($jsonApi)
    {
        $this->JsonApi = $jsonApi;
        $this->timeNow = time();
    }


    /**
     * @param $args
     * @return bool|false|int|string
     */
    public function validate_year($args)
    {
        $year = !empty($args[1]) ? $args[1] : false;
        $year = $year ?: date('Y');
        $year = (is_numeric($year) && $year > 2009 && $year <= (int)date('Y')) ? $year : false;
        if (!$year) {
            $this->JsonApi->message = 'invalid year';
            $this->JsonApi->success = false;
            return false;
        }
        return $year;
    }

    /**
     * @param $year_type
     * @return string
     */
    public function get_verbal_year_type($year_type)
    {
        if ('c' == strtolower($year_type)) {
            return 'calendar';
        }
        return 'fiscal';
    }

    /**
     * @param $args
     * @param $default
     * @return string
     */
    public function validate_year_type($args, $default = 'B')
    {
        $year_type = !empty($args[2]) ? $args[2] : $default;
        switch (strtolower($year_type)) {
            case 'c':
            case 'calendar':
                return 'C';
            case 'b':
            case 'fiscal':
                return 'B';
            default:
                return $default;
        }
    }
}
